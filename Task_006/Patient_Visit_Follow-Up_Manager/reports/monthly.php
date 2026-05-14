<?php
require("../config/db.php");
include("../includes/header.php"); 

$search = "";

if (isset($_POST['search'])) {
    $search = trim($_POST['search_text'] ?? "");
}

$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
if ($page < 1) $page = 1;

$limit = 5;
$offset = ($page - 1) * $limit;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Monthly Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-4">

<h2 class="text-center mb-4">Monthly Report</h2>

<!-- ================= SEARCH ================= -->
<form method="POST" class="d-flex justify-content-center mb-3 gap-2">

<input type="text" name="search_text" class="form-control w-25"
placeholder="Search Month (YYYY-MM)"
value="<?= htmlspecialchars($search) ?>">

<button type="submit" name="search" class="btn btn-success">Search</button>

</form>

<?php
$where = "";


if ($search != "") {
    $where = "WHERE DATE_FORMAT(v.visit_date,'%Y-%m') LIKE :search";
}

$countSql = "
SELECT COUNT(DISTINCT DATE_FORMAT(v.visit_date,'%Y-%m')) as total
FROM visits v
$where
";

$countStmt = $conn->prepare($countSql);

if ($search != "") {
    $like = "%$search%";
    $countStmt->bindParam(":search", $like);
}

$countStmt->execute();
$totalRows = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalRows / $limit);


$sql = "
SELECT 
DATE_FORMAT(v.visit_date, '%Y-%m') AS month,
COUNT(v.visit_id) AS total_visits,

(SELECT COUNT(*) 
 FROM patient p 
 WHERE DATE_FORMAT(p.join_date,'%Y-%m') = DATE_FORMAT(v.visit_date,'%Y-%m')
) AS total_patients_joined

FROM visits v
$where
GROUP BY month
ORDER BY month DESC
LIMIT :limit OFFSET :offset
";

$stmt = $conn->prepare($sql);

if ($search != "") {
    $stmt->bindParam(":search", $like);
}

$stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
$stmt->bindParam(":offset", $offset, PDO::PARAM_INT);

$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<table class="table table-bordered table-striped text-center">

<thead class="table-dark">
<tr>
    <th>Month (YYYY-MM)</th>
    <th>Total Visits</th>
    <th>Patients Joined</th>
</tr>
</thead>

<tbody>

<?php foreach($data as $d): ?>
<tr>
    <td><?= $d['month']; ?></td>
    <td><?= $d['total_visits']; ?></td>
    <td><?= $d['total_patients_joined']; ?></td>
</tr>
<?php endforeach; ?>

</tbody>

</table>


<form method="POST" class="text-center mt-3">

<input type="hidden" name="search_text" value="<?= htmlspecialchars($search) ?>">

<button name="page" value="<?= $page-1 ?>" class="btn btn-secondary"
<?= ($page<=1) ? 'disabled' : '' ?>>
Prev
</button>

<span class="mx-3">
Page <?= $page ?> of <?= $totalPages ?>
</span>

<button name="page" value="<?= $page+1 ?>" class="btn btn-secondary"
<?= ($page>=$totalPages) ? 'disabled' : '' ?>>
Next
</button>

</form>

</div>

</body>
</html>
<?php
include("../includes/footer.php");
?>