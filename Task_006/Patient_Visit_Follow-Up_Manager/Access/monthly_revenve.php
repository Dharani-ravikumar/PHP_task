<?php
require("../config/db.php");
session_start();

/* ================= SELECT YEAR ================= */

$selectedYear = $_GET['year'] ?? date('Y');

/* ================= GET AVAILABLE YEARS ================= */

$yearList = $conn->query("
    SELECT DISTINCT YEAR(visit_date) AS year
    FROM visits
    ORDER BY year DESC
")->fetchAll(PDO::FETCH_ASSOC);

/* ================= MONTHLY DATA FOR SELECTED YEAR ================= */

$sql = "
SELECT 
MONTH(visit_date) AS month_no,
MONTHNAME(visit_date) AS month_name,
SUM(consultation_fee) AS consultation,
SUM(lab_fee) AS lab
FROM visits
WHERE YEAR(visit_date) = ?
GROUP BY MONTH(visit_date)
ORDER BY MONTH(visit_date)
";

$stmt = $conn->prepare($sql);
$stmt->execute([$selectedYear]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$months = [];
$consultationArr = [];
$labArr = [];
$totalArr = [];

foreach($data as $row){

    $months[] = $row['month_name'];

    $consultationArr[] = $row['consultation'] ?? 0;
    $labArr[] = $row['lab'] ?? 0;

    $totalArr[] = ($row['consultation'] ?? 0) + ($row['lab'] ?? 0);
}

/* ================= TOTAL ================= */

$totalConsultation = array_sum($consultationArr);
$totalLab = array_sum($labArr);
$totalRevenue = $totalConsultation + $totalLab;

?>

<!DOCTYPE html>
<html>

<head>

<title>Monthly Revenue</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body class="bg-light">

<div class="container mt-5">

    <h2 class="text-center mb-4">📊 Monthly Revenue Report</h2>

    <!-- ================= YEAR FILTER ================= -->

    <form method="GET" class="text-center mb-4">

        <select name="year" class="form-select w-25 d-inline">

            <?php foreach($yearList as $y): ?>

                <option value="<?= $y['year'] ?>"
                    <?= ($selectedYear == $y['year']) ? 'selected' : '' ?>>

                    <?= $y['year'] ?>

                </option>

            <?php endforeach; ?>

        </select>

        <button class="btn btn-primary">Filter</button>

    </form>

    <!-- ================= SUMMARY CARDS ================= -->

    <div class="row">

        <div class="col-md-4 mb-3">

            <div class="card bg-primary text-white shadow">

                <div class="card-body text-center">

                    <h5>Consultation</h5>

                    <h3>₹ <?= number_format($totalConsultation,2) ?></h3>

                </div>

            </div>

        </div>

        <div class="col-md-4 mb-3">

            <div class="card bg-success text-white shadow">

                <div class="card-body text-center">

                    <h5>Lab</h5>

                    <h3>₹ <?= number_format($totalLab,2) ?></h3>

                </div>

            </div>

        </div>

        <div class="col-md-4 mb-3">

            <div class="card bg-dark text-white shadow">

                <div class="card-body text-center">

                    <h5>Total Revenue</h5>

                    <h3>₹ <?= number_format($totalRevenue,2) ?></h3>

                </div>

            </div>

        </div>

    </div>

    <!-- ================= TABLE ================= -->

    <div class="card shadow mt-4">

        <div class="card-body">

            <h4 class="mb-3">
                Monthly Report (<?= $selectedYear ?>)
            </h4>

            <table class="table table-bordered text-center">

                <thead class="table-dark">

                    <tr>
                        <th>Month</th>
                        <th>Consultation</th>
                        <th>Lab</th>
                        <th>Total</th>
                    </tr>

                </thead>

                <tbody>

                    <?php foreach($data as $row): ?>

                    <tr>

                        <td><?= $row['month_name'] ?></td>

                        <td>₹ <?= number_format($row['consultation'] ?? 0,2) ?></td>

                        <td>₹ <?= number_format($row['lab'] ?? 0,2) ?></td>

                        <td>
                            ₹ <?= number_format(
                                ($row['consultation'] ?? 0) + ($row['lab'] ?? 0),
                                2
                            ) ?>
                        </td>

                    </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

    <!-- ================= CHART ================= -->

    <div class="card shadow mt-4">

        <div class="card-body">

            <h4 class="text-center mb-3">
                Monthly Chart (<?= $selectedYear ?>)
            </h4>

            <canvas id="chart"></canvas>

        </div>

    </div>

</div>

<script>

const ctx = document.getElementById('chart');

new Chart(ctx, {

    type: 'bar',

    data: {

        labels: <?= json_encode($months) ?>,

        datasets: [

            {
                label: 'Consultation',
                data: <?= json_encode($consultationArr) ?>,
                backgroundColor: '#0d6efd'
            },

            {
                label: 'Lab',
                data: <?= json_encode($labArr) ?>,
                backgroundColor: '#198754'
            },

            {
                label: 'Total',
                data: <?= json_encode($totalArr) ?>,
                backgroundColor: '#dc3545'
            }

        ]

    },

    options: {

        responsive: true,

        plugins: {
            legend: { position: 'bottom' }
        },

        scales: {
            y: {
                beginAtZero: true
            }
        }

    }

});

</script>

</body>
</html>