<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get logged-in user's ID
$user_id = $_SESSION['user_id'];

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "healthq";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize message variable
$message = "";

// Handle deletion of appointment
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']); // Ensure the ID is an integer

    // Prepare the delete query
    $delete_query = "DELETE FROM appointment WHERE appointment_id = ? AND user_id = ?";
    $stmt = $conn->prepare($delete_query);
    if ($stmt) {
        $stmt->bind_param("ii", $delete_id, $user_id);
        if ($stmt->execute()) {
            $message = "Appointment cancelled successfully.";
        } else {
            $message = "Error cancelling appointment.";
        }
        $stmt->close();
    } else {
        $message = "Error preparing the delete query: " . $conn->error;
    }
}

// Query to fetch appointments and doctor details
$query = "
    SELECT 
        appointment.appointment_id, 
        appointment.fullname AS patient_name, 
        appointment.phone AS patient_phone, 
        appointment.date_time, 
        appointment.status, 
        specializations.name AS specialization_name, 
        hospitals.name AS hospital_name, 
        doctors.fullname AS doctor_name,
        rejected.feedback AS admin_feedback
    FROM 
        appointment
    LEFT JOIN 
        specializations ON appointment.specialization = specializations.name
    LEFT JOIN 
        doctors ON specializations.id = doctors.specialty
    LEFT JOIN 
        hospitals ON appointment.hospital_id = hospitals.hospital_id
    LEFT JOIN 
        rejected ON appointment.appointment_id = rejected.appointment_id
    WHERE 
        appointment.user_id = ?;
";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Appointments</title>
    <link rel="stylesheet" href="view.css">
    <style>
        .back-btn {
            background-color: #007BFF;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            position: absolute;
            top: 20px;
            right: 20px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .back-btn:hover {
            background-color: #005f99;
            transform: scale(1.05);
        }

        .container {
            padding: 20px;
            margin-bottom: 100px; /* Space for the fixed footer */
        }

        .appointments-table {
            margin-top: 60px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .table-container {
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        .delete-btn {
            color: red;
            text-decoration: none;
        }

        .message {
            color: green;
            text-align: center;
            font-weight: bold;
            margin-top: 10px;
            opacity: 1;
            transition: opacity 0.5s ease-in-out;
        }

        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #51c8e;
            color: white;
            text-align: center;
            padding: 10px 0;
            font-size: 14px;
            z-index: 1000;
        }

        footer p {
            margin: 0;
        }
    </style>
</head>
<body>
    <!-- Back Button -->
    <a href="user_dashboard.php" class="back-btn">Back</a>

    <div class="container">
        <div class="appointments-table">
            <h2>Your Booked Appointments</h2>
            <!-- Show the message below the heading -->
            <?php if (!empty($message)): ?>
                <div id="message" class="message"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <div class="table-container">
                <?php if ($result->num_rows > 0): ?>
                    <table>
                        <tr>
                            <th>Appointment ID</th>
                            <th>Patient Name</th>
                            <th>Phone</th>
                            <th>Date & Time</th>
                            <th>Specialization</th>
                            <th>Hospital</th>
                            <th>Doctor Name</th>
                            <th>Status</th>
                            <th>Admin Feedback</th>
                            <th>Actions</th>
                        </tr>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['appointment_id']) ?></td>
                                <td><?= htmlspecialchars($row['patient_name']) ?></td>
                                <td><?= htmlspecialchars($row['patient_phone']) ?></td>
                                <td><?= htmlspecialchars($row['date_time']) ?></td>
                                <td><?= htmlspecialchars($row['specialization_name'] ?: 'Not Specified') ?></td>
                                <td><?= htmlspecialchars($row['hospital_name']) ?></td>
                                <td><?= htmlspecialchars($row['doctor_name'] ?: 'Not Assigned') ?></td>
                                <td><?= htmlspecialchars($row['status']) ?></td>
                                <td><?= htmlspecialchars($row['admin_feedback'] ?: 'N/A') ?></td>
                                <td>
                                    <?php if ($row['status'] == 'pending'): ?>
                                        <a href="?delete_id=<?= htmlspecialchars($row['appointment_id']) ?>" class="delete-btn">Cancel</a>
                                    <?php else: ?>
                                        <span>N/A</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                <?php else: ?>
                    <p>No appointments found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <footer>
        <p>&copy; 2024, All Rights Reserved,<br>Design &amp; Designed by: <b>HealthQ</b></p>
    </footer>

    <script>
        // Hide the message after 3-4 seconds
        window.addEventListener('DOMContentLoaded', () => {
            const messageDiv = document.getElementById('message');
            if (messageDiv) {
                setTimeout(() => {
                    messageDiv.style.opacity = '0';
                }, 3000); // 3 seconds
                setTimeout(() => {
                    messageDiv.style.display = 'none';
                }, 4000); // 4 seconds
            }
        });
    </script>
</body>
</html>

<?php
// Close statement and connection
$stmt->close();
$conn->close();
?>
