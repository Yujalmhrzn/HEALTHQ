<?php
// Start the session and enable output buffering to prevent header issues
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password_input = $_POST['password'];

        // Prepare statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT user_id, fullname, password, type FROM users WHERE email = ?");
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $stored_password = $row['password'];  // The stored password as plain text
                $user_type = $row['type'];
                $user_id = $row['user_id'];  // Use user_id instead of id
                $user_fullname = $row['fullname'];

                // Compare the entered password with the stored password
                if ($password_input == $stored_password) {
                    // Store user data in the session
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['user_name'] = $user_fullname;

                    // Redirect based on user type
                    if ($user_type == 'user') {
                        ob_end_clean(); // Clear buffer
                        header("Location: user_dashboard.php");
                        exit();
                    } elseif ($user_type == 'admin') {
                        ob_end_clean(); // Clear buffer
                        header("Location: admin_dashboard.php");
                        exit();
                    }
                } else {
                    echo "<p>Incorrect password. Please try again.</p>";
                }
            } else {
                echo "<p>No account found with that email. Please register.</p>";
            }
            $stmt->close();
        } else {
            echo "<p>Failed to prepare statement: " . $conn->error . "</p>";
        }
    } else {
        echo "<p>Please enter both email and password.</p>";
    }
}

// Close the connection
$conn->close();
ob_end_flush(); // End output buffering
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthQ || Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="container">
        <h1>HealthQ Login</h1>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
            <p class="forgot-password">
                <a href="registration.php">Register New Account</a>
            </p>
        </form>
    </div>
</body>
</html>
