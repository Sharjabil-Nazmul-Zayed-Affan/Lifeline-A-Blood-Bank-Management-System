<?php
// Model/patientBloodModel.php - Database operations for Blood and Blood_Bag entities

require_once __DIR__ . '/patientDb.php';

/**
 * Curated list of partner hospitals/blood banks used across the app so that
 * blood stock and reservations show varied, realistic hospital names and
 * locations instead of one repeated generic name.
 */
function getHospitalDirectory() {
    return [
        ['name' => 'Dhaka Medical College Hospital',        'location' => 'Dhaka'],
        ['name' => 'Square Hospitals Ltd.',                 'location' => 'Panthapath, Dhaka'],
        ['name' => 'Chittagong General Hospital',           'location' => 'Chattogram'],
        ['name' => 'Rajshahi Medical College Hospital',     'location' => 'Rajshahi'],
        ['name' => 'Sylhet MAG Osmani Medical College',     'location' => 'Sylhet'],
        ['name' => 'Khulna Medical College Hospital',       'location' => 'Khulna'],
        ['name' => 'Comilla Medical College Hospital',      'location' => 'Cumilla'],
        ['name' => 'Rangpur Medical College Hospital',      'location' => 'Rangpur'],
        ['name' => 'Apollo Hospitals Dhaka',                'location' => 'Bashundhara, Dhaka'],
        ['name' => 'Barisal Sher-e-Bangla Medical College', 'location' => 'Barishal'],
    ];
}

/**
 * Deterministically picks a hospital for a given numeric id, so the same
 * record always maps to the same hospital while different records get
 * different hospitals/locations.
 */
function getHospitalForId($id) {
    $hospitals = getHospitalDirectory();
    $index = ((int)$id) % count($hospitals);
    if ($index < 0) {
        $index += count($hospitals);
    }
    return $hospitals[$index];
}

/**
 * Looks up a hospital's location by its name from the directory.
 */
function getHospitalLocationByName($hospitalName) {
    foreach (getHospitalDirectory() as $hospital) {
        if ($hospital['name'] === $hospitalName) {
            return $hospital['location'];
        }
    }
    return '';
}

/**
 * Returns the list of hospitals that currently have at least one bag of
 * blood in stock, with their total bag count. Used to populate the
 * hospital selector on the Reserve Blood form.
 */
function getHospitalsWithStock() {
    $grouped = getAvailableBloodGroupedByHospital();
    $hospitals = [];
    foreach ($grouped as $h) {
        $hospitals[] = [
            'name'      => $h['hospitalName'],
            'location'  => $h['location'],
            'totalBags' => $h['totalBags']
        ];
    }
    return $hospitals;
}

/**
 * Total bags of a specific blood group available at a specific hospital.
 */
function getGroupQuantityAtHospital($hospitalName, $bloodGroup) {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT Blood_Bag_Id, Number_of_Bags FROM Blood_Bag WHERE Blood_Group = ? AND Number_of_Bags > 0 ORDER BY Blood_Bag_Id ASC");
    $stmt->execute([$bloodGroup]);
    $bags = $stmt->fetchAll();

    $total = 0;
    foreach ($bags as $bag) {
        $hospital = getHospitalForId($bag['Blood_Bag_Id']);
        if ($hospital['name'] === $hospitalName) {
            $total += (int)$bag['Number_of_Bags'];
        }
    }
    return $total;
}

/**
 * Deducts bags of a blood group specifically from bags mapped to the given
 * hospital (oldest bag id first), up to the requested quantity.
 */
function deductFromHospitalStock($hospitalName, $bloodGroup, $quantity) {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT Blood_Bag_Id, Number_of_Bags FROM Blood_Bag WHERE Blood_Group = ? AND Number_of_Bags > 0 ORDER BY Blood_Bag_Id ASC");
    $stmt->execute([$bloodGroup]);
    $bags = $stmt->fetchAll();

    $remaining = (int)$quantity;
    foreach ($bags as $bag) {
        if ($remaining <= 0) break;

        $hospital = getHospitalForId($bag['Blood_Bag_Id']);
        if ($hospital['name'] !== $hospitalName) {
            continue;
        }

        $bagId = (int)$bag['Blood_Bag_Id'];
        $avail = (int)$bag['Number_of_Bags'];
        $deduct = min($avail, $remaining);
        $remaining -= $deduct;

        $upd = $db->prepare("UPDATE Blood_Bag SET Number_of_Bags = Number_of_Bags - ? WHERE Blood_Bag_Id = ?");
        $upd->execute([$deduct, $bagId]);
    }
    syncSQLFile();
    return $remaining <= 0;
}

function getAllAvailableBlood() {
    $db = getDBConnection();
    $query = "SELECT bb.Blood_Bag_Id, bb.Blood_Group, bb.Number_of_Bags, bb.Date_Blood_Added, d.D_Name, d.D_Address 
              FROM Blood_Bag bb 
              LEFT JOIN Donor d ON bb.D_Username = d.D_Username 
              WHERE bb.Number_of_Bags > 0 
              ORDER BY bb.Blood_Bag_Id ASC";
    $stmt = $db->query($query);
    $rows = $stmt->fetchAll();
    
    $results = [];
    foreach ($rows as $row) {
        $hospital = getHospitalForId($row['Blood_Bag_Id']);
        $results[] = [
            'id'           => (string)$row['Blood_Bag_Id'],
            'bloodGroup'   => $row['Blood_Group'],
            'quantity'     => (int)$row['Number_of_Bags'],
            'hospitalName' => $hospital['name'],
            'location'     => $hospital['location']
        ];
    }
    return $results;
}

/**
 * Same underlying blood stock as getAllAvailableBlood()/searchBlood(), but
 * grouped by hospital so each hospital shows every blood group it has in
 * stock instead of one card per blood group.
 */
function getAvailableBloodGroupedByHospital($bloodGroup = '', $location = '') {
    $flatResults = empty($bloodGroup) && empty($location)
        ? getAllAvailableBlood()
        : searchBlood($bloodGroup, $location);

    $hospitals = [];
    foreach ($flatResults as $item) {
        $key = $item['hospitalName'] . '|' . $item['location'];
        if (!isset($hospitals[$key])) {
            $hospitals[$key] = [
                'hospitalName' => $item['hospitalName'],
                'location'     => $item['location'],
                'bloodGroups'  => [],
                'totalBags'    => 0
            ];
        }
        $hospitals[$key]['bloodGroups'][] = [
            'bloodGroup' => $item['bloodGroup'],
            'quantity'   => $item['quantity']
        ];
        $hospitals[$key]['totalBags'] += $item['quantity'];
    }

    return array_values($hospitals);
}

function getAvailableBloodGroupSummary() {
    $db = getDBConnection();
    $query = "SELECT b.Blood_Group, COALESCE(SUM(bb.Number_of_Bags), 0) AS Total_Bags 
              FROM Blood b 
              LEFT JOIN Blood_Bag bb ON b.Blood_Group = bb.Blood_Group 
              GROUP BY b.Blood_Group 
              ORDER BY b.Blood_Group ASC";
    $stmt = $db->query($query);
    return $stmt->fetchAll();
}

function getTotalBagsByBloodGroup($bloodGroup) {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT COALESCE(SUM(Number_of_Bags), 0) AS total FROM Blood_Bag WHERE Blood_Group = ?");
    $stmt->execute([$bloodGroup]);
    $row = $stmt->fetch();
    return $row ? (int)$row['total'] : 0;
}

function deductBloodByGroup($bloodGroup, $quantity) {
    $db = getDBConnection();
    // Deduct from available blood bags for this blood group starting from oldest
    $stmt = $db->prepare("SELECT Blood_Bag_Id, Number_of_Bags FROM Blood_Bag WHERE Blood_Group = ? AND Number_of_Bags > 0 ORDER BY Blood_Bag_Id ASC");
    $stmt->execute([$bloodGroup]);
    $bags = $stmt->fetchAll();

    $remaining = (int)$quantity;
    foreach ($bags as $bag) {
        if ($remaining <= 0) break;
        $bagId = (int)$bag['Blood_Bag_Id'];
        $avail = (int)$bag['Number_of_Bags'];

        if ($avail <= $remaining) {
            $deduct = $avail;
            $remaining -= $avail;
        } else {
            $deduct = $remaining;
            $remaining = 0;
        }

        $upd = $db->prepare("UPDATE Blood_Bag SET Number_of_Bags = Number_of_Bags - ? WHERE Blood_Bag_Id = ?");
        $upd->execute([$deduct, $bagId]);
    }
    syncSQLFile();
    return true;
}

function searchBlood($bloodGroup = '', $location = '') {
    $db = getDBConnection();
    $query = "SELECT bb.Blood_Bag_Id, bb.Blood_Group, bb.Number_of_Bags, bb.Date_Blood_Added 
              FROM Blood_Bag bb 
              WHERE bb.Number_of_Bags > 0";
    
    $params = [];
    if (!empty($bloodGroup)) {
        $query .= " AND bb.Blood_Group = ?";
        $params[] = $bloodGroup;
    }
    
    $query .= " ORDER BY bb.Blood_Bag_Id ASC";
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();
    
    $results = [];
    foreach ($rows as $row) {
        $hospital = getHospitalForId($row['Blood_Bag_Id']);

        // Filter by hospital name/location (what's actually shown to the user)
        if (!empty($location)) {
            $haystack = strtolower($hospital['name'] . ' ' . $hospital['location']);
            if (strpos($haystack, strtolower($location)) === false) {
                continue;
            }
        }

        $results[] = [
            'id'           => (string)$row['Blood_Bag_Id'],
            'bloodGroup'   => $row['Blood_Group'],
            'quantity'     => (int)$row['Number_of_Bags'],
            'hospitalName' => $hospital['name'],
            'location'     => $hospital['location']
        ];
    }
    return $results;
}

function getBloodById($bloodBagId) {
    $db = getDBConnection();
    $query = "SELECT bb.Blood_Bag_Id, bb.Blood_Group, bb.Number_of_Bags, bb.Date_Blood_Added, d.D_Name, d.D_Address 
              FROM Blood_Bag bb 
              LEFT JOIN Donor d ON bb.D_Username = d.D_Username 
              WHERE bb.Blood_Bag_Id = ? LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->execute([(int)$bloodBagId]);
    $row = $stmt->fetch();
    if (!$row) {
        return null;
    }
    $hospital = getHospitalForId($row['Blood_Bag_Id']);
    return [
        'id'           => (string)$row['Blood_Bag_Id'],
        'bloodGroup'   => $row['Blood_Group'],
        'quantity'     => (int)$row['Number_of_Bags'],
        'hospitalName' => $hospital['name'],
        'location'     => $hospital['location']
    ];
}

function deductBloodQuantity($bloodBagId, $quantity) {
    $db = getDBConnection();
    $stmt = $db->prepare("UPDATE Blood_Bag SET Number_of_Bags = GREATEST(0, Number_of_Bags - ?) WHERE Blood_Bag_Id = ?");
    $result = $stmt->execute([(int)$quantity, (int)$bloodBagId]);
    if ($result) {
        syncSQLFile();
    }
    return $result;
}
?>
