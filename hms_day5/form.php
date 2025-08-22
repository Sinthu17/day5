<?php
include 'config.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    // Validation
    if (empty($name) || empty($email) || empty($phone)) {
        $message = "<div class='alert alert-warning'>⚠️ All fields are required!</div>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<div class='alert alert-danger'>❌ Invalid email format!</div>";
    } elseif (!is_numeric($phone) || strlen($phone) != 10) {
        $message = "<div class='alert alert-danger'>❌ Phone must be 10 digits!</div>";
    } else {
        // Prevent duplicate email
        $sql_check = "SELECT * FROM entries WHERE email=?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows > 0) {
            $message = "<div class='alert alert-warning'>⚠️ Email already exists!</div>";
        } else {
            // Insert into database
            $sql = "INSERT INTO entries (name,email,phone) VALUES (?,?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $name, $email, $phone);

            if ($stmt->execute()) {
                $message = "<div class='alert alert-success'>✅ Patient Registered Successfully!</div>";
                // Clear form values
                $name = $email = $phone = '';
            } else {
                $message = "<div class='alert alert-danger'>❌ Error: " . $stmt->error . "</div>";
            }
            $stmt->close();
        }

        $stmt_check->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register Patient</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #0d1b2a;
            color: #fff;
        }

        .form-container {
            max-width: 500px;
            margin: auto;
            background: #1b263b;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
        }

        h2 {
            color: #fca311;
            text-align: center;
        }

        .btn-custom {
            background-color: #fca311;
            border: none;
            color: #000;
            font-weight: bold;
        }

        .btn-custom:hover {
            background-color: #ffba08;
            color: #000;
        }

        .navbar {
            background-color: #1b263b !important;
        }

        .navbar-brand,
        .nav-link {
            color: #fca311 !important;
            font-weight: bold;
        }

        .nav-link:hover {
            color: #ffba08 !important;
        }
    </style>
</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg shadow">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">🏥 HospitalMS</a>
        <div>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="form.php">Register</a></li>
                <li class="nav-item"><a class="nav-link" href="about.html">About</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.html">Contact</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="form-container">
        <h2>📝 Register Patient</h2>

        <!-- Message -->
        <?php if (!empty($message)) echo $message; ?>

        <form action="form.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" id="name" class="form-control"
                       value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>"
                       required>
                <div class="form-text text-light">Minimum 3 characters</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control"
                       value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" id="phone" class="form-control"
                       maxlength="10"
                       value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>" required>
                <div class="form-text text-light">Enter 10-digit phone number</div>
            </div>

            <button type="submit" class="btn btn-custom w-100">Submit</button>
        </form>
    </div>
</div>

<!-- ✅ JavaScript validation copied from update.php -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const nameInput = document.getElementById("name");
    const phoneInput = document.getElementById("phone");
    const form = document.querySelector("form");

    // Real-time validation for Name
    nameInput.addEventListener("input", function () {
        this.value = this.value.toUpperCase();
        if (this.value.length < 3) {
            this.style.borderColor = "red";
        } else {
            this.style.borderColor = "green";
        }
    });

    // Real-time validation for Phone (only digits and 10 digits check)
    phoneInput.addEventListener("input", function () {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length !== 10) {
            this.style.borderColor = "red";
        } else {
            this.style.borderColor = "green";
        }
    });

    // Final check on form submission
    form.addEventListener("submit", function (e) {
        const name = nameInput.value;
        const phone = phoneInput.value;

        if (name.length < 3) {
            e.preventDefault();
            alert("Name must be at least 3 characters!");
            nameInput.focus();
            return;
        }

        if (phone.length !== 10) {
            e.preventDefault();
            alert("Phone number must be exactly 10 digits!");
            phoneInput.focus();
            return;
        }
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
