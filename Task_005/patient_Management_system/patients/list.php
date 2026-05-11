<?php
session_start();
include("../includes/header.php");
include("../config/db.php");



// Search
$search = "";

if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

// Sorting
$sort = "id";

if (isset($_GET['sort'])) {
    $sort = $_GET['sort'];
}

// Pagination

$find_row="SELECT *FROM PATIENTS";
$run_row=$conn->query($find_row);
$limit = 5;
$page = 1;
$rows=$run_row->num_rows;
$totalPages=ceil($rows/$limit);
 // for ($i = 1; $i <= $totalPages; $i++) 
  //   echo '<a href="list.php?page=' . $i . '">' . $i . '</a>';

if (isset($_GET['page'])) {
    $page = $_GET['page'];
}

$start = ($page - 1) * $limit;

$sql = "SELECT * FROM patients
        WHERE patient_name LIKE '%$search%'
        OR diagnosis LIKE '%$search%'
        ORDER BY $sort ASC
        LIMIT $start, $limit";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Patient List</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

</head>

<body>
    <div class="container mt-3">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">

                <?php
                if (isset($_SESSION['success'])) {
                    echo "<div class='alert alert-success'>" . $_SESSION['success'] . "</div>";
                    unset($_SESSION['success']);
                }
                ?>

            </div>
        </div>
    </div>

    <div class="container mt-5">

        <div class="d-flex justify-content-between align-items-center mb-3">

            <h2>Patient List</h2>

           

        </div>

        <!-- Search Form -->
        <form method="GET" class="row mb-4">

            <div class="col-md-4">

                <input type="text"
                    name="search"
                    class="form-control"
                    placeholder="Search by name or diagnosis"
                    value="<?php echo $search; ?>">

            </div>

            <div class="col-md-3">

                <select name="sort" class="form-select">

                    <option value="patient_name"
                        <?php
                        if ($sort == "patient_name") echo "selected";
                        ?>>
                        Sort by Name
                    </option>

                    <option value="age"
                        <?php
                        if ($sort == "age") echo "selected";
                        ?>>
                        Sort by Age
                    </option>

                </select>

            </div>

            <div class="col-md-2">

                <button type="submit" class="btn btn-success w-100">
                    Search
                </button>

            </div>

        </form>

        <!-- Patient Table -->
        <table class="table table-bordered table-striped">

            <thead class="table-dark">

                <tr>

                    <th>ID</th>
                    <th>Patient Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Diagnosis</th>
                    <th>Actions</th>

                </tr>

            </thead>

            <tbody>

                <?php

                if ($result->num_rows > 0) {

                    while ($row = $result->fetch_assoc()) {

                ?>

                        <tr>

                            <td><?php echo $row['id']; ?></td>

                            <td><?php echo $row['patient_name']; ?></td>

                            <td><?php echo $row['email']; ?></td>

                            <td><?php echo $row['phone']; ?></td>

                            <td><?php echo $row['age']; ?></td>

                            <td><?php echo $row['gender']; ?></td>

                            <td><?php echo $row['diagnosis']; ?></td>

                            <td>

                                <a href="edit.php ? id=<?php echo $row['id']; ?>"
                                    class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <a href="delete.php ? id=<?php echo $row['id']; ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure?')">

                                    Delete

                                </a>

                            </td>

                        </tr>

                    <?php
                    }
                } else {

                    ?>

                    <tr>

                        <td colspan="8" class="text-center">
                            No patients found
                        </td>

                    </tr>

                <?php
                }

                ?>

            </tbody>

        </table>

        <!-- Pagination -->
       <nav>
    <ul class="pagination">

        <?php
        for ($i = 1; $i <= $totalPages; $i++) {

            $active = ($page == $i) ? "active" : "";

            echo '
            <li class="page-item '.$active.'">
                <a class="page-link" href="list.php?page='.$i.'">
                    '.$i.'
                </a>
            </li>';
        }
        ?>

    </ul>
</nav>
    </div>

</body>

</html>
<?php
include("../includes/footer.php");
?>