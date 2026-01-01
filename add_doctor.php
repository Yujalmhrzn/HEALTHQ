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

// Start session to check user login
session_start();
if (!isset($_SESSION['user_id'])) {
    die("Error: User not logged in. Please log in.");
}

$user_id = $_SESSION['user_id'];

// Get the hospital name for the logged-in user
$hospital_query = "SELECT hospital_name FROM users WHERE user_id = '$user_id'";
$hospital_result = mysqli_query($conn, $hospital_query);

if ($hospital_result && mysqli_num_rows($hospital_result) > 0) {
    $hospital_data = mysqli_fetch_assoc($hospital_result);
    $hospital_name = $hospital_data['hospital_name'];
} else {
    die("Error: Could not determine hospital for the logged-in user.");
}

// Use the hospital name to fetch the hospital ID from the hospitals table
$hospital_id_query = "SELECT hospital_id FROM hospitals WHERE name = '$hospital_name'";
$hospital_id_result = mysqli_query($conn, $hospital_id_query);

if ($hospital_id_result && mysqli_num_rows($hospital_id_result) > 0) {
    $hospital_id_data = mysqli_fetch_assoc($hospital_id_result);
    $hospital_id = $hospital_id_data['hospital_id'];
} else {
    die("Error: No matching hospital found in the database.");
}

// Fetch all specializations for the hospital
$specializations_query = "
    SELECT id, name 
    FROM specializations 
    WHERE hospital_id = '$hospital_id'";
$specializations_result = mysqli_query($conn, $specializations_query);

if (!$specializations_result) {
    die("Query failed: " . mysqli_error($conn));
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize input data
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $specialty = mysqli_real_escape_string($conn, $_POST['specialty']);

    // Validate selected specialty ID
    $valid_specialty_query = "SELECT id FROM specializations WHERE id = '$specialty' AND hospital_id = '$hospital_id'";
    $valid_specialty_result = mysqli_query($conn, $valid_specialty_query);
    
    if (!$valid_specialty_result || mysqli_num_rows($valid_specialty_result) == 0) {
        echo "Error: Invalid specialty selected.<br>";
        exit();
    }

    // Insert the doctor information into the 'doctors' table
    $insert_query = "
        INSERT INTO doctors (fullname, email, phone, specialty, hospital_id) 
        VALUES ('$fullname', '$email', '$phone', '$specialty', '$hospital_id')";

    if (mysqli_query($conn, $insert_query)) {
        // Redirect to the page that lists doctors if the insertion is successful
        header("Location: view_doctor.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Close the database connection
mysqli_close($conn);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthQ || Add Doctor</title>
    <style>
             /* Global Reset */
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
    flex: 1;
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

.login-container {
    margin-left: auto;
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

/*--- Footer Styling --- */
footer {
    background-color: #51c8e6;
    padding: 20px;
    text-align: center;
    color: #fff;
    font-size: 20px;
}

/*--- Main Content Styling --- */
.container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh; /* Full viewport height */
    padding: 30px;
    background-color: #f8f9fa;
}

.form-container {
    width: 100%;
    max-width: 650px;
    padding: 40px;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.form-container:hover {
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
}

.form-container label {
    font-size: 1.2rem;
    color: #495057;
    margin-bottom: 5px;
    display: block;
}

.form-container input,
.form-container select {
    width: 100%;
    padding: 12px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 1rem;
    background-color: #f1f3f5;
    color: #495057;
    transition: border 0.3s ease;
}

.form-container input:focus,
.form-container select:focus {
    border-color: #007bff;
    outline: none;
}

.form-container input[type="submit"] {
    background-color: #007bff;
    color: white;
    font-weight: bold;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    padding: 14px 20px;
    font-size: 1.1rem;
    transition: background-color 0.3s ease;
}

.form-container input[type="submit"]:hover {
    background-color: #0056b3;
}

.form-container input[type="submit"]:active {
    background-color: #004085;
    transform: scale(0.98);
}

/* Centering the heading */
.form-container h2 {
    text-align: center; /* Centers the heading */
    font-size: 2rem;
    color: #495057;
    margin-bottom: 30px;
}

/* Responsive Design */
@media screen and (max-width: 768px) {
    .form-container {
        padding: 30px;
    }

    h2 {
        font-size: 2rem;
    }

    .form-container label {
        font-size: 1rem;
    }

    .form-container input,
    .form-container select {
        font-size: 0.9rem;
        padding: 10px;
    }
}
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav>
        <ul>
            <li><a href="view_doctor.php">View Doctors</a></li>
            <span class="login-container">
                <li><a href="logout.php">Logout</a></li>
            </span>
        </ul>
    </nav>

    <!-- Add Doctor Form -->
    <div class="container">
        <div class="form-container">
            <h2>Add New Doctor</h2>
            <form method="POST" action="add_doctor.php">
                <label for="fullname">Full Name:</label>
                <input type="text" id="fullname" name="fullname" required><br><br>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required><br><br>

                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" required><br><br>

                <label for="specialty">Specialty:</label>
                <select id="specialty" name="specialty" required>
                    <option value="">Select Specialty</option>
                    <?php while ($row = mysqli_fetch_assoc($specializations_result)) { ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                    <?php } ?>
                </select><br><br>

                <input type="submit" value="Add Doctor">
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024, All Rights Reserved,<br>Design &amp; Designed by: <b>HealthQ</b></p>
    </footer>
</body>
</html>
