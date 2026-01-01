<?php
// Start the session to access the logged-in user's session data
session_start();

// Check if the user is logged in. If not, redirect to login page.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if user is not logged in
    exit();
}

// Fetch user data from session
$user_id = $_SESSION['user_id'];
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : "User"; // Default to "User" if not set
?>

<!doctype html>
<html lang="en">
<head>
    <title>HealthQ || Home Page</title>
    <link rel="stylesheet" href="user_dashboard.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav>
        <ul>
            <li>Dashboard</li>
            <span class="login-container">
                <li><a href="logout.php">Logout</a></li>
            </span>
        </ul>
    </nav>
    
    <!-- Welcome Message Below Navbar -->
    <div class="welcome-message">
        <h2>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h2>
    </div>
    
    <!-- Main Content -->
    <div class="container">
        <div class="info">          
            <!-- Book an Appointment -->
            <div class="pop_count">
                <h2>Book an Appointment</h2>
                <button type="button" onclick="document.location='bookanappointment.php'" class="pop_count_button">Click Here</button>
            </div>

            <!-- View Appointments -->
            <div class="pop_count">
                <h2>View Appointments</h2>
                <button type="button" onclick="document.location='viewappointments.php'" class="pop_count_button">Click Here</button>
            </div>
        </div>
    </div>

    <!-- Footer Section -->
    <footer>
        <p>&copy; 2024, All Rights Reserved,<br>Design &amp; Designed by: <b>HealthQ</b></p>
    </footer>
</body>
</html>
