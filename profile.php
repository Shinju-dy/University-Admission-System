<?php
session_start();

// Ensure the student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

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

$student_id = $_SESSION['student_id'];

// Fetch student profile details with LEFT JOIN to handle missing relationships
$sql_profile = "SELECT s.student_id, s.first_name, s.middle_name, s.last_name, s.student_level, 
                       p.Name AS programme_name, d.department_name, f.faculty_name
                FROM students s
                JOIN Programme p ON s.programme_id = p.Programme_id
                LEFT JOIN Department d ON p.department_id = d.department_id
                LEFT JOIN Faculty f ON d.faculty_id = f.faculty_id
                WHERE s.student_id = ?";

$stmt = $conn->prepare($sql_profile);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

// Handle cases where no data is returned
if (!$student) {
    die("No student found with ID: " . htmlspecialchars($student_id));
}


// Fetch enrolled courses for the current semester
$current_semester = 'Semester 1';  // Adjust as needed
$sql_courses = "SELECT c.course_name 
                FROM Enrollments e
                JOIN Courses c ON e.course_id = c.course_id
                WHERE e.student_id = ? AND c.semester = ?";
$stmt = $conn->prepare($sql_courses);
$stmt->bind_param("is", $student_id, $current_semester);
$stmt->execute();
$courses = $stmt->get_result();
$has_courses = $courses->num_rows > 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link rel="stylesheet" href="/css/profile.css">
</head>
<body>
<?php include 'header.php'; ?>
<div class="profile-container">
<h2>Student Profile</h2>
<p><strong>Student ID:</strong> <?php echo htmlspecialchars($student['student_id']); ?></p>
<p><strong>First Name:</strong> <?php echo htmlspecialchars($student['first_name']); ?></p>
<p><strong>Middle Name:</strong> <?php echo htmlspecialchars($student['middle_name']); ?></p>
<p><strong>Last Name:</strong> <?php echo htmlspecialchars($student['last_name']); ?></p>
<p><strong>Level:</strong> <?php echo htmlspecialchars($student['student_level']); ?></p>
<p><strong>Program:</strong> <?php echo htmlspecialchars($student['programme_name']); ?></p>
<p><strong>Department:</strong> <?php echo htmlspecialchars($student['department_name']); ?></p>
<p><strong>Faculty:</strong> <?php echo htmlspecialchars($student['faculty_name']); ?></p>

<h3>Enrolled Courses - <?php echo htmlspecialchars($current_semester); ?></h3>

<?php if ($has_courses): ?>
    <ul>
        <?php while ($course = $courses->fetch_assoc()): ?>
            <li><?php echo htmlspecialchars($course['course_name']); ?></li>
        <?php endwhile; ?>
    </ul>
<?php else: ?>
    <p>No courses enrolled this semester.</p>
<?php endif; ?>

<!-- Button to navigate to course enrollment -->
<a href="course_enrollment.php">Enroll in Courses</a>

<!-- Logout button -->
<a href="logout.php" class="logout-button">Logout</a>
</div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
