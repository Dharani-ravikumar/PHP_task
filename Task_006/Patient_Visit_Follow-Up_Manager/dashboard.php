<?php
session_start();
require("config/db.php");

if(!isset($_SESSION['user'])){
    header("Location: ../Access/login.php");
    exit;
}

/* ================= COUNTS ================= */


$patients = $conn->query("SELECT COUNT(*) AS total FROM patient")
->fetch(PDO::FETCH_ASSOC)['total'];

$visits = $conn->query("SELECT COUNT(*) AS total FROM visits")
->fetch(PDO::FETCH_ASSOC)['total'];

$overdue = $conn->query("
SELECT COUNT(*) AS total FROM visits
WHERE DATE_ADD(visit_date, INTERVAL 7 DAY) < CURDATE()
")->fetch(PDO::FETCH_ASSOC)['total'];

$upcoming = $conn->query("
    SELECT COUNT(*) AS total 
    FROM visits   
    WHERE follow_up_due >= CURDATE()
")->fetch(PDO::FETCH_ASSOC)['total'];

/* ================= MONTHLY ================= */

$chartData = $conn->query("
SELECT MONTHNAME(visit_date) AS month_name,
COUNT(*) AS total_visits
FROM visits
GROUP BY MONTH(visit_date)
ORDER BY MONTH(visit_date)
")->fetchAll(PDO::FETCH_ASSOC);

$months = [];
$totals = [];

foreach($chartData as $row){
    $months[] = $row['month_name'];
    $totals[] = $row['total_visits'];
}

?>

<!DOCTYPE html>
<html>

<head>

<title>Dashboard</title>

<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="assets/style.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>

<!-- ================= SIDEBAR ================= -->

<div class="sidebar">

    <h3>Hospital </h3>

    <a href="dashboard.php">Dashboard</a>
    <a href="./patients/list.php">Patients</a>
    <a href="./patients/add.php">Add Patient</a>
    <a href="./visits/list.php">Visits</a>
    <a href="./visits/add.php">Add Visit</a>

    <?php if($_SESSION['role'] == 'admin'): ?>
         <a href="./reports/summary.php">Summary</a>
        <a href="./reports/monthly.php">Monthly Reports</a>
        <a href="./reports/birthdays.php">Birthday Report</a>
        <a href="./Access/monthly_revenve.php">Revenue Report</a>
    <?php endif; ?>

    <a href="./Access/logout.php">Logout</a>

</div>

<!-- ================= MAIN ================= -->

<div class="main">

    <div class="d-flex justify-content-between mb-4">

        <h3>Dashboard</h3>

        <span class="badge bg-dark">
            <?= strtoupper($_SESSION['role']) ?>
        </span>

    </div>

    <!-- ================= CARDS ================= -->

    <div class="row">

        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white text-center p-3">
                <h6>Patients</h6>
                <h3><?= $patients ?></h3>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white text-center p-3">
                <h6>Visits</h6>
                <h3><?= $visits ?></h3>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card bg-danger text-white text-center p-3">
                <h6>Overdue</h6>
                <h3><?= $overdue ?></h3>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-dark text-center p-3">
                <h6>Upcoming</h6>
                <h3><?= $upcoming ?></h3>
            </div>
        </div>

    </div>

    <!-- ================= CHART ================= -->

    <div class="card mt-4 p-3 shadow">

        <h5 class="text-center">Monthly Visits</h5>

        <canvas id="chart"></canvas>

    </div>

</div>

<script>

new Chart(document.getElementById('chart'), {

    type: 'bar',

    data: {

        labels: <?= json_encode($months) ?>,

        datasets: [{

            label: 'Visits',

            data: <?= json_encode($totals) ?>,

            backgroundColor: '#0d6efd'

        }]

    },

    options: {

        responsive:true,

        scales:{
            y:{ beginAtZero:true }
        }

    }

});

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>