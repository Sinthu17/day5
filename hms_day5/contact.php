<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $message = trim($_POST['message']);

    // Validation
    if (empty($name) || empty($email) || empty($message)) {
        die("<script>alert('⚠️ All fields are required!'); window.location='contact.html';</script>");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("<script>alert('❌ Invalid email format!'); window.location='contact.html';</script>");
    }

    // Insert into database
    $sql = "INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $name, $email, $message);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Message sent successfully!'); window.location='contact.html';</script>";
    } else {
        echo "<script>alert('❌ Error: " . $stmt->error . "'); window.location='contact.html';</script>";
    }

    $stmt->close();
}
$conn->close();
?>
