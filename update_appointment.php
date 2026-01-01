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

if (isset($_POST['appointment_id'])) {
    $appointment_id = $_POST['appointment_id'];
    $status = '';

    // Check if the user clicked approve or reject
    if (isset($_POST['approve'])) {
        $status = 'approved';
    } elseif (isset($_POST['reject'])) {
        $status = 'rejected';
    }

    // Update the appointment status
    if ($status != '') {
        $query = "UPDATE appointment SET status = '$status' WHERE id = '$appointment_id'";

        if (mysqli_query($conn, $query)) {
            echo "<script> alert('Appointment status updated to $status.');
            window.location.href = 'admin_dashboard.php'; // Redirect to dashboard
            </script>";
        } else {
            echo "Error updating status: " . mysqli_error($conn);
        }
    }
}

// Close connection
mysqli_close($conn);
?>
