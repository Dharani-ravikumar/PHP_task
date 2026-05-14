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
    <title>Follow-up Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-4">

<h2 class="text-center mb-4"> Follow-up Report</h2>


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


$countSql = "SELECT COUNT(*) as total FROM patient p $where";
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
p.patient_id,
p.name,


COALESCE(MAX(v.visit_date), 'No Visit') AS last_visit,


COALESCE(
    DATE_ADD(MAX(v.visit_date), INTERVAL 7 DAY),
    'Not Scheduled'
) AS follow_up_due,


CASE 
    WHEN MAX(v.visit_date) IS NULL THEN 'NO VISIT'
    WHEN DATE_ADD(MAX(v.visit_date), INTERVAL 7 DAY) < CURDATE() THEN 'OVERDUE'
    WHEN DATE_ADD(MAX(v.visit_date), INTERVAL 7 DAY)
         BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
    THEN 'UPCOMING'
    ELSE 'OK'
END AS status

FROM patient p
LEFT JOIN visits v ON p.patient_id = v.patient_id
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


 
<table class="table table-bordered table-striped text-center">

<thead class="table-dark">
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Last Visit</th>
    <th>Follow-up</th>
    <th>Status</th>
</tr>
</thead>

<tbody>

<?php foreach($data as $d): ?>
<tr>

    <td><?= $d['patient_id'] ?></td>
    <td><?= $d['name'] ?></td>

   
    <td>
        <?= ($d['last_visit'] == 'No Visit') ? 'No Visit Yet' : $d['last_visit'] ?>
    </td>

   
    <td>
        <?= ($d['follow_up_due'] == 'Not Scheduled') ? 'Not Scheduled' : $d['follow_up_due'] ?>
    </td>

 
    <td>
        <?php if($d['status'] == 'OVERDUE'): ?>
            <span class="badge bg-danger">OVERDUE</span>

        <?php elseif($d['status'] == 'UPCOMING'): ?>
            <span class="badge bg-warning text-dark">UPCOMING</span>

        <?php elseif($d['status'] == 'NO VISIT'): ?>
            <span class="badge bg-secondary">NO VISIT</span>

        <?php else: ?>
            <span class="badge bg-success">OK</span>
        <?php endif; ?>
    </td>

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