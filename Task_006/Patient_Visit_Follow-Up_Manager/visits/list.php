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
    <title>Visits List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-4">


    <!-- ADD VISIT BUTTON -->
    <a href="add.php" class="btn btn-success">
        + Add Visit
    </a>
<h2 class="text-center mb-4">Visits List</h2>

<!-- ================= SEARCH ================= -->
<form method="POST" class="d-flex justify-content-center mb-3 gap-2">

<input type="text" name="search_text" class="form-control w-25"
placeholder="Search Patient Name"
value="<?= htmlspecialchars($search) ?>">

<button type="submit" name="search" class="btn btn-success">Search</button>

</form>

<?php
$where = "";

if ($search != "") {
    $where = "WHERE p.name LIKE :search";
}

/* ================= COUNT ================= */
$countSql = "
SELECT COUNT(*) as total
FROM visits v
INNER JOIN patient p ON p.patient_id = v.patient_id
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

/* ================= MAIN QUERY ================= */
$sql = "
SELECT 
v.visit_id,
p.patient_id,
p.name,
v.visit_date,

TIMESTAMPDIFF(DAY, v.visit_date, CURDATE()) AS days_since_visit,

DATE_ADD(v.visit_date, INTERVAL 7 DAY) AS follow_up_due,

CASE 
    WHEN DATE_ADD(v.visit_date, INTERVAL 7 DAY) < CURDATE()
    THEN 'OVERDUE'
    ELSE 'NOT OVERDUE'
END AS overdue_status,

CASE 

    WHEN TIMESTAMPDIFF(DAY, v.visit_date, CURDATE()) > 180
    THEN 'INACTIVE'

    WHEN DATE_ADD(v.visit_date, INTERVAL 7 DAY)
         BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
    THEN 'UPCOMING'

    ELSE 'NORMAL'

END AS upcoming_status

FROM visits v
INNER JOIN patient p ON p.patient_id = v.patient_id
$where
ORDER BY v.visit_date DESC
LIMIT :limit OFFSET :offset
";

$stmt = $conn->prepare($sql);

if ($search != "") {
    $stmt->bindParam(":search", $like);
}

$stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
$stmt->bindParam(":offset", $offset, PDO::PARAM_INT);

$stmt->execute();
$visits = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!-- ================= TABLE ================= -->
<div class="table-responsive">

<table class="table table-bordered table-striped text-center">

<thead class="table-dark">
<tr>
    <th>Vist ID</th>
     <th>Patient ID</th>
    <th>Patient</th>
    <th>Visit Date</th>
    <th>Days Since Visit</th>
    <th>Follow-up Due</th>
    <th>Status</th>
    <th>Upcoming</th>
    <th>Action</th>
</tr>
</thead>

<tbody>

<?php foreach($visits as $v): ?>

<tr>
    <td><?= $v['visit_id']; ?></td>
    <td><?= $v['patient_id']; ?></td>
    <td><?= $v['name']; ?></td>
    <td><?= $v['visit_date']; ?></td>
    <td><?= $v['days_since_visit']; ?> days</td>
    <td><?= $v['follow_up_due']; ?></td>

    <td>
        <?php if ($v['overdue_status'] == 'OVERDUE'): ?>
            <span class="badge bg-danger">OVERDUE</span>
        <?php else: ?>
            <span class="badge bg-success">OK</span>
        <?php endif; ?>
    </td>

    <td>

    <?php if ($v['upcoming_status'] == 'UPCOMING'): ?>

        <span class="badge bg-warning text-dark">
            UPCOMING
        </span>

    <?php elseif ($v['upcoming_status'] == 'INACTIVE'): ?>

        <span class="badge bg-dark">
            INACTIVE
        </span>

    <?php else: ?>

        <span class="badge bg-secondary">
            NORMAL
        </span>

    <?php endif; ?>

</td>

    <td>
        <form method="POST" action="patient_visits.php">
            <input type="hidden" name="id" value="<?= $v['patient_id']; ?>">
            <button type="submit" class="btn btn-info btn-sm">History</button>
        </form>
    </td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>


<form method="POST" class="text-center mt-3">

<input type="hidden" name="search_text" value="<?= htmlspecialchars($search) ?>">

<button name="page" value="<?= $page-1 ?>" class="btn btn-secondary"
<?= ($page<=1)?'disabled':'' ?>>
Prev
</button>

<span class="mx-3">
Page <?= $page ?> of <?= $totalPages ?>
</span>

<button name="page" value="<?= $page+1 ?>" class="btn btn-secondary"
<?= ($page>=$totalPages)?'disabled':'' ?>>
Next
</button>

</form>

</div>

</body>
</html>
<?php
include("../includes/footer.php");
?>