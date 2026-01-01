<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "healthq";

// Start session
session_start();

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Initialize variables for messages
$status_message = "";
$feedback_form = false;
$current_appointment_id = null;

// Fetch the hospital name dynamically for the logged-in admin
$logged_in_admin_id = $_SESSION['user_id']; // Assume admin ID is stored in the session
$hospital_query = "SELECT hospital_name FROM users WHERE user_id = '$logged_in_admin_id'";
$hospital_result = mysqli_query($conn, $hospital_query);

if ($hospital_result && mysqli_num_rows($hospital_result) > 0) {
    $hospital_data = mysqli_fetch_assoc($hospital_result);
    $logged_in_admin_hospital_name = $hospital_data['hospital_name'];
} else {
    die("Error: Unable to fetch the hospital name for the logged-in admin.");
}

// Process the appointment status update
if (isset($_POST['update_status'])) {
    $appointment_id = $_POST['appointment_id'];
    $status = $_POST['status'];

    // Fetch appointment and hospital details
    $fetch_query = "
        SELECT a.*, h.name AS hospital_name 
        FROM appointment a
        JOIN hospitals h ON a.hospital_id = h.hospital_id
        WHERE a.appointment_id = '$appointment_id'
    ";
    $appointment_result = mysqli_query($conn, $fetch_query);

    if ($appointment_result && mysqli_num_rows($appointment_result) > 0) {
        $appointment_data = mysqli_fetch_assoc($appointment_result);

        // Check if the logged-in admin has permission
        if ($appointment_data['hospital_name'] === $logged_in_admin_hospital_name) {
            if ($status == 'approved') {
                // Insert into the approved table
                $insert_query = "
                    INSERT INTO approved (appointment_id, fullname, phone, date_time, specialization, status)
                    VALUES (
                        '{$appointment_data['appointment_id']}',
                        '{$appointment_data['fullname']}',
                        '{$appointment_data['phone']}',
                        '{$appointment_data['date_time']}',
                        '{$appointment_data['specialization']}',
                        'approved'
                    )
                ";
                if (mysqli_query($conn, $insert_query)) {
                    // Update the `appointment` table
                    $update_query = "UPDATE appointment SET status='approved' WHERE appointment_id='$appointment_id'";
                    mysqli_query($conn, $update_query);
                    $status_message = "Appointment Approved";
                } else {
                    $status_message = "Error: " . mysqli_error($conn);
                }
            } elseif ($status == 'cancelled') {
                // Show feedback form
                $feedback_form = true;
                $current_appointment_id = $appointment_id;
            }
        } else {
            $status_message = "Error: You do not have permission to update this appointment.";
        }
    } else {
        $status_message = "Error: Appointment not found.";
    }
}

// Handle feedback submission
if (isset($_POST['send_feedback'])) {
    $appointment_id = $_POST['appointment_id'];
    $feedback = $_POST['feedback'];

    // Insert into rejected table
    $insert_query = "
        INSERT INTO rejected (appointment_id, fullname, phone, date_time, specialization, status, feedback)
        SELECT appointment_id, fullname, phone, date_time, specialization, 'rejected', '$feedback'
        FROM appointment
        WHERE appointment_id='$appointment_id'
    ";
    if (mysqli_query($conn, $insert_query)) {
        // Update the `appointment` table
        $update_query = "UPDATE appointment SET status='rejected' WHERE appointment_id='$appointment_id'";
        mysqli_query($conn, $update_query);
        $status_message = "Appointment Rejected";
    } else {
        $status_message = "Error: " . mysqli_error($conn);
    }
}

// Query to get appointments for the admin's hospital
$query = "
    SELECT a.appointment_id, a.date_time, a.fullname, a.phone, a.specialization, a.status, h.name AS hospital_name
    FROM appointment a
    JOIN hospitals h ON a.hospital_id = h.hospital_id
    WHERE h.name = '$logged_in_admin_hospital_name'
";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Appointments - HealthQ</title>
    <style>
        * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

/*--- Navigation Styling --- */
nav {
    background-color: #f0f8ff;
    padding: 15px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    position: sticky;
    top: 0;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

nav ul {
    display: flex;
    align-items: center;
    list-style: none;
    flex: 1; /* Allows spacing control */
}

nav ul li {
    margin: 0 15px;
    font-size: 20px;
    font-weight: 600;
    color: #333;
    transition: color 0.3s ease, transform 0.3s ease;
}

nav ul li a {
    text-decoration: none;
    color: #333;
}

nav ul li a:hover {
    color: #007acc;
    transform: scale(1.1);
}

/*--- Logout Button Alignment (Right) --- */
.login-container {
    margin-left: auto; /* Pushes logout to the right */
}

.login-container a {
    font-size: 20px;
    font-weight: 600;
    color: #fff;
    padding: 8px 16px;
    background-color: #007acc;
    border-radius: 5px;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.login-container a:hover {
    background-color: #005f99;
    color: #000;
    transform: scale(1.05);
}

/*--- Footer Styling (Fixed at the Bottom) --- */
footer {
    background-color: #51c8e6;
    padding: 20px;
    text-align: center;
    color: #fff;
    font-size: 20px;
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    z-index: 1000;
}

/*--- Main Content and Info Blocks Styling --- */
.container {
    display: flex;
    justify-content: center;
    padding: 30px;
    padding-bottom: 80px; /* Adding space to ensure content doesn't overlap footer */
}

.info {
    display: flex;
    gap: 20px;
    flex-wrap: wrap; /* Allows blocks to wrap on smaller screens */
}

/*--- Block Styles --- */
.pop_count {
    width: 300px;
    height: 350px; /* Increased height */
    padding: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    background-color: #f0f8ff;
    border: 2px solid #007acc;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.pop_count h2 {
    font-size: 1.5em;
    color: #333;
    margin-bottom: 15px;
}

/*--- Button Styles --- */
.pop_count_button {
    padding: 10px 25px;
    background-color: #007acc;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    font-weight: bold;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.pop_count_button:hover {
    background-color: #005f99;
    transform: scale(1.05);
}

/*--- Table Styling --- */
.appointments-table h2 {
    text-align: center;
    color: #333;
    margin-bottom: 20px;
    font-size: 28px;
}

.table-container table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.table-container th, .table-container td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.table-container th {
    background-color: #007acc;
    color: white;
    font-size: 16px;
}

.table-container td {
    font-size: 14px;
}

.table-container tr:hover {
    background-color: #f1f1f1;
}

.table-container p {
    text-align: center;
    font-size: 18px;
    color: #888;
}

/*--- Delete Button Styling --- */
.table-container td a {
    color: red;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    padding: 5px 10px;
    border-radius: 5px;
    background-color: rgba(255, 0, 0, 0.1);
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.table-container td a:hover {
    background-color: red;
    color: white;
    transform: scale(1.1);
}

/* Responsive Design */
@media screen and (max-width: 768px) {
    .appointments-table h2 {
        font-size: 24px;
    }

    .table-container table {
        font-size: 14px;
    }

    .table-container th, .table-container td {
        padding: 10px;
    }
}  

        /* Status Message Styling */
        .status-message {
            margin-top: 10px;
            padding: 10px;
            background-color: #007acc;
            color: white;
            font-size: 16px;
            text-align: center;
            border-radius: 5px;
            display: none; /* Initially hidden */
        }

        .status-message.show {
            display: block; /* Shown when the message needs to be visible */
        }
    </style>
</head>
<body>
<nav>
    <ul>
        <li><a href="admin_dashboard.php">Dashboard</a></li>
        <span class="login-container">
            <li><a href="logout.php">Logout</a></li>
        </span>
    </ul>
</nav>

<div class="container">
    <div class="appointments-table">
        <h2>All Appointments</h2>

        <?php if (!empty($status_message)): ?>
            <p class="status-message"><?= htmlspecialchars($status_message) ?></p>
        <?php endif; ?>

        <div class="table-container">
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Appointment ID</th>
                            <th>Patient Name</th>
                            <th>Phone No.</th>
                            <th>Appointment Date & Time</th>
                            <th>Doctor Specialization</th>
                            <th>Status</th>
                            <th>Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['appointment_id']) ?></td>
                                <td><?= htmlspecialchars($row['fullname']) ?></td>
                                <td><?= htmlspecialchars($row['phone']) ?></td>
                                <td><?= htmlspecialchars($row['date_time']) ?></td>
                                <td><?= htmlspecialchars($row['specialization']) ?></td>
                                <td><?= htmlspecialchars($row['status']) ?></td>
                                <td>
                                    <?php if ($row['status'] == 'pending'): ?>
                                        <form method="POST">
                                            <input type="hidden" name="appointment_id" value="<?= $row['appointment_id'] ?>">
                                            <select name="status">
                                                <option value="approved">Approve</option>
                                                <option value="cancelled">Cancel</option>
                                            </select>
                                            <input type="submit" name="update_status" value="Update">
                                        </form>
                                    <?php else: ?>
                                        No Action
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No appointments found for your hospital.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<footer>
    <p>&copy; 2024, All Rights Reserved, HealthQ</p>
</footer>
</body>
</html>
