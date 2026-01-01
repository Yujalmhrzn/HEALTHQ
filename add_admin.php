<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "healthq";

// Start session
session_start();

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in and has userid 1
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
    echo "<script>alert('You do not have permission to add or delete records.'); window.location.href='view_admins.php';</script>";
    exit();
}

// Fetch hospital names for the dropdown
$hospital_query = "SELECT name FROM hospitals";
$hospital_result = $conn->query($hospital_query);

// Process form submission to add new admin and user
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $hospital_name = $_POST['hospital_name'];
    $type = "admin"; // Default type

    // Insert into admins table
    $sql_admin = "INSERT INTO admins (name, email, hospital_name) VALUES ('$name', '$email', '$hospital_name')";

    // Insert into users table
    $sql_user = "INSERT INTO users (email, password, fullname, phone, type, hospital_name) VALUES ('$email', '$password', '$name', '$phone', '$type', '$hospital_name')";

    if ($conn->query($sql_user) === TRUE) {
        echo "<script>alert('New admin added successfully')</script>";
    } else {
        echo "Error: " . $sql_admin . "<br>" . $conn->error;
        echo "Error: " . $sql_user . "<br>" . $conn->error;
    }

    $conn->close();
}

// Check if delete_id is set and delete the record
if (isset($_GET['delete_id']) && $_SESSION['userid'] == 1) {
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    $delete_query = "DELETE FROM admins WHERE email = '$delete_id'";

    if (mysqli_query($conn, $delete_query)) {
        header("Location: view_admins.php");
        exit();
    } else {
        die("Error deleting admin: " . mysqli_error($conn));
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Admin</title>
    <link rel="stylesheet" href="view.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }

        nav {
            margin-bottom: 20px; /* Added space below navbar */
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            width: 50%;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        label {
            font-weight: bold;
            display: block;
            margin: 10px 0 5px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="phone"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        select {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 14px 20px;
            margin-top: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            font-weight: bold;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        footer {
            margin-top: 20px;
            text-align: center;
            background-color: #51c8e6;
            padding: 10px 0;
            color: white;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav>
        <ul>
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <span class="login-container">
                <li><a href="logout.php">Logout</a></li>
            </span>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="info">
        <form method="post" action="">
            <legend><h2>Add Admin</h2></legend>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <label for="phone">Phone:</label>
            <input type="phone" id="phone" name="phone" required>
            <label for="hospital_name">Hospital Name:</label>
            <select id="hospital_name" name="hospital_name" required>
                <?php
                if ($hospital_result->num_rows > 0) {
                    while($row = $hospital_result->fetch_assoc()) {
                        echo "<option value='" . htmlspecialchars($row['name']) . "'>" . htmlspecialchars($row['name']) . "</option>";
                    }
                } else {
                    echo "<option value=''>No hospitals available</option>";
                }
                ?>
            </select>
            <input type="submit" value="Submit">
        </form>
    </div>

    <!-- Footer Section -->
    <footer>
        <p>&copy; 2024, All Rights Reserved,<br>Design &amp; Designed by: <b>HealthQ</b></p>
    </footer>
</body>
</html>
