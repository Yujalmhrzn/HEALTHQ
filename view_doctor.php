<?php
// Start the session to get the logged-in user's details
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

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Access denied. Please log in.");
}

// Get logged-in admin's user_id from session
$user_id = $_SESSION['user_id'];

// Check if the user is a superadmin
$is_superadmin = ($user_id == 1);

// If not a superadmin, retrieve the hospital_id of the logged-in admin
if (!$is_superadmin) {
    $hospital_query = "
        SELECT hospital_id 
        FROM users 
        JOIN hospitals ON users.hospital_name = hospitals.name 
        WHERE users.user_id = '$user_id'
    ";
    $hospital_result = mysqli_query($conn, $hospital_query);

    if (!$hospital_result || mysqli_num_rows($hospital_result) === 0) {
        die("Failed to fetch admin's hospital information: " . mysqli_error($conn));
    }

    $hospital_row = mysqli_fetch_assoc($hospital_result);
    $admin_hospital_id = $hospital_row['hospital_id'];
}

// Delete doctor if the delete_id is set
if (isset($_GET['delete_id'])) {
    // Sanitize the delete_id value to prevent SQL injection
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);

    // Query to delete the doctor based on email
    $delete_query = "DELETE FROM doctors WHERE email = '$delete_id'";

    // Execute the delete query
    if (mysqli_query($conn, $delete_query)) {
        // Redirect to the same page to refresh the list
        header("Location: view_doctor.php");
        exit();
    } else {
        die("Error deleting doctor: " . mysqli_error($conn));
    }
}

// Query to fetch doctors' information
if ($is_superadmin) {
    // Superadmin can view all doctors, including hospital_name
    $query = "
        SELECT 
            doctors.fullname, 
            doctors.email, 
            doctors.phone, 
            specializations.name AS specialty,
            hospitals.name AS hospital_name
        FROM 
            doctors 
        JOIN 
            specializations ON doctors.specialty = specializations.id 
        JOIN 
            hospitals ON specializations.hospital_id = hospitals.hospital_id
    ";
} else {
    // Admin can only view doctors from their own hospital
    $query = "
        SELECT 
            doctors.fullname, 
            doctors.email, 
            doctors.phone, 
            specializations.name AS specialty 
        FROM 
            doctors 
        JOIN 
            specializations ON doctors.specialty = specializations.id 
        WHERE 
            specializations.hospital_id = '$admin_hospital_id'
    ";
}

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
    <title>HealthQ || View Doctors</title>
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
        }

        .delete-btn:hover {
            color: darkred;
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
            <?php
                if (!$is_superadmin) {
                echo '<li><a href="add_doctor.php">Add Doctor</a></li>';
                }
            ?>
            <span class="login-container">
                <li><a href="logout.php">Logout</a></li>
            </span>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <div class="info">
            <!-- Doctors Information Table -->
            <div class="appointments-table">
                <h2>Doctors' Information</h2>
                <div class="table-container">
                    <?php
                    // Check if there are any doctors' information to display
                    if (mysqli_num_rows($result) > 0) {
                        echo "<table>
                                <tr>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Phone No.</th>
                                    <th>Specialty</th>";
                        if ($is_superadmin) {
                            echo "<th>Hospital Name</th>";
                        }
                        if (!$is_superadmin) {
                            echo "<th>Actions</th>";
                        }
                        echo "</tr>";
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr id='doctor-" . htmlspecialchars($row['email']) . "'>
                                    <td>" . htmlspecialchars($row['fullname']) . "</td>
                                    <td>" . htmlspecialchars($row['email']) . "</td>
                                    <td>" . htmlspecialchars($row['phone']) . "</td>
                                    <td>" . htmlspecialchars($row['specialty']) . "</td>";
                            if ($is_superadmin) {
                                echo "<td>" . htmlspecialchars($row['hospital_name']) . "</td>";
                            }
                            if (!$is_superadmin) {
                                echo "<td><a href='?delete_id=" . urlencode($row['email']) . "' class='delete-btn'>Delete</a></td>";
                            }
                            echo "</tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "<p>No doctors found.</p>";
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