<?php
require("../config/db.php");
include("../includes/header.php"); 
$section = "full";
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
<title>Reports Summary</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-4">

<h2 class="text-center mb-4"> Reports Summary</h2>


<div class="d-flex justify-content-center gap-3 mb-3">

<form action ="monthly.php" method="POST"><button name="monthly" class="btn btn-primary">Monthly</button></form>
<form action ="birthdays.php" method="POST"><button name="birthday" class="btn btn-warning">Birthday</button></form>
<form action ="followups.php" method="POST"><button name="followup" class="btn btn-danger">Follow-Up</button></form>

</div>

<?php if($section == "full"): ?>


<form method="POST" class="mb-3 d-flex justify-content-center gap-2">

<input type="text" name="search_text" class="form-control w-25"
value="<?= htmlspecialchars($search) ?>" placeholder="Search Patient Name">

<button type="submit" name="search" class="btn btn-success">Search</button>

</form>

<?php
$where = "";

if ($search != "") {
    $where = "WHERE p.name LIKE :search";
}

/* ================= COUNT ================= */
$countSql = "SELECT COUNT(*) as total FROM patient p $where";
$countStmt = $conn->prepare($countSql);

if ($search != "") {
    $like = "%$search%";
    $countStmt->bindParam(":search", $like);
}

$countStmt->execute();
$totalRows = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalRows / $limit);

/* ================= MAIN QUERY (FIXED NULL ISSUE) ================= */
$sql = "
SELECT 
p.patient_id,
p.name,
TIMESTAMPDIFF(YEAR,p.dob,CURDATE()) AS age,
COUNT(v.visit_id) AS total_visits,

-- SAFE VALUES (NO NULL)
COALESCE(MAX(v.visit_date), 'No Visit') AS last_visit,

COALESCE(
    TIMESTAMPDIFF(DAY, MAX(v.visit_date), CURDATE()),
    0
) AS days_since_last_visit,

COALESCE(
    DATE_ADD(MAX(v.visit_date), INTERVAL 7 DAY),
    'No Follow-up'
) AS next_follow_up

FROM patient p
LEFT JOIN visits v ON p.patient_id=v.patient_id
$where
GROUP BY p.patient_id
ORDER BY p.patient_id DESC
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

<h4 class="mb-3">📄 Full Summary</h4>

<table class="table table-bordered table-striped text-center">

<tr class="table-dark">
<th>ID</th>
<th>Name</th>
<th>Age</th>
<th>Total Visits</th>
<th>Last Visit</th>
<th>Days Since</th>
<th>Next Follow-up</th>
</tr>

<?php foreach($data as $d): ?>
<tr>
<td><?= $d['patient_id'] ?></td>
<td><?= $d['name'] ?></td>
<td><?= $d['age'] ?></td>
<td><?= $d['total_visits'] ?></td>
<td><?= $d['last_visit'] ?></td>
<td><?= $d['days_since_last_visit'] ?></td>
<td><?= $d['next_follow_up'] ?></td>
</tr>
<?php endforeach; ?>

</table>

<!-- ================= PAGINATION ================= -->
<form method="POST" class="text-center mt-3">

<input type="hidden" name="search_text" value="<?= htmlspecialchars($search) ?>">

<button name="page" value="<?= $page-1 ?>" class="btn btn-secondary"
<?= $page<=1?'disabled':'' ?>>
Prev
</button>

<span class="mx-3">
Page <?= $page ?> of <?= $totalPages ?>
</span>

<button name="page" value="<?= $page+1 ?>" class="btn btn-secondary"
<?= $page>=$totalPages?'disabled':'' ?>>
Next
</button>

</form>

<?php endif; ?>


<!-- ================= BIRTHDAY ================= -->




</body>
</html>
<?php
include("../includes/footer.php");
?>