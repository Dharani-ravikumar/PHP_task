<?php
require("../config/db.php");
include("../includes/header.php"); 

$sql = "
SELECT 
    patient_id,
    name,
    dob,
    TIMESTAMPDIFF(YEAR, dob, CURDATE()) AS age,

    CASE 
        WHEN TIMESTAMPDIFF(YEAR, dob, CURDATE()) IN (39,49,59)
        THEN 'MILESTONE'
        ELSE 'NORMAL'
    END AS status

FROM patient

WHERE DATE_FORMAT(dob, '%m-%d') 
BETWEEN DATE_FORMAT(CURDATE(), '%m-%d') 
AND DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 30 DAY), '%m-%d')
";

$stmt = $conn->prepare($sql);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Birthday Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-4">

<h2 class="text-center mb-4">🎂 Birthday Report</h2>

<div class="card shadow">

<div class="card-body">

<table class="table table-bordered table-striped text-center mb-0">

<thead class="table-dark">
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>DOB</th>
    <th>Age</th>
    <th>Status</th>
</tr>
</thead>

<tbody>

<?php if(count($data) > 0): ?>

    <?php foreach($data as $d): ?>
    <tr>
        <td><?= $d['patient_id']; ?></td>
        <td><?= $d['name']; ?></td>
        <td><?= $d['dob']; ?></td>
        <td><?= $d['age']; ?></td>
        <td>
            <span class="badge bg-info">
                <?= $d['status']; ?>
            </span>
        </td>
    </tr>
    <?php endforeach; ?>

<?php else: ?>

    <tr>
        <td colspan="5" class="text-center text-muted py-4">
             No upcoming birthdays found
        </td>
    </tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

</div>

</body>
</html>

<?php include("../includes/footer.php"); ?>