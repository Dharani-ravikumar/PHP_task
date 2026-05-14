<?php
session_start();
require("./config/db.php");

$error = "";

/* ================= LOGIN ================= */

if(isset($_POST['login'])){

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if($username == "" || $password == ""){

        $error = "All fields are required";

    }else{

        $sql = "
        SELECT *
        FROM users
        WHERE username = ?
        AND password = ?
        ";

        $stmt = $conn->prepare($sql);

        $stmt->execute([$username,$password]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user){

            $_SESSION['user'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            header("Location:dashboard.php");
            exit;

        }else{

            $error = "Invalid Username or Password";

        }

    }

}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Hospital Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container">

    <div class="row justify-content-center align-items-center vh-100">

        <div class="col-md-4">

            <div class="card shadow border-0">

                <div class="card-body p-4">

                    <h2 class="text-center mb-4 text-primary">
                        Hospital Login
                    </h2>

                    <!-- ================= ERROR ================= -->

                    <?php if($error != ""): ?>

                        <div class="alert alert-danger">

                            <?= $error ?>

                        </div>

                    <?php endif; ?>

                    <!-- ================= FORM ================= -->

                    <form method="POST">

                        <div class="mb-3">

                            <label class="form-label">
                                Username
                            </label>

                            <input type="text"
                                   name="username"
                                   class="form-control"
                                   placeholder="Enter Username">

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Password
                            </label>

                            <input type="password"
                                   name="password"
                                   class="form-control"
                                   placeholder="Enter Password">

                        </div>

                        <button type="submit"
                                name="login"
                                class="btn btn-primary w-100">

                            Login

                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>