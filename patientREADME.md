Lifeline Blood Bank - Patient Module

Patient confirmation creates a Pending reservation and does not decrease blood stock.
When Reservation.Approval changes from Pending to Approved, the database trigger deducts the requested bags from the selected hospital's stock. Rejected reservations do not change stock.

Patient files use the patient prefix. No Admin pages/controllers/models are included.
