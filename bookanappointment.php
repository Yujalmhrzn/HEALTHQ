<?php
// Start the session
session_start();

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "healthq";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch hospitals from the database
$hospitalQuery = "SELECT hospital_id, name FROM hospitals";
$hospitalsResult = $conn->query($hospitalQuery);

// Fetch specializations from the database
$specializationsQuery = "SELECT id, hospital_id, name FROM specializations";
$specializationsResult = $conn->query($specializationsQuery);

// Create an associative array to store specializations based on hospital_id
$specializations = [];
if ($specializationsResult->num_rows > 0) {
    while ($row = $specializationsResult->fetch_assoc()) {
        $specializations[$row['hospital_id']][] = $row['name'];
    }
}

$selectedHospital = '';
$selectedSpecialization = '';
$fullname = '';
$phone = '';
$date_time = '';
$message = '';
$messageClass = '';

// Get the logged-in user's ID
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Ensure the user is logged in
if (!$user_id) {
    echo "<script>alert('Please log in to book an appointment.'); window.location.href = 'login.php';</script>";
    exit();
}

// Check if the user_id exists in the users table
$userCheckQuery = "SELECT user_id FROM users WHERE user_id = ?";
$stmt = $conn->prepare($userCheckQuery);
if ($stmt === false) {
    die('Error preparing query: ' . $conn->error);
}

$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    echo "<script>alert('Invalid user ID. Please log in again.'); window.location.href = 'login.php';</script>";
    exit();
}

// If form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $selectedHospital = $_POST['hospital'];
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $date_time = $_POST['date_time'];
    $selectedSpecialization = $_POST['specialization'];

    // Validate phone number
    if (!preg_match('/^\d{10}$/', $phone)) {
        $message = "Please enter a valid 10-digit phone number.";
        $messageClass = 'error';
    } elseif (empty($fullname) || empty($phone) || empty($date_time) || empty($selectedHospital) || empty($selectedSpecialization)) {
        $message = "Please fill in all fields.";
        $messageClass = 'error';
    } else {
        $query = "INSERT INTO appointment (user_id, fullname, phone, date_time, hospital_id, specialization) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);

        if ($stmt === false) {
            $message = "Error preparing query: " . $conn->error;
            $messageClass = 'error';
        } else {
            if (!$stmt->bind_param("isssis", $user_id, $fullname, $phone, $date_time, $selectedHospital, $selectedSpecialization)) {
                $message = "Error binding parameters: " . $stmt->error;
                $messageClass = 'error';
            } else {
                if ($stmt->execute()) {
                    $message = "Appointment booked successfully!";
                    $messageClass = 'success';
                    // Redirect to viewappointments.php after 3 seconds
                    header("refresh:3;url=viewappointments.php");
                } else {
                    $message = "Error executing the query: " . $stmt->error;
                    $messageClass = 'error';
                }
            }
            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthQ || Book Appointment</title>
    <link rel="stylesheet" href="bookanappointment.css">
    <script>
        function updateSpecializations() {
            const selectedHospital = document.getElementById('hospital').value;
            const specializationSelect = document.getElementById('specialization');
            specializationSelect.innerHTML = '<option value="">Select Specialization</option>';
            const specializations = <?php echo json_encode($specializations); ?>;
            if (specializations[selectedHospital]) {
                specializations[selectedHospital].forEach(function(specialization) {
                    const option = document.createElement('option');
                    option.value = specialization;
                    option.textContent = specialization;
                    specializationSelect.appendChild(option);
                });
            }
        }

        function setMinDateTime() {
            const dateTimeInput = document.getElementById('date_time');
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            dateTimeInput.min = `${year}-${month}-${day}T${hours}:${minutes}`;
        }

        window.onload = setMinDateTime;
    </script>
    <style>
        .message {
            margin: 15px 0;
            padding: 10px;
            border-radius: 5px;
            font-size: 14px;
            text-align: center;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <nav>
        <ul>
            <li><a href="user_dashboard.php">Dashboard</a></li>
            <span class="login-container">
                <li><a href="logout.php">Logout</a></li>
            </span>
        </ul>
    </nav>
    <div class="container">
        <div class="info">
            <div class="appointment-form">
                <h2>Book an Appointment</h2>
                <?php if (!empty($message)): ?>
                    <div class="message <?php echo $messageClass; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                <form method="POST" action="bookanappointment.php">
                    <div class="form-group">
                        <label for="fullname">Full Name:</label>
                        <input type="text" id="fullname" name="fullname" value="<?php echo $fullname; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number:</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo $phone; ?>" pattern="\d{10}" title="Please enter a valid 10-digit phone number" required>
                    </div>
                    <div class="form-group">
                        <label for="date_time">Appointment Date and Time:</label>
                        <input type="datetime-local" id="date_time" name="date_time" value="<?php echo $date_time; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="hospital">Hospital:</label>
                        <select id="hospital" name="hospital" onchange="updateSpecializations()" required>
                            <option value="">Select a Hospital</option>
                            <?php if ($hospitalsResult->num_rows > 0): ?>
                                <?php while ($hospital = $hospitalsResult->fetch_assoc()): ?>
                                    <option value="<?php echo $hospital['hospital_id']; ?>" <?php echo ($selectedHospital == $hospital['hospital_id']) ? 'selected' : ''; ?>>
                                        <?php echo $hospital['name']; ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="specialization">Specialization:</label>
                        <select id="specialization" name="specialization" required>
                            <option value="">Select Specialization</option>
                            <?php
                                if ($selectedHospital && isset($specializations[$selectedHospital])) {
                                    foreach ($specializations[$selectedHospital] as $spec) {
                                        echo "<option value=\"$spec\" " . ($selectedSpecialization == $spec ? 'selected' : '') . ">$spec</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="btn">Book Appointment</button>
                </form>
            </div>
        </div>
    </div>
    <footer>
        <p>&copy; 2024, All Rights Reserved,<br>Design &amp; Designed by: <b>HealthQ</b></p>
    </footer>
</body>
</html>
