<?php
// Model/patientReservationModel.php - Database operations for Reservation entity

require_once __DIR__ . '/patientDb.php';
require_once __DIR__ . '/patientBloodModel.php';

/**
 * Resolves the hospital name/location for a reservation row. Uses the
 * actually-selected hospital stored on the row when present; falls back to
 * a deterministic pick (for reservations made before hospital selection
 * existed) so older rows still render sensibly.
 */
function resolveReservationHospital($row) {
    if (!empty($row['Hospital_Name'])) {
        return [
            'name'     => $row['Hospital_Name'],
            'location' => $row['Hospital_Location'] ?? getHospitalLocationByName($row['Hospital_Name'])
        ];
    }
    return getHospitalForId($row['Reserve_ID']);
}

function mapReservationRow($row) {
    $hospital = resolveReservationHospital($row);
    return [
        'reserveId'        => $row['Reserve_ID'],
        'username'         => $row['P_Username'],
        'approval'         => $row['Approval'] ?? 'Pending',
        'reservationTime'  => $row['Reservation_Time'],
        'neededDate'       => $row['Needed_Date'] ?? date('Y-m-d', strtotime($row['Reservation_Time'])),
        'bloodGroup'       => $row['Blood_Group'],
        'numberOfBags'     => $row['R_Number_of_Bags'],
        'hospitalName'     => $hospital['name'],
        'hospitalLocation' => $hospital['location']
    ];
}

function createReservation($username, $bloodGroup, $numberOfBags, $neededDate = null, $hospitalName = null, $hospitalLocation = null) {
    $db = getDBConnection();
    if (empty($neededDate)) {
        $neededDate = date('Y-m-d');
    }
    $stmt = $db->prepare("INSERT INTO Reservation (P_Username, Approval, Reservation_Time, Needed_Date, Blood_Group, R_Number_of_Bags, Hospital_Name, Hospital_Location) VALUES (?, 'Pending', NOW(), ?, ?, ?, ?, ?)");
    $stmt->execute([$username, $neededDate, $bloodGroup, (int)$numberOfBags, $hospitalName, $hospitalLocation]);
    $newId = (int)$db->lastInsertId();
    syncSQLFile();
    return $newId;
}

function getReservationsByPatient($username) {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT * FROM Reservation WHERE P_Username = ? ORDER BY Reservation_Time DESC");
    $stmt->execute([$username]);
    $rows = $stmt->fetchAll();

    $results = [];
    foreach ($rows as $row) {
        $results[] = mapReservationRow($row);
    }
    return $results;
}

function getReservationById($reserveId) {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT * FROM Reservation WHERE Reserve_ID = ? LIMIT 1");
    $stmt->execute([$reserveId]);
    $row = $stmt->fetch();
    if (!$row) {
        return null;
    }
    return mapReservationRow($row);
}

function countReservationsByPatient($username) {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT COUNT(*) AS total FROM Reservation WHERE P_Username = ?");
    $stmt->execute([$username]);
    $row = $stmt->fetch();
    return $row ? (int)$row['total'] : 0;
}

function countReservationsByStatus($username, $status) {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT COUNT(*) AS total FROM Reservation WHERE P_Username = ? AND LOWER(Approval) = LOWER(?)");
    $stmt->execute([$username, $status]);
    $row = $stmt->fetch();
    return $row ? (int)$row['total'] : 0;
}

function getRecentReservations($username, $limit = 5) {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT * FROM Reservation WHERE P_Username = ? ORDER BY Reservation_Time DESC LIMIT " . (int)$limit);
    $stmt->execute([$username]);
    $rows = $stmt->fetchAll();

    $results = [];
    foreach ($rows as $row) {
        $results[] = mapReservationRow($row);
    }
    return $results;
}

/**
 * Returns the single closest upcoming reservation (Needed_Date today or later).
 * Falls back to the most recently made reservation if none are upcoming.
 */
function getNextUpcomingReservation($username) {
    $db = getDBConnection();

    $stmt = $db->prepare("SELECT * FROM Reservation 
                          WHERE P_Username = ? AND COALESCE(Needed_Date, DATE(Reservation_Time)) >= CURDATE()
                          ORDER BY COALESCE(Needed_Date, DATE(Reservation_Time)) ASC 
                          LIMIT 1");
    $stmt->execute([$username]);
    $row = $stmt->fetch();

    if (!$row) {
        $stmt = $db->prepare("SELECT * FROM Reservation WHERE P_Username = ? ORDER BY Reservation_Time DESC LIMIT 1");
        $stmt->execute([$username]);
        $row = $stmt->fetch();
    }

    if (!$row) {
        return null;
    }

    return mapReservationRow($row);
}

function getUpcomingReservations($username, $limit = 5) {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT * FROM Reservation 
                          WHERE P_Username = ? 
                          ORDER BY COALESCE(Needed_Date, DATE(Reservation_Time)) ASC, Reservation_Time DESC 
                          LIMIT " . (int)$limit);
    $stmt->execute([$username]);
    $rows = $stmt->fetchAll();

    $results = [];
    foreach ($rows as $row) {
        $results[] = mapReservationRow($row);
    }
    return $results;
}
?>
