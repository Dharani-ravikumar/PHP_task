<?php
require("../config/db.php");
include("../includes/header.php");



$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$limit = 5;
$offset = ($page - 1) * $limit;



$search = "";

if (isset($_POST['search'])) {
    $search = trim($_POST['search_text'] ?? "");
}

$where = "";

if ($search != "") {
    $where = "WHERE p.name LIKE :search";
}



$countSql = "
SELECT COUNT(*) as total 
FROM patient p
$where ";

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
    TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) AS age_years,

    CONCAT(
        TIMESTAMPDIFF(YEAR, p.dob, CURDATE()), ' Years ',
        TIMESTAMPDIFF(MONTH, p.dob, CURDATE()) % 12, ' Months'
    ) AS age_full,

    p.join_date AS Join_date,

    COUNT(v.visit_id) AS total_visits

FROM patient p

LEFT JOIN visits v 
ON p.patient_id = v.patient_id

$where

GROUP BY p.patient_id

ORDER BY p.patient_id DESC

LIMIT $limit OFFSET $offset
";

$stmt = $conn->prepare($sql);

if ($search != "") {
    $stmt->bindParam(":search", $like);
}

$stmt->execute();

$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Patient List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
   

<div class="container mt-4">

    <h2 class="text-center mb-3">Patient List</h2>

    <div class="d-flex justify-content-end mb-3">
        <a href="add.php" class="btn btn-success">+ Add Patient</a>
    </div>

    <!-- ================= SEARCH ================= -->

    <form method="POST" class="d-flex justify-content-center mb-3 gap-2">

        <input type="text"
               name="search_text"
               class="form-control w-25"
               placeholder="Search Patient Name"
               value="<?= htmlspecialchars($search) ?>">

        <button type="submit" name="search" class="btn btn-success">
            Search
        </button>

    </form>

    <!-- ================= TABLE ================= -->

    <table class="table table-bordered table-striped text-center">

        <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Age</th>
            <th>Age Full</th>
            <th>Join Date</th>
            <th>Total Visits</th>
            <th>Actions</th>
        </tr>
        </thead>

        <tbody>

        <?php foreach($patients as $p): ?>

        <tr>
            <td><?= $p['patient_id']; ?></td>
            <td><?= $p['name']; ?></td>
            <td><?= $p['age_years']; ?></td>
            <td><?= $p['age_full']; ?></td>
            <td><?= $p['Join_date']; ?></td>
            <td><?= $p['total_visits']; ?></td>

            <td>
                <div class="d-flex gap-1 justify-content-center">

                    <form method="POST" action="view.php">
                        <input type="hidden" name="id" value="<?= $p['patient_id']; ?>">
                        <button class="btn btn-primary btn-sm">View</button>
                    </form>

                    <form method="POST" action="edit.php">
                        <input type="hidden" name="id" value="<?= $p['patient_id']; ?>">
                        <button class="btn btn-warning btn-sm">Edit</button>
                    </form>

                </div>
            </td>
        </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

    <!-- ================= PAGINATION ================= -->

    <form method="POST" class="text-center mt-3">

        <!-- keep search when paging -->
        <input type="hidden" name="search_text" value="<?= htmlspecialchars($search) ?>">

        <button name="page" value="<?= $page - 1 ?>"
                class="btn btn-secondary"
                <?= ($page <= 1) ? 'disabled' : '' ?>>
            Prev
        </button>

        <span class="mx-3">
            Page <?= $page ?> of <?= $totalPages ?>
        </span>

        <button name="page" value="<?= $page + 1 ?>"
                class="btn btn-secondary"
                <?= ($page >= $totalPages) ? 'disabled' : '' ?>>
            Next
        </button>

    </form>

</div>

</body>
</html>

<?php include("../includes/footer.php"); ?>