<?php

include("../config/db.php");

$id = $_GET['id'];

/* =========================
   FETCH EXISTING DATA
========================= */
$sql = "SELECT * FROM patients WHERE id=$id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

/* =========================
   UPDATE PATIENT
========================= */
if (isset($_POST['update'])) {

    $name = trim($_POST['patient_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $age = trim($_POST['age']);
    $gender = trim($_POST['gender']);
    $diagnosis = trim($_POST['diagnosis']);

    $update = "UPDATE patients SET
                patient_name='$name',
                email='$email',
                phone='$phone',
                age='$age',
                gender='$gender',
                diagnosis='$diagnosis'
               WHERE id=$id";

    if ($conn->query($update) === TRUE) {

       
    session_start();

    $_SESSION['success'] = "Data Updated successfully!";
    header("Location: list.php");
    exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Patient</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
            <link rel="stylesheet" href="/assets/css/style.css">

</head> 

<body>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-6">

            <div class="card shadow p-4">

                <h3 class="text-center mb-4">Edit Patient</h3>

                <form method="POST">

                    <!-- Name -->
                    <div class="mb-3">
                        <label class="form-label">Patient Name</label>
                        <input type="text" class="form-control"
                               name="patient_name"
                               value="<?php echo $row['patient_name']; ?>" required>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control"
                               name="email"
                               value="<?php echo $row['email']; ?>" required>
                    </div>

                    <!-- Phone -->
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control"
                               name="phone"
                               value="<?php echo $row['phone']; ?>" required>
                    </div>

                    <!-- Age -->
                    <div class="mb-3">
                        <label class="form-label">Age</label>
                        <input type="number" class="form-control"
                               name="age"
                               value="<?php echo $row['age']; ?>" required>
                    </div>

                    <!-- Gender -->
                    <div class="mb-3">
                        <label class="form-label">Gender</label>
                        <select class="form-select" name="gender" required>

                            <option value="Male" <?php if($row['gender']=="Male") echo "selected"; ?>>
                                Male
                            </option>

                            <option value="Female" <?php if($row['gender']=="Female") echo "selected"; ?>>
                                Female
                            </option>

                            <option value="Other" <?php if($row['gender']=="Other") echo "selected"; ?>>
                                Other
                            </option>

                        </select>
                    </div>

                    <!-- Diagnosis -->
                    <div class="mb-3">
                        <label class="form-label">Diagnosis</label>
                        <input type="text" class="form-control"
                               name="diagnosis"
                               value="<?php echo $row['diagnosis']; ?>" required>
                    </div>

                    <!-- Button -->
                    <button type="submit"
                            name="update"
                            class="btn btn-warning w-100">
                        Update Patient
                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

</body>
</html>