<?php
session_start();
require("../config/db.php");
include("../includes/header.php");

$message = "";
$error="";

if (isset($_POST['submit'])) {

    $patient_id = $_POST['patient_id'];
    $visit_date = $_POST['visit_date'];
    $consultation_fee = $_POST['consultation_fee'];
    $lab_fee = $_POST['lab_fee'];

   $today = date("Y-m-d");

if (empty($patient_id) || empty($visit_date)) {
        $error = "Patient ID and Visit Date are required!";
    }

    /* ================= 2. DATE VALIDATION ================= */
    elseif ($visit_date != $today) {
        $error = "Visit date must be today !";
         header("Refresh:4");
    }

    else {

    $check = $conn->prepare("SELECT patient_id FROM patient WHERE patient_id = :id");
    $check->bindParam(":id", $patient_id);
    $check->execute();

    $patientExists = $check->fetch(PDO::FETCH_ASSOC);
    
    if (!$patientExists) {
        $error = " Patient ID does not exist!";
    }

    elseif (empty($patient_id) || empty($visit_date)) {
        $error = "Patient ID and Visit Date are required!";
    }

    else {

        /* ================= INSERT VISIT ================= */

        $sql = "
        INSERT INTO visits 
        (patient_id, visit_date, consultation_fee, lab_fee)
        VALUES 
        (:patient_id, :visit_date, :consultation_fee, :lab_fee)
        ";

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(":patient_id", $patient_id);
        $stmt->bindParam(":visit_date", $visit_date);
        $stmt->bindParam(":consultation_fee", $consultation_fee);
        $stmt->bindParam(":lab_fee", $lab_fee);

        if ($stmt->execute()) {
            $message = " Visit added successfully!";
           header("Refresh:4");
        } else {
            $error = " Failed to add visit!";
            header("Refresh:4");
        }
    }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Visit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="d-flex justify-content-center align-items-center" style="min-height:100vh;">

    <div class="col-md-5">

        <h2 class="text-center mb-3">Add Visit</h2>

        <?php if($message): ?>
            <div class="alert alert-info text-center">
                <?= $message ?>
            </div>
        <?php endif; ?>
        <?php if($error): ?>
    <div class="alert alert-danger text-center">
        <?= $error ?>
       
    </div>
<?php endif; ?>

        <form method="POST" class="p-4 border rounded bg-white shadow">

            <div class="mb-3">
                <label>Patient ID</label>
                <input type="number" name="patient_id" class="form-control" 
                 required>
            </div>

            <div class="mb-3">
                <label>Visit Date</label>
                <input type="date" name="visit_date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Consultation Fee</label>
                <input type="number" name="consultation_fee" class="form-control">
            </div>

            <div class="mb-3">
                <label>Lab Fee</label>
                <input type="number" name="lab_fee" class="form-control">
            </div>

            <button type="submit" name="submit" class="btn btn-success w-100">
                Add Visit
            </button>

            <a href="list.php" class="btn btn-secondary w-100 mt-2">
                Back
            </a>

        </form>

    </div>

</div>

</body>
</html>

<?php include("../includes/footer.php"); ?>