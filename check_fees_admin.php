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

// Fetch count of students who have fully paid and those who haven't
$sql_paid = "SELECT COUNT(*) AS total_paid FROM Fees WHERE status = 'paid'";
$sql_pending = "SELECT COUNT(*) AS total_pending FROM Fees WHERE status != 'paid'";

$result_paid = $conn->query($sql_paid);
$result_pending = $conn->query($sql_pending);

$paid_count = ($result_paid->num_rows > 0) ? $result_paid->fetch_assoc()['total_paid'] : 0;
$pending_count = ($result_pending->num_rows > 0) ? $result_pending->fetch_assoc()['total_pending'] : 0;

// Search for a specific student's fee record
$student_record = null;

if (isset($_POST['search'])) {
    $student_id = $_POST['student_id'];
    $sql_student = "SELECT * FROM Fees WHERE student_id = ?";
    $stmt = $conn->prepare($sql_student);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $student_record = $result->fetch_assoc();
    } else {
        $error = "No fee records found for Student ID: $student_id";
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
    <title>Admin - Check Fees</title>
    <link rel="stylesheet" href="/css/header.css"> <!-- Main header CSS -->
    <link rel="stylesheet" href="/css/admin_fees.css"> <!-- Specific CSS for this page -->
</head>
<body>

<?php include 'header.php'; ?>

<div class="fees-container">
    <h1>Fees Status Overview</h1>

    <div class="fees-summary">
        <p>Total Students Who Fully Paid: <strong><?php echo $paid_count; ?></strong></p>
        <p>Total Students Yet to Pay Fully: <strong><?php echo $pending_count; ?></strong></p>
    </div>

    <h2>Search Student Fee Record</h2>
    <form action="check_fees_admin.php" method="POST">
        <label for="student_id">Student ID:</label>
        <input type="text" id="student_id" name="student_id" required>
        <input type="submit" name="search" value="Search">
    </form>

    <?php if (isset($student_record)): ?>
        <h3>Fee Record for Student ID: <?php echo $student_record['student_id']; ?></h3>
        <p>Total Due: <?php echo $student_record['total_due']; ?></p>
        <p>Amount Paid: <?php echo $student_record['amount_paid']; ?></p>
        <p>Status: <?php echo $student_record['status']; ?></p>
    <?php elseif (isset($error)): ?>
        <p><?php echo $error; ?></p>
    <?php endif; ?>
</div>

</body>
</html>
