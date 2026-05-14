<?php

require("../config/db.php");
include("../includes/header.php");

$message = "";
$error = "";

$name = "";
$dob = "";
$join_date = "";
$phone = "";
$address = "";

if (isset($_POST['submit'])) {

    $name = trim($_POST['name']);
    $dob = $_POST['dob'];
    $join_date = $_POST['join_date'];
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    $today = date("Y-m-d");

    /* ================= VALIDATION ================= */

    if (empty($name) || empty($dob) || empty($join_date) || empty($phone) || empty($address)) {
        $error = "All fields are required!";
    }

    elseif ($dob > $today) {
        $error = "DOB cannot be in the future!";
    }

    elseif ($join_date < $dob) {
        $error = "Join date must be after DOB!";
    }

    
    else {

        $sql = "INSERT INTO patient (name, dob, join_date, phone, address)
                VALUES (:name, :dob, :join_date, :phone, :address)";

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':dob', $dob);
        $stmt->bindParam(':join_date', $join_date);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address);

        if ($stmt->execute()) {
 $message = "Patient added successfully!";

            // clear form after success
            $name = $dob = $join_date = $phone = $address = "";

        } else {
            $error = "Failed to add patient!";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <?php if($message): ?>
    <div class="alert alert-success text-center">
        <?= $message ?>
    </div>
<?php endif; ?>

<?php if($error): ?>
    <div class="alert alert-danger text-center">
        <?= $error ?>
    </div>
<?php endif; ?>

<title>Add Patient</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">

<div class="col-md-6">

    <h3 class="text-center mb-3">Add Patient</h3>

    <!-- ================= ERROR ================= -->

    <?php if($error): ?>
        <div class="alert alert-danger text-center">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="p-4 bg-white border rounded shadow">

        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control"
                   value="<?= htmlspecialchars($name) ?>">
        </div>

        <div class="mb-3">
            <label>DOB</label>
            <input type="date" name="dob" class="form-control"
                   value="<?= $dob ?>">
        </div>

        <div class="mb-3">
            <label>Join Date</label>
            <input type="date" name="join_date" class="form-control"
                   value="<?= $join_date ?>">
        </div>

        <div class="mb-3">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control"
                   value="<?= htmlspecialchars($phone) ?>">
        </div>

        <div class="mb-3">
            <label>Address</label>
            <textarea name="address" class="form-control"><?= htmlspecialchars($address) ?></textarea>
        </div>

        <button type="submit" name="submit" class="btn btn-success w-100">
            Add Patient
        </button>

    </form>

</div>

</div>

</body>
</html>

<?php include("../includes/footer.php"); ?>