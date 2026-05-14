<?php
require("../config/db.php");
include("../includes/header.php"); 
if (!isset($_POST['id'])) {
    die("No patient selected");
}

$id = $_POST['id'];

/*
    VISIT HISTORY FOR A PATIENT
    - Total visits
    - First visit
    - Last visit
    - Days between first and last visit
*/

$sql = "
SELECT 
    p.patient_id,
    p.name,

    COUNT(v.visit_id) AS total_visits,

    MIN(v.visit_date) AS first_visit,
    MAX(v.visit_date) AS last_visit,

    DATEDIFF(
        MAX(v.visit_date),
        MIN(v.visit_date)
    ) AS days_between_first_last

FROM patient p

LEFT JOIN visits v 
ON p.patient_id = v.patient_id

WHERE p.patient_id = :id

GROUP BY p.patient_id
";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

$data = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Visit History</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">

    <div class="card shadow p-4">

        <h3 class="text-center mb-4">Visit History</h3>

        <p><b>Patient ID:</b> <?= $data['patient_id']; ?></p>

        <p><b>Name:</b> <?= $data['name']; ?></p>

        <hr>

        <p><b>Total Visits:</b> <?= $data['total_visits']; ?></p>

        <p><b>First Visit:</b> 
            <?= $data['first_visit'] ?? 'No Visit'; ?>
        </p>

        <p><b>Last Visit:</b> 
            <?= $data['last_visit'] ?? 'No Visit'; ?>
        </p>

        <p><b>Days Between First & Last Visit:</b> 
            <?= $data['days_between_first_last'] ?? 'N/A'; ?>
        </p>

        <div class="text-center mt-3">
            <a href="list.php" class="btn btn-primary">Back</a>
        </div>

    </div>

</div>

</body>
</html>
<?php
include("../includes/footer.php");
?>