<?php
// Database connection
$servername = "localhost:3307";
$username = "root";  // Your MySQL username
$password = "";  // Your MySQL password
$dbname = "UniversityRegistration";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch total number of students, males, and females
$sql_total_students = "SELECT COUNT(*) AS total_students FROM students";
$sql_male_students = "SELECT COUNT(*) AS male_students FROM students WHERE gender = 'Male'";
$sql_female_students = "SELECT COUNT(*) AS female_students FROM students WHERE gender = 'Female'";

$total_students = $conn->query($sql_total_students)->fetch_assoc()['total_students'];
$male_students = $conn->query($sql_male_students)->fetch_assoc()['male_students'];
$female_students = $conn->query($sql_female_students)->fetch_assoc()['female_students'];

// Search a student by student_id
// Search a student by student_id
$searched_student = null;
if (isset($_POST['search_student'])) {
    $student_id = $_POST['student_id'];
    $sql_student = "SELECT s.student_id, s.first_name, s.middle_name, s.last_name, s.birthdate, s.gender, s.phone_number,
    g.guardian_first_name, g.guardian_last_name, g.guardian_relation, g.guardian_occupation, g.guardian_contact_number,
    s.shs_name, s.wassce_index_number, s.email, s.nationality, s.session, ad.country, ad.region, ad.house_address, 
    s.student_level, f.amount_paid, s.registration_status, s.consent_to_keep_data, p.Name as programme_name
    FROM students s
    JOIN Programme p ON s.programme_id = p.Programme_id
    LEFT JOIN Fees f ON s.student_id = f.student_id
    JOIN guardian g ON s.guardian_id = g.guardian_id
    JOIN address ad ON s.address_id = ad.address_id
    WHERE s.student_id = ?";

    $stmt = $conn->prepare($sql_student);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $searched_student = $result->fetch_assoc();
    } else {
        $error = "No student found with Student ID: $student_id";
    }
    $stmt->close();
}


// Insert a new student
if (isset($_POST['insert_student'])) {
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $birthdate = $_POST['birthdate'];
    $gender = $_POST['gender'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $nationality = $_POST['nationality'];
    $programme_id = $_POST['programme'];
    $session = $_POST['session'];
    $shs_name = $_POST['shs_name'];
    $wassce_index_number = $_POST['wassce_index_number'];
    
    // Insert guardian data
    $guardian_first_name = $_POST['guardian_first_name'];
    $guardian_last_name = $_POST['guardian_last_name'];
    $guardian_relation = $_POST['guardian_relation'];
    $guardian_occupation = $_POST['guardian_occupation'];
    $guardian_contact_number = $_POST['guardian_contact_number'];
    
    $insert_guardian_sql = "INSERT INTO guardian (guardian_first_name, guardian_last_name, guardian_relation, guardian_occupation, guardian_contact_number) 
                            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_guardian_sql);
    $stmt->bind_param("sssss", $guardian_first_name, $guardian_last_name, $guardian_relation, $guardian_occupation, $guardian_contact_number);
    $stmt->execute();
    $guardian_id = $conn->insert_id;  // Get the new guardian_id
    $stmt->close();
    
    // Insert address data
    $country = $_POST['country'];
    $region = $_POST['region'];
    $house_address = $_POST['house_address'];

    $insert_address_sql = "INSERT INTO address (country, region, house_address) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insert_address_sql);
    $stmt->bind_param("sss", $country, $region, $house_address);
    $stmt->execute();
    $address_id = $conn->insert_id;  // Get the new address_id
    $stmt->close();

    // Insert student data
    $insert_sql = "INSERT INTO students (
        first_name, middle_name, last_name, birthdate, gender, phone_number, email, nationality, programme_id, session, 
        shs_name, wassce_index_number, guardian_id, address_id, registration_date, fees_paid, registration_status, 
        consent_to_keep_data, student_level) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 0, 'pending', 0, 100)";
        
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("ssssssssssssii", 
        $first_name, $middle_name, $last_name, $birthdate, $gender, $phone_number, $email, $nationality, $programme_id, $session, 
        $shs_name, $wassce_index_number, $guardian_id, $address_id);
    
    if ($stmt->execute()) {
        $success_message = "Student inserted successfully.";
    } else {
        $error_message = "Error inserting student: " . $stmt->error;
    }
    $stmt->close();
}



// Update a student
if (isset($_POST['update_student'])) {
    $student_id = $_POST['student_id'];
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $birthdate = $_POST['birthdate'];
    $gender = $_POST['gender'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $nationality = $_POST['nationality'];
    $programme_id = $_POST['programme'];
    $session = $_POST['session'];
    $shs_name = $_POST['shs_name'];
    $wassce_index_number = $_POST['wassce_index_number']; 
    $registration_status = $_POST['registration_status'];
    $fees_paid = $_POST['fees_paid'];
    
    // Update guardian data
    $guardian_first_name = $_POST['guardian_first_name'];
    $guardian_last_name = $_POST['guardian_last_name'];
    $guardian_relation = $_POST['guardian_relation'];
    $guardian_occupation = $_POST['guardian_occupation'];
    $guardian_contact_number = $_POST['guardian_contact_number'];
    
    $update_guardian_sql = "UPDATE guardian SET 
        guardian_first_name = ?, guardian_last_name = ?, guardian_relation = ?, guardian_occupation = ?, guardian_contact_number = ?
        WHERE guardian_id = (SELECT guardian_id FROM students WHERE student_id = ?)";
    $stmt = $conn->prepare($update_guardian_sql);
    $stmt->bind_param("sssssi", $guardian_first_name, $guardian_last_name, $guardian_relation, $guardian_occupation, $guardian_contact_number, $student_id);
    $stmt->execute();
    $stmt->close();

    // Update address data
    $country = $_POST['country'];
    $region = $_POST['region'];
    $house_address = $_POST['house_address'];
    
    $update_address_sql = "UPDATE address SET country = ?, region = ?, house_address = ? WHERE address_id = (SELECT address_id FROM students WHERE student_id = ?)";
    $stmt = $conn->prepare($update_address_sql);
    $stmt->bind_param("sssi", $country, $region, $house_address, $student_id);
    $stmt->execute();
    $stmt->close();
    
    // Update student data
    $update_sql = "UPDATE students SET 
        first_name = ?, middle_name = ?, last_name = ?, birthdate = ?, gender = ?, phone_number = ?, email = ?, 
        nationality = ?, programme_id = ?, session = ?, shs_name = ?, wassce_index_number = ?, registration_status = ?, 
        fees_paid = ?, consent_to_keep_data = ? 
        WHERE student_id = ?";
    
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sssssssssssssiii", 
        $first_name, $middle_name, $last_name, $birthdate, $gender, $phone_number, $email, $nationality, $programme_id, $session, 
        $shs_name, $wassce_index_number, $registration_status, $fees_paid, $consent_to_keep_data, $student_id);

    if ($stmt->execute()) {
        $success_message = "Student updated successfully.";
    } else {
        $error_message = "Error updating student: " . $stmt->error;
    }
    $stmt->close();
}


// Delete a student
if (isset($_POST['delete_student'])) {
    $student_id = $_POST['student_id'];

    $delete_sql = "DELETE FROM students WHERE student_id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $student_id);
    if ($stmt->execute()) {
        $success_message = "Student deleted successfully.";
    } else {
        $error_message = "Error deleting student: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Students</title>
    <link rel="stylesheet" href="/css/header.css">
    <link rel="stylesheet" href="/css/manage_students.css">
</head>
<body>

<?php include 'header.php'; ?>
<?php if (isset($success_message)): ?>
    <div class="success-banner" id="success-banner">
        <span><?php echo $success_message; ?></span>
        <button class="close" onclick="closeBanner()">X</button>
    </div>
<?php endif; ?>

<?php if (isset($error_message)): ?>
    <div class="error-banner" id="error-banner">
        <span><?php echo $error_message; ?></span>
        <button class="close" onclick="closeBanner()">X</button>
    </div>
<?php endif; ?>

<div class="container">
    <h1>Student Management</h1>
    <!-- Student Statistics -->
    <div class="student-stats">
        <p>Total Students: <strong><?php echo $total_students; ?></strong></p>
        <p>Males: <strong><?php echo $male_students; ?></strong></p>
        <p>Females: <strong><?php echo $female_students; ?></strong></p>
    </div>

    <!-- Search Student -->
    <h2>Search Student by ID</h2>
    <form action="manage_students.php" method="POST">
        <label for="student_id">Student ID:</label>
        <input type="text" id="student_id" name="student_id" required>
        <input type="submit" name="search_student" value="Search">
    </form>

    <?php if (isset($searched_student)): ?>
        <h3>Student Information</h3>
        <p>Student ID: <?php echo $searched_student['student_id']; ?></p>
        <p>Level: <?php echo $searched_student['student_level']; ?></p>
        <p>First Name: <?php echo $searched_student['first_name']; ?></p>
        <p>Middle Name: <?php echo $searched_student['middle_name']; ?></p>
        <p>Last Name: <?php echo $searched_student['last_name']; ?></p>
        <p>Gender: <?php echo $searched_student['gender']; ?></p>
        <p>Phone: <?php echo $searched_student['phone_number']; ?></p>
        <p>Email: <?php echo $searched_student['email']; ?></p>
        <p>Nationality: <?php echo $searched_student['nationality']; ?></p>
        <p>Programme: <?php echo $searched_student['programme_name']; ?></p>
        <p>Session: <?php echo $searched_student['session']; ?></p>
        <p>Guardian's First Name: <?php echo $searched_student['guardian_first_name']; ?></p>
        <p>Guardian's Last Name: <?php echo $searched_student['guardian_last_name']; ?></p>
        <p>Guardian Relation: <?php echo $searched_student['guardian_relation']; ?></p>
        <p>Guardian Contact: <?php echo $searched_student['guardian_contact_number']; ?></p>

    <?php elseif (isset($error)): ?>
        <p><?php echo $error; ?></p>
    <?php endif; ?>

    <!-- Insert Student -->
<h2>Insert a New Student</h2>
<form action="manage_students.php" method="POST">
    <label for="first_name">First Name:</label>
    <input type="text" id="first_name" name="first_name" required>

    <label for="middle_name">Middle Name:</label>
    <input type="text" id="middle_name" name="middle_name">

    <label for="last_name">Last Name:</label>
    <input type="text" id="last_name" name="last_name" required>

    <label for="student_level">Student level:</label>
    <input type="text" id="student_level" name="student_level" required>

    <label for="birthdate">Birthdate:</label>
    <input type="date" id="birthdate" name="birthdate" required>

    <label for="gender">Gender:</label>
    <select id="gender" name="gender" required>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
    </select>

    <label for="phone_number">Phone Number:</label>
    <input type="tel" id="phone_number" name="phone_number" required>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>

    <label for="nationality">Nationality:</label>
    <input type="text" id="nationality" name="nationality" required>

    <!-- Guardian Information -->
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

    <!-- SHS Information -->
    <h2>SHS Information</h2>
    <label for="shs_name">SHS Name:</label>
    <input type="text" id="shs_name" name="shs_name" required>

    <label for="wassce_index_number">WASSCE Index Number:</label>
    <input type="text" id="wassce_index_number" name="wassce_index_number" required>

    <!-- Address Information -->
    <h2>Address</h2>
    <label for="country">Country:</label>
    <select id="country" name="country" required>
        <option value="Ghana">Ghana</option>
        <option value="Nigeria">Nigeria</option>
        <!-- Add more countries as needed -->
    </select>

    <label for="region">Region:</label>
    <input type="text" id="region" name="region" required>

    <label for="house_address">House address:</label>
    <input type="text" id="house_address" name="house_address" required>


    <!-- Programme and Session -->
    <label for="programme">Programme:</label>
    <select id="programme" name="programme" required>
        <option value="">Select Programme</option>
        <option value="1">BSc Management</option>
        <option value="2">BSc Computer Science</option>
        <option value="3">BA Communication</option>
    </select>


    <label for="session">Session Type:</label>
    <select id="session" name="session" required>
        <option value="Morning">Morning Session</option>
        <option value="Evening">Evening Session</option>
    </select>

    <input type="submit" name="insert_student" value="Insert Student">
</form>

    <!-- Update Student -->
    <h2>Update Student Information</h2>
    <form action="manage_students.php" method="POST">
    <input type="hidden" name="student_id" value="<?php echo isset($searched_student['student_id']) ? $searched_student['student_id'] : ''; ?>">

    <label for="first_name">First Name:</label>
    <input type="text" id="first_name" name="first_name" value="<?php echo isset($searched_student['first_name']) ? $searched_student['first_name'] : ''; ?>" required>

    <label for="middle_name">Middle Name:</label>
    <input type="text" id="middle_name" name="middle_name" value="<?php echo isset($searched_student['middle_name']) ? $searched_student['middle_name'] : ''; ?>">

    <label for="last_name">Last Name:</label>
    <input type="text" id="last_name" name="last_name" value="<?php echo isset($searched_student['last_name']) ? $searched_student['last_name'] : ''; ?>" required>

    <label for="student_level">student_level:</label>
    <input type="text" id="last_name" name="student_level" value="<?php echo isset($searched_student['student_level']) ? $searched_student['student_level'] : ''; ?>" required>

    <label for="birthdate">Birthdate:</label>
    <input type="date" id="birthdate" name="birthdate" value="<?php echo isset($searched_student['birthdate']) ? $searched_student['birthdate'] : ''; ?>" required>

    <label for="gender">Gender:</label>
    <select id="gender" name="gender" required>
        <option value="Male" <?php echo (isset($searched_student['gender']) && $searched_student['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
        <option value="Female" <?php echo (isset($searched_student['gender']) && $searched_student['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
    </select>

    <!-- Phone Number Field -->
    <label for="phone_number">Phone Number:</label>
    <input type="tel" id="phone_number" name="phone_number" value="<?php echo isset($searched_student['phone_number']) ? $searched_student['phone_number'] : ''; ?>" required>

    <!-- Email Field -->
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" value="<?php echo isset($searched_student['email']) ? $searched_student['email'] : ''; ?>" required>

    <!-- Nationality Field -->
    <label for="nationality">Nationality:</label>
    <input type="text" id="nationality" name="nationality" value="<?php echo isset($searched_student['nationality']) ? $searched_student['nationality'] : ''; ?>" required>

    <!-- Guardian Information -->
    <h2>Guardian Information</h2>
    <label for="guardian_first_name">Guardian First Name:</label>
    <input type="text" id="guardian_first_name" name="guardian_first_name" value="<?php echo isset($searched_student['guardian_first_name']) ? $searched_student['guardian_first_name'] : ''; ?>" required>

    <label for="guardian_last_name">Guardian Last Name:</label>
    <input type="text" id="guardian_last_name" name="guardian_last_name" value="<?php echo isset($searched_student['guardian_last_name']) ? $searched_student['guardian_last_name'] : ''; ?>" required>

    <label for="guardian_relation">Guardian's Relation:</label>
    <input type="text" id="guardian_relation" name="guardian_relation" value="<?php echo isset($searched_student['guardian_relation']) ? $searched_student['guardian_relation'] : ''; ?>" required>

    <label for="guardian_occupation">Guardian Occupation:</label>
    <input type="text" id="guardian_occupation" name="guardian_occupation" value="<?php echo isset($searched_student['guardian_occupation']) ? $searched_student['guardian_occupation'] : ''; ?>" required>

    <label for="guardian_contact_number">Guardian Contact Number:</label>
    <input type="tel" id="guardian_contact_number" name="guardian_contact_number" value="<?php echo isset($searched_student['guardian_contact_number']) ? $searched_student['guardian_contact_number'] : ''; ?>" required>

    <!-- SHS Information -->
    <h2>SHS Information</h2>
    <label for="shs_name">SHS Name:</label>
    <input type="text" id="shs_name" name="shs_name" value="<?php echo isset($searched_student['shs_name']) ? $searched_student['shs_name'] : ''; ?>" required>

    <label for="wassce_index_number">WASSCE Index Number:</label>
    <input type="text" id="wassce_index_number" name="wassce_index_number" value="<?php echo isset($searched_student['wassce_index_number']) ? $searched_student['wassce_index_number'] : ''; ?>" required>

    <!-- Address Information -->
    <h2>Address</h2>
    <label for="country">Country:</label>
    <select id="country" name="country" required>
        <option value="Ghana" <?php echo (isset($searched_student['country']) && $searched_student['country'] == 'Ghana') ? 'selected' : ''; ?>>Ghana</option>
        <option value="Nigeria" <?php echo (isset($searched_student['country']) && $searched_student['country'] == 'Nigeria') ? 'selected' : ''; ?>>Nigeria</option>
    </select>

    <label for="region">Region:</label>
    <input type="text" id="region" name="region" value="<?php echo isset($searched_student['region']) ? $searched_student['region'] : ''; ?>" required>

    <label for="house_address">House address:</label>
    <input type="text" id="house_address" name="house_address" value="<?php echo isset($searched_student['house_address']) ? $searched_student['house_address'] : ''; ?>" required>

    <!-- Programme and Session -->
    <label for="programme">Programme:</label>
    <select id="programme" name="programme" required>
        <option value="1" <?php echo (isset($searched_student['programme_id']) && $searched_student['programme_id'] == 1) ? 'selected' : ''; ?>>BSc Management</option>
        <option value="2" <?php echo (isset($searched_student['programme_id']) && $searched_student['programme_id'] == 2) ? 'selected' : ''; ?>>BSc Computer Science</option>
        <option value="3" <?php echo (isset($searched_student['programme_id']) && $searched_student['programme_id'] == 3) ? 'selected' : ''; ?>>BA Communication</option>
    </select>

    <label for="session">Session Type:</label>
    <select id="session" name="session" required>
        <option value="Morning" <?php echo (isset($searched_student['session']) && $searched_student['session'] == 'Morning') ? 'selected' : ''; ?>>Morning Session</option>
        <option value="Evening" <?php echo (isset($searched_student['session']) && $searched_student['session'] == 'Evening') ? 'selected' : ''; ?>>Evening Session</option>
    </select>

    <label for="registration_status">Registration Status:</label>
    <input type="text" id="registration_status" name="registration_status" value="<?php echo isset($searched_student) ? $searched_student['registration_status'] : ''; ?>" required>

    <label for="fees_paid">Fees Paid:</label>
    <input type="number" id="fees_paid" name="fees_paid" value="<?php echo isset($searched_student) ? $searched_student['amount_paid'] : ''; ?>" required>

    <input type="submit" name="update_student" value="Update Student">
</form>

    <!-- Delete Student -->
    <h2>Delete a Student</h2>
    <form action="manage_students.php" method="POST">
        <label for="student_id">Student ID (to delete):</label>
        <input type="text" id="student_id" name="student_id" required>

        <input type="submit" name="delete_student" value="Delete Student">
    </form>
</div>
<script>
    function closeBanner() {
    document.getElementById('success-banner').style.display = 'none';
    document.getElementById('error-banner').style.display = 'none';
}
</script>
</body>
</html>
