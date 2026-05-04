<?php
session_start();


$usernameCookie = $_COOKIE['remember_username'] ?? '';
$theme = $_COOKIE['user_theme'] ?? 'bg-light';

$error = $_SESSION['error'] ?? '';

$errors = $_SESSION['errors'] ?? [];

$old = $_SESSION['old'] ?? [];

unset($_SESSION['error'], $_SESSION['errors'], $_SESSION['old']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="<?php echo $theme; ?>">

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-12 col-sm-10 col-md-6 col-lg-4">

            <div class="card shadow p-4">

                <h2 class="text-center mb-3">Login</h2>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form action="auth.php" method="post">

                    
                    <div class="mb-3">
                        <input 
                            type="text"
                            name="username"
                            placeholder="Username"
                            class="form-control <?php echo !empty($errors['username']) ? 'is-invalid' : ''; ?>"
                            value="<?php echo $old['username'] ?? $usernameCookie; ?>"
                        >
                        <div class="text-danger small">
                            <?php echo $errors['username'] ?? ''; ?>
                        </div>
                    </div>

                   
                    <div class="mb-3">
                        <input 
                            type="email"
                            name="email"
                            placeholder="Email"
                            class="form-control <?php echo !empty($errors['email']) ? 'is-invalid' : ''; ?>"
                            value="<?php echo $old['email'] ?? ''; ?>"
                        >
                        <div class="text-danger small">
                            <?php echo $errors['email'] ?? ''; ?>
                        </div>
                    </div>

                    
                    <div class="mb-3">
                        <input 
                            type="password"
                            name="password"
                            placeholder="Password"
                            class="form-control <?php echo !empty($errors['password']) ? 'is-invalid' : ''; ?>"
                        >
                        <div class="text-danger small">
                            <?php echo $errors['password'] ?? ''; ?>
                        </div>
                    </div>

                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">
                            Remember Me
                        </label>
                    </div>

                    
                    <button type="submit" class="btn btn-success w-100">
                        Login
                    </button>

                </form>

            </div>

        </div>
    </div>
</div>

</body>
</html>