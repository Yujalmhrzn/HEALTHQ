<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "healthq";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get hospital_id from request
$hospital_id = $_GET['hospital_id'];

// Fetch specializations for the selected hospital
$query = "SELECT name FROM specializations WHERE hospital_id = $hospital_id";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    echo '<option value="">Select Specialization</option>';
    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . $row['name'] . '">' . $row['name'] . '</option>';
    }
} else {
    echo '<option value="">No specializations available</option>';
}

$conn->close();
?>
