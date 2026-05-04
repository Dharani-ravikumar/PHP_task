<?php
session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$theme = $_SESSION['theme'];
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="<?php echo $theme; ?>">

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">

        <div class="col-12 col-sm-10 col-md-6 col-lg-4">

            <div class="card shadow p-4 text-center">

                <h2 class="mb-3">Welcome, <?php echo $_SESSION['username']; ?></h2>

                <p><strong>User ID:</strong> <?php echo $_SESSION['user_id']; ?></p>
                <p><strong>Email:</strong> <?php echo $_SESSION['email']; ?></p>

                <a href="logout.php" class="btn btn-danger w-100 mt-3">
                    Logout
                </a>

            </div>

        </div>

    </div>
</div>

</body>
</html>