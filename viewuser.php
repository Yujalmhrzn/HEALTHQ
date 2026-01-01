<?php
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

// Check if the 'delete_id' parameter is set in the URL (for deletion)
if (isset($_GET['delete_id'])) {
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);

    // Query to delete user from the 'user' table
    $delete_query = "DELETE FROM users WHERE email = '$delete_id' AND type = 'user'"; // Assuming 'email' is unique

    // Execute the delete query
    if (mysqli_query($conn, $delete_query)) {
        echo "<script>alert('User deleted successfully.'); window.location.href='viewpatient.php';</script>";
    } else {
        echo "<script>alert('Error deleting user: " . mysqli_error($conn) . "');</script>";
    }
}

// Query to fetch user information from the 'user' table
$query = "SELECT fullname, email, phone
          FROM users
          WHERE type = 'user'"; 

// Execute the query
$result = mysqli_query($conn, $query);

// Check if the query was successful
if (!$result) {
    die("Query failed: " . mysqli_error($conn)); // Display error if query fails
}
?>

<!doctype html>
<html lang="en">
<head>
    <title>HealthQ || View Users</title>
    <link rel="stylesheet" href="view.css"> <!-- CSS file for user table -->
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
    <div class="container">
        <div class="info">
            <!-- User Information Table -->
            <div class="appointments-table">
                <h2>Users' Information</h2>
                <div class="table-container">
                    <?php
                    // Check if there are any user information to display
                    if (mysqli_num_rows($result) > 0) {
                        echo "<table>
                                <tr>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Phone No.</th>
                                    <th>Actions</th>
                                </tr>";
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>
                                    <td>" . htmlspecialchars($row['fullname']) . "</td>
                                    <td>" . htmlspecialchars($row['email']) . "</td>
                                    <td>" . htmlspecialchars($row['phone']) . "</td>
                                    <td><a href='?delete_id=" . urlencode($row['email']) . "' class='delete-btn'>Delete</a></td>
                                  </tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "<p>No users found.</p>";
                    }

                    // Close connection
                    mysqli_close($conn);
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
