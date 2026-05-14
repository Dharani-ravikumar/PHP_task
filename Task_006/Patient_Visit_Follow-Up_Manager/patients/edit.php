<?php
require("../config/db.php");
 $message="";

if (!isset($_POST['id'])) {
    die("No patient selected");
}

$id = $_POST['id'];


$sql = "SELECT * FROM patient WHERE patient_id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

$patient = $stmt->fetch(PDO::FETCH_ASSOC);


$message = "";

if (isset($_POST['update'])) {

    $name = $_POST['name'];
    $dob = $_POST['dob'];
    $join_date = $_POST['join_date'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $today = date("Y-m-d");

    
    if ($dob > $today) {
        $message = " DOB cannot be in future!";
    }
    elseif ($join_date < $dob) {
        $message = "Join date must be after DOB!";
    }
    else {

        $updateSql = "
        UPDATE patient 
        SET 
            name = :name,
            dob = :dob,
            join_date = :join_date,
            phone = :phone,
            address = :address
        WHERE patient_id = :id
        ";

        $stmt = $conn->prepare($updateSql);

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':dob', $dob);
        $stmt->bindParam(':join_date', $join_date);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
           $message = "Patient updated successfully!";

            // clear form after success
            $name = $dob = $join_date = $phone = $address = "";
        } else {
            $message = " Update failed!";
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
    <title>Edit Patient</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">

    <div class="col-md-6">

        <div class="card shadow p-4">

            <h3 class="text-center mb-3">Edit Patient</h3>

            <?php if($message): ?>
                <div class="alert alert-danger text-center">
                    <?= $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <input type="hidden" name="id" value="<?= $patient['patient_id']; ?>">

                <div class="mb-2">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control"
                        value="<?= $patient['name']; ?>">
                </div>

                <div class="mb-2">
                    <label>DOB</label>
                    <input type="date" name="dob" class="form-control"
                        value="<?= $patient['dob']; ?>">
                </div>

                <div class="mb-2">
                    <label>Join Date</label>
                    <input type="date" name="join_date" class="form-control"
                        value="<?= $patient['join_date']; ?>">
                </div>

                <div class="mb-2">
                    <label>Phone</label>
                    <input type="text" name="phone" class="form-control"
                        value="<?= $patient['phone']; ?>">
                </div>

                <div class="mb-2">
                    <label>Address</label>
                    <textarea name="address" class="form-control"><?= $patient['address']; ?></textarea>
                </div>

                <button type="submit" name="update" class="btn btn-primary w-100">
                    Update Patient
                </button>

                <a href="list.php" class="btn btn-secondary w-100 mt-2">
                    Back
                </a>

            </form>

        </div>

    </div>

</div>

</body>
</html>
<?php
include("../includes/footer.php");
?>