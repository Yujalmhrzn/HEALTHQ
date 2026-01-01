<?php
// Start session at the very beginning
session_start();

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "healthq";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user_id = $_SESSION['user_id'];

// Delete admin if the delete_id is set and user email matches the required email
if (isset($_GET['delete_id']) && $user_id === 1) {
    // Sanitize the delete_id value to prevent SQL injection
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);

    // Query to delete the admin based on email
    $delete_query = "DELETE FROM users WHERE email = '$delete_id' AND type='admin'";

    // Execute the delete query
    if (mysqli_query($conn, $delete_query)) {
        // Redirect to the same page to refresh the list
        header("Location: view_admins.php");
        exit();
    } else {
        die("Error deleting admin: " . mysqli_error($conn));
    }
} else if (isset($_GET['delete_id'])) {
    echo "<script>alert('You do not have permission to add or delete records.'); window.location.href='view_admins.php';</script>";
}

// Query to fetch admins' information including full name, email, password, and phone
$query = "
    SELECT
        user_id,
        fullname, 
        email, 
        password,
        phone,
        hospital_name 
    FROM 
        users
    WHERE
        type='admin'
";

// Execute the query
$result = mysqli_query($conn, $query);

// Check if the query was successful
if (!$result) {
    die("Query failed: " . mysqli_error($conn)); // Display error if query fails
}

// Close connection
mysqli_close($conn);
?>

<!doctype html>
<html lang="en">
<head>
    <title>HealthQ || View Admins</title>
    <link rel="stylesheet" href="view.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f4f4f4;
        }

        .delete-btn {
            color: red;
            text-decoration: none;
            font-weight: bold;
            padding: 5px 10px;
            border: 1px solid red;
            border-radius: 5px;
            background-color: #ffe6e6;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .delete-btn:hover {
            color: white;
            background-color: red;
        }

        footer {
            background-color: #51c8e6;
            padding: 20px;
            text-align: center;
            color: #fff;
            font-size: 20px;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav>
        <ul>
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="add_admin.php">Add Admin</a></li>
            <span class="login-container">
                <li><a href="logout.php">Logout</a></li>
            </span>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <div class="info">
            <!-- Admins Information Table -->
            <div class="appointments-table">
                <h2>Admins' Information</h2>
                <div class="table-container">
                    <?php
                    // Check if there are any admins' information to display
                    if (mysqli_num_rows($result) > 0) {
                        echo "<table>
                                <tr>
                                    <th>Id</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Password</th>
                                    <th>Phone</th>
                                    <th>Hospital Name</th>
                                    <th>Action</th>
                                </tr>";
                        while ($row = mysqli_fetch_assoc($result)) {
                            // Display password based on user_id
                            $password = $user_id === 1 ? htmlspecialchars($row['password']) : '***';
                            echo "<tr id='admin-" . htmlspecialchars($row['user_id']) . "'>
                                    <td>" . htmlspecialchars($row['user_id']) . "</td>
                                    <td>" . htmlspecialchars($row['fullname']) . "</td>
                                    <td>" . htmlspecialchars($row['email']) . "</td>
                                    <td>" . $password . "</td>
                                    <td>" . htmlspecialchars($row['phone']) . "</td>
                                    <td>" . htmlspecialchars($row['hospital_name']) . "</td>
                                    <td><a href='?delete_id=" . urlencode($row['email']) . "' class='delete-btn'>Delete</a></td>
                                  </tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "<p>No admins found.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Section -->
    <footer>
        <p>&copy; 2024, All Rights Reserved,<br>Design &amp; Designed by: <b>HealthQ</b></p>
    </footer>
</body>
</html>
