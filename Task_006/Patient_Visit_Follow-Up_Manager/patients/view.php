<?php
require("../config/db.php");
include("../includes/header.php"); 


$id = $_POST['id'];

/*
    VIEW PATIENT + FOLLOW-UP (SQL ONLY)
*/

$sql = "
SELECT 
    p.patient_id,
    p.name,
    p.dob,
    p.join_date,
    p.phone,
    p.address,

    
    TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) AS age_years,

   
    MAX(v.visit_date) AS last_visit,

   
    TIMESTAMPDIFF(DAY, MAX(v.visit_date), CURDATE()) AS days_since_last_visit,

    
    DATE_ADD(MAX(v.visit_date), INTERVAL 7 DAY) AS next_follow_up,

    
    CASE 
        WHEN MAX(v.visit_date) IS NULL THEN 'NO VISIT'
        WHEN DATE_ADD(MAX(v.visit_date), INTERVAL 7 DAY) < CURDATE()
        THEN 'OVERDUE'
        ELSE 'OK'
    END AS followup_status

FROM patient p

LEFT JOIN visits v 
ON p.patient_id = v.patient_id

WHERE p.patient_id = :id

GROUP BY p.patient_id
";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

$patient = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Patient View</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">

    <div class="card shadow p-4">

        <h3 class="text-center mb-4">Patient Details</h3>

        <div class="row">

            <div class="col-md-6">
                <p><b>ID:</b> <?= $patient['patient_id']; ?></p>
                <p><b>Name:</b> <?= $patient['name']; ?></p>
                <p><b>Age:</b> <?= $patient['age_years']; ?> Years</p>
                <p><b>DOB:</b> <?= $patient['dob']; ?></p>
                <p><b>Join Date:</b> <?= $patient['join_date']; ?></p>
            </div>

            <div class="col-md-6">
                <p><b>Phone:</b> <?= $patient['phone']; ?></p>
                <p><b>Address:</b> <?= $patient['address']; ?></p>

                <hr>

                <p><b>Last Visit:</b>
                    <?= $patient['last_visit'] ?? 'No Visit'; ?>
                </p>

                <p><b>Days Since Last Visit:</b>
                    <?= $patient['days_since_last_visit'] ?? 'N/A'; ?>
                </p>

                <p><b>Next Follow-up:</b>
                    <?= $patient['next_follow_up'] ?? 'N/A'; ?>
                </p>

                <p><b>Status:</b>
                    <?php if ($patient['followup_status'] == 'OVERDUE'): ?>
                        <span class="badge bg-danger">OVERDUE</span>

                    <?php elseif ($patient['followup_status'] == 'NO VISIT'): ?>
                        <span class="badge bg-secondary">NO VISIT</span>

                    <?php else: ?>
                        <span class="badge bg-success">OK</span>
                    <?php endif; ?>
                </p>

            </div>

        </div>

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