<?php
// Start session and enable output buffering
session_start();
ob_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "healthq";

// Create the database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect user input with sanitization
    $fullname = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $password_input = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format. Please enter a valid email.');</script>";
    } elseif (!preg_match("/^\d{10}$/", $phone)) {
        // Validate phone number (10 digits)
        echo "<script>alert('Please enter a valid 10-digit phone number.');</script>";
    } elseif ($password_input !== $confirm_password) {
        // Check if passwords match
        echo "<script>alert('Passwords do not match. Please try again.');</script>";
    } else {
        // Check if the email already exists using a prepared statement
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<script>alert('Email already exists!');</script>";
        } else {
            // Insert the user data into the database (no hashing)
            $stmt = $conn->prepare("INSERT INTO users (fullname, email, phone, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $fullname, $email, $phone, $password_input); // Store password as plain text

            if ($stmt->execute()) {
                echo "<script>alert('Registration successful!');
                window.location.href = 'login.php';
                </script>";
            } else {
                echo "<script>alert('Error: " . $conn->error . "');</script>";
            }
        }

        // Close statement
        $stmt->close();
    }
}

// Close the connection
$conn->close();
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthQ || Registration</title>
    <link rel="stylesheet" href="registration.css">
    <script>
        function validatePasswords() {
            // Get the values of the password fields
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;

            // Check if the passwords match
            if (password !== confirmPassword) {
                alert("Passwords do not match. Please try again.");
                return false;
            }

            // Validate email format using regex
            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (!emailRegex.test(email)) {
                alert("Please enter a valid email address.");
                return false;
            }

            // Validate phone number (basic check for 10 digits)
            const phoneRegex = /^\d{10}$/;
            if (!phoneRegex.test(phone)) {
                alert("Please enter a valid phone number (10 digits).");
                return false;
            }

            // If everything is valid, submit the form
            document.getElementById('registration-form').submit();
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>HealthQ Registration</h1>
        <form id="registration-form" method="POST">
            <div class="form-group">
                <label for="name">Full Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number:</label>
                <input type="tel" id="phone" name="phone" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm-password">Confirm Password:</label>
                <input type="password" id="confirm-password" name="confirm-password" required>
            </div>
            <button type="button" class="btn" onclick="validatePasswords()">Register</button>
            <p class="login-link">
                Already have an account? <a href="login.php">Login here</a>
            </p>
        </form>
    </div>
</body>
</html>
