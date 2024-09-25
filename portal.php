<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $dob = $_POST['dob'];

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

    // Check if student ID and date of birth match
    $sql = "SELECT * FROM students WHERE student_id = ? AND birthdate = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $student_id, $dob);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Student authenticated
        $_SESSION['student_id'] = $student_id;
        header("Location: profile.php");  // Redirect to profile page
        exit;
    } else {
        $error_message = "Invalid Student ID or Date of Birth.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
    <link rel="stylesheet" href="/css/portal.css">
</head>
<body>
<?php include 'header.php'; ?>
<div class="login-container">
    <h2>Student Login</h2>

    <?php if (isset($error_message)): ?>
        <p class="error-message"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <form action="portal.php" method="POST">
        <label for="student_id">Student ID:</label>
        <input type="text" id="student_id" name="student_id" required>

        <label for="dob">Date of Birth (YYYY-MM-DD):</label>
        <input type="date" id="dob" name="dob" required>

        <input type="submit" value="Login">
    </form>
</div>

</body>
</html>
