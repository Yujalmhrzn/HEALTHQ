<!doctype html>
<html lang="en">
<head>
    <title>HealthQ || Admin</title>
    <link rel="stylesheet" href="admin_dashboard.css">
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
            <!-- Total Doctors Display -->
            <div class="pop_count">
                <h2>Total Doctors:
                    <?php
                    session_start(); // Start session to access user_id
                    
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $dbname = "healthq";

                    // Establish connection
                    $conn = mysqli_connect($servername, $username, $password, $dbname);

                    if (!$conn) {
                        die("Connection failed: " . mysqli_connect_error());
                    }

                    if (isset($_SESSION['user_id'])) {
                        $logged_in_user = $_SESSION['user_id'];

                        if ($logged_in_user == 1) {
                            // Special case for user_id = 1
                            $query = "SELECT COUNT(*) AS doc_count FROM doctors";
                            $result = mysqli_query($conn, $query);
                            if ($result) {
                                $row = mysqli_fetch_assoc($result);
                                echo $row['doc_count'];
                            } else {
                                echo "Query failed: " . mysqli_error($conn);
                            }
                        } else {
                            // For other admins, count doctors in their hospital
                            $query = "SELECT hospital_name FROM users WHERE user_id='$logged_in_user'";
                            $result = mysqli_query($conn, $query);
                            if ($result && mysqli_num_rows($result) > 0) {
                                $row = mysqli_fetch_assoc($result);
                                $admin_hospital_name = $row['hospital_name'];

                                // Fetch hospital_id for the admin's hospital_name
                                $query = "SELECT hospital_id FROM hospitals WHERE name='$admin_hospital_name'";
                                $result = mysqli_query($conn, $query);
                                if ($result && mysqli_num_rows($result) > 0) {
                                    $row = mysqli_fetch_assoc($result);
                                    $admin_hospital_id = $row['hospital_id'];

                                    // Count doctors associated with the admin's hospital_id
                                    $query = "SELECT COUNT(*) AS doc_count 
                                              FROM doctors d 
                                              JOIN specializations s ON d.specialty = s.id
                                              WHERE s.hospital_id = '$admin_hospital_id'";
                                    $result = mysqli_query($conn, $query);
                                    if ($result) {
                                        $row = mysqli_fetch_assoc($result);
                                        echo $row['doc_count'];
                                    } else {
                                        echo "Query failed: " . mysqli_error($conn);
                                    }
                                } else {
                                    echo "No hospital found for the admin.";
                                }
                            } else {
                                echo "No hospital associated with the admin.";
                            }
                        }
                    } else {
                        echo "User is not logged in.";
                    }

                    mysqli_close($conn);
                    ?>
                </h2>
                <button type="button" onclick="document.location='view_doctor.php'" class="pop_count_button">View Doctors</button>
            </div>

            <!-- Total Users Display -->
            <div class="pop_count">
                <h2>Total Users:
                    <?php
                    $conn = mysqli_connect($servername, $username, $password, $dbname);
                    $query = "SELECT COUNT(*) AS patients FROM users WHERE type='user'";
                    $result = mysqli_query($conn, $query);
                    if ($result) {
                        $row = mysqli_fetch_assoc($result);
                        echo $row['patients'];
                    } else {
                        echo "Query failed: " . mysqli_error($conn);
                    }
                    ?>
                </h2>
                <button type="button" onclick="document.location='viewuser.php'" class="pop_count_button">View Users</button>
            </div>

            <!-- Total Admins Display -->
            <div class="pop_count">
                <h2>Total Admins:
                    <?php
                    $query = "SELECT COUNT(*) AS count FROM users WHERE type='admin'";
                    $result = mysqli_query($conn, $query);
                    if ($result) {
                        $row = mysqli_fetch_assoc($result);
                        echo $row['count'];
                    } else {
                        echo "Query failed: " . mysqli_error($conn);
                    }
                    ?>
                </h2>
                <button type="button" onclick="document.location='view_admins.php'" class="pop_count_button">View Admins</button>
            </div>
        </div>
    </div>

    <!-- Footer Section -->
    <footer>
        <p>&copy; 2025, All Rights Reserved,<br>Design &amp; Designed by: <b>HealthQ</b></p>
    </footer>
</body>
</html>
