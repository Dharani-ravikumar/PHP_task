<?php
include("../includes/header.php");
include("../config/db.php");

$message = "";
$class = "";
$emailError = false;
//$error = "";

if (isset($_POST['submit'])) {

    $name = trim($_POST['patient_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $age = trim($_POST['age']);
    $gender = trim($_POST['gender']);
    $diagnosis = trim($_POST['diagnosis']);

    // Check email already exists
    $check = "SELECT * FROM patients WHERE email='$email'";
    $result = $conn->query($check);

    if ($result->num_rows > 0) {

        $message = "Email already exists!";
        $class = "alert-danger";
        $emailError = true;

    } 
    elseif (strlen($phone) != 10) {
  $class = "alert-danger";
        $message = "Phone number invalid";
    }
else {

        // Insert Query
        $sql = "INSERT INTO patients
                (patient_name, email, phone, age, gender, diagnosis)
                VALUES
                ('$name', '$email', '$phone', '$age', '$gender', '$diagnosis')";

        if ($conn->query($sql) === TRUE) {

    session_start();

    $_SESSION['success'] = "Data saved successfully!";
    header("Location: list.php");
    exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>

    <title>Patient Registration</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- Custom CSS -->
     <!----<link rel="stylesheet" href="../assets/css/style.css">-->
   

</head>

<body>

<div class="container mt-5">

    <div class="row justify-content-center">
       
        <div class="col-md-6">

            <div class="card shadow p-4">

                <h3 class="text-center mb-4">
                    Patient Registration
                </h3>
                <a href="../index.php" class="btn btn-secondary mb-3">
    ← Go back to list
</a>

                <!-- Alert Message -->
                <?php
                if ($message != "") {
                    echo "<div class='alert $class'>$message</div>";
                }
                ?>

                <form method="POST">

                    <!-- Patient Name -->
                    <div class="mb-3">

                        <label class="form-label">
                            Patient Name
                        </label>

                        <input type="text"
                               class="form-control"
                               name="patient_name"
                               value="<?php echo isset($_POST['patient_name']) ? $_POST['patient_name'] : ''; ?>"
                               required>

                    </div>

                    <!-- Email -->
                    <div class="mb-3">

                        <label class="form-label">
                            Email
                        </label>

                        <input type="email"
                               class="form-control <?php if($emailError) echo 'is-invalid'; ?>"
                               name="email"
                               value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>"
                               required>

                        <div class="invalid-feedback">
                            Email already exists!
                        </div>

                    </div>

                    <!-- Phone -->
                    <div class="mb-3">

                        <label class="form-label">
                            Phone
                        </label>

                        <input type="number"
                               class="form-control"
                               name="phone"
                               value="<?php echo isset($_POST['phone']) ? $_POST['phone'] : ''; ?>"
                               required>

                    </div>

                    <!-- Age -->
                    <div class="mb-3">

                        <label class="form-label">
                            Age
                        </label>

                        <input type="number"
                               class="form-control"
                               name="age"
                               value="<?php echo isset($_POST['age']) ? $_POST['age'] : ''; ?>"
                               required>

                    </div>

                    <!-- Gender -->
                    <div class="mb-3">

                        <label class="form-label">
                            Gender
                        </label>

                        <select class="form-select"
                                name="gender"
                                required>

                            <option value="">
                                Select
                            </option>

                            <option value="Male"
                                <?php
                                if(isset($_POST['gender']) && $_POST['gender']=="Male")
                                echo "selected";
                                ?>>
                                Male
                            </option>

                            <option value="Female"
                                <?php
                                if(isset($_POST['gender']) && $_POST['gender']=="Female")
                                echo "selected";
                                ?>>
                                Female
                            </option>

                            <option value="Other"
                                <?php
                                if(isset($_POST['gender']) && $_POST['gender']=="Other")
                                echo "selected";
                                ?>>
                                Other
                            </option>

                        </select>

                    </div>

                    <!-- Diagnosis -->
                    <div class="mb-3">

                        <label class="form-label">
                            Diagnosis
                        </label>

                        <input type="text"
                               class="form-control"
                               name="diagnosis"
                               value="<?php echo isset($_POST['diagnosis']) ? $_POST['diagnosis'] : ''; ?>"
                               required>

                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                            name="submit"
                            class="btn btn-primary w-100">

                        Submit

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

</body>
</html>
<?php
include("../includes/footer.php");
?>
