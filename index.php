<?php
// Database connection
$servername = "localhost:3307";
$username = "root";
$password = "";
$dbname = "UniversityRegistration";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);  // Enable MySQLi error reporting

// ===== FORM SUBMISSION HANDLING CODE =====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture form data
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $birthdate = $_POST['birthdate'];
    $gender = $_POST['gender'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $nationality = $_POST['nationality'];
    $guardian_first_name = $_POST['guardian_first_name'];
    $guardian_last_name = $_POST['guardian_last_name'];
    $guardian_relation = $_POST['guardian_relation'];
    $guardian_occupation = $_POST['guardian_occupation'];
    $guardian_contact_number = $_POST['guardian_contact_number'];
    $shs_name = $_POST['shs_name'];
    $wassce_index_number = $_POST['wassce_index_number'];
    $country = $_POST['country'];
    $region = $_POST['region'];
    $house_address = $_POST['house_address'];
    $programme = $_POST['programme'];  // This is programme_id in the DB
    $session = $_POST['session'];
    $consent = isset($_POST['consent']) ? 1 : 0;

    // Debugging print (optional)
    print_r([$first_name, $last_name, $birthdate, $programme, $session]);

    // Start transaction
    $conn->begin_transaction();

    // Insert guardian data into the guardian table
    $guardian_stmt = $conn->prepare("INSERT INTO guardian 
    (guardian_first_name, guardian_last_name, guardian_relation, guardian_occupation, guardian_contact_number) 
    VALUES (?, ?, ?, ?, ?)");
    $guardian_stmt->bind_param("sssss", $guardian_first_name, $guardian_last_name, $guardian_relation, $guardian_occupation, $guardian_contact_number);

    if ($guardian_stmt->execute()) {
        $guardian_id = $conn->insert_id; // Get the newly inserted guardian_id
    } else {
        echo "Error inserting guardian: " . $guardian_stmt->error;
        exit;
    }

    // Insert address data into the address table
    $address_stmt = $conn->prepare("INSERT INTO address (country, region, house_address) VALUES (?, ?, ?)");
    $address_stmt->bind_param("sss", $country, $region, $house_address);

    if ($address_stmt->execute()) {
        $address_id = $conn->insert_id; // Get the newly inserted address_id
    } else {
        echo "Error inserting address: " . $address_stmt->error;
        exit;
    }

    // Insert application data with guardian_id and address_id
    $application_stmt = $conn->prepare("INSERT INTO application (
        first_name, middle_name, last_name, birthdate, gender, phone_number, email, nationality, 
        shs_name, wassce_index_number, programme_id, session, consent_to_keep_data, guardian_id, address_id
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $application_stmt->bind_param(
        "ssssssssssissii",
        $first_name, $middle_name, $last_name, $birthdate, $gender, $phone_number, $email, 
        $nationality, $shs_name, $wassce_index_number, $programme, $session, $consent, $guardian_id, $address_id
    );

    if ($application_stmt->execute()) {
        // Commit the transaction
        $conn->commit();
        $application_stmt->close();

        header("Location: index.php?success=1");
        exit;
    } else {
        echo "Error inserting application: " . $application_stmt->error;
        $conn->rollback(); // Rollback on error
        exit;
    }
}

$conn->close();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Registration</title>
    <link rel="stylesheet" href="/css/reg.css">
</head>
<body>

<?php include 'header.php'; ?>
<?php
// Check if the 'success' query parameter is present
if (isset($_GET['success']) && $_GET['success'] == 1) {
    echo '<div class="success-banner" id="success-banner">
            <span>Application sent successfully!</span>
            <button class="close" onclick="closeBanner()">X</button>
          </div>';
}
?>

<div class="container">
    <h1>University Registration Form</h1>
    <form action="index.php" method="POST">
    <h2>Student Information</h2>
    <label for="first_name">First Name:</label>
    <input type="text" id="first_name" name="first_name" required>

    <label for="middle_name">Middle Name:</label>
    <input type="text" id="middle_name" name="middle_name">

    <label for="last_name">Last Name:</label>
    <input type="text" id="last_name" name="last_name" required>

    <label for="birthdate">Birthdate:</label>
    <input type="date" id="birthdate" name="birthdate" required>

    <label for="gender">Gender:</label>
    <select id="gender" name="gender" required>
        <option value="">Select Gender</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
    </select>

    <label for="phone_number">Phone Number:</label>
    <input type="tel" id="phone_number" name="phone_number" required>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>

    <label for="nationality">Nationality:</label>
    <input type="text" id="nationality" name="nationality" required>

    <h2>Guardian Information</h2>
    <label for="guardian_first_name">Guardian First Name:</label>
    <input type="text" id="guardian_first_name" name="guardian_first_name" required>

    <label for="guardian_last_name">Guardian Last Name:</label>
    <input type="text" id="guardian_last_name" name="guardian_last_name" required>

    <label for="guardian_relation">Guardian's Relation:</label>
    <input type="text" id="guardian_relation" name="guardian_relation" required>

    <label for="guardian_occupation">Guardian Occupation:</label>
    <input type="text" id="guardian_occupation" name="guardian_occupation" required>

    <label for="guardian_contact_number">Guardian Contact Number:</label>
    <input type="tel" id="guardian_contact_number" name="guardian_contact_number" required>

    <h2>SHS Information</h2>
    <label for="shs_name">SHS Name:</label>
    <input type="text" id="shs_name" name="shs_name" required>

    <label for="wassce_index_number">WASSCE Index Number:</label>
    <input type="text" id="wassce_index_number" name="wassce_index_number" required>

    <h2>Address</h2>
    <label for="country">Country:</label>
    <select id="country" name="country" required>
        <option value="">Select Country</option>
        <option value="ghana">Ghana</option>
        <option value="nigeria">Nigeria</option>
    </select>

    <label for="region">Region:</label>
    <input type="text" id="region" name="region" required>

    <label for="house_address">House Address:</label>
    <input type="text" id="house_address" name="house_address" required>

    <h2>Program</h2>
    <label for="programme">Programme:</label>
    <select id="programme" name="programme" required>
        <option value="">Select Programme</option>
        <option value="1">BSc Management</option>
        <option value="2">BSc Computer Science</option>
        <option value="3">BA Communication</option>
    </select>


    <label for="session">Session Type:</label>
    <select id="session" name="session" required>
        <option value="">Select Session</option>
        <option value="Morning Session">Morning Session</option>
        <option value="Evening Session">Evening Session</option>
    </select>

    <div class="checkbox-container">
        <input type="checkbox" id="consent" name="consent" value="yes">
        <label for="consent">Do you agree to let us keep your data if your application is rejected?</label>
    </div>

    <input type="submit" value="Register">
</form>

</div>
<script>
    function toggleMenu() {
        const navLinks = document.getElementById('nav-links');
        if (navLinks.style.display === 'flex') {
            navLinks.style.display = 'none';
        } else {
            navLinks.style.display = 'flex';
        }
    }

    // Function to close the banner
    function closeBanner() {
        document.getElementById('success-banner').classList.add('hidden');
    }
</script>
</body>
</html>
