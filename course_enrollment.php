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

// Fetch student's program and current academic year
$sql_student = "SELECT programme_id, student_level FROM students WHERE student_id = ?";
$stmt = $conn->prepare($sql_student);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$programme_id = $student['programme_id'];

// Convert student_level to academic_year by dividing by 100
$academic_year = $student['student_level'] / 100;  // e.g., 100 -> 1, 200 -> 2

// Fetch enrolled courses to check if the student has already enrolled
$current_semester = 'Semester 1';
$sql_enrolled_courses = "SELECT c.course_id, c.course_name 
                         FROM Enrollments e 
                         JOIN Courses c ON e.course_id = c.course_id 
                         WHERE e.student_id = ? AND c.semester = ?";
$stmt = $conn->prepare($sql_enrolled_courses);
$stmt->bind_param("is", $student_id, $current_semester);
$stmt->execute();
$enrolled_courses = $stmt->get_result();

// Check if the student has already enrolled
$already_enrolled = $enrolled_courses->num_rows > 0;

// Fetch available courses for the semester
$sql_courses = "SELECT course_id, course_name FROM Courses 
                WHERE programme_id = ? AND semester = ? AND academic_year = ?";
$stmt = $conn->prepare($sql_courses);
$stmt->bind_param("isi", $programme_id, $current_semester, $academic_year);
$stmt->execute();
$courses = $stmt->get_result();

// Handle course selection form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_enrollment'])) {
        // Delete the current enrollments if the user requests to remove them
        $delete_sql = "DELETE FROM Enrollments WHERE student_id = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("i", $student_id);
        if ($stmt->execute()) {
            $success_message = "Your courses have been deleted. You can re-enroll now.";
            $already_enrolled = false;
        }
    } elseif (isset($_POST['courses'])) {
        $selected_courses = $_POST['courses'];

        if (count($selected_courses) == 5) {
            // Enroll student in the selected courses
            foreach ($selected_courses as $course_id) {
                $enroll_sql = "INSERT INTO Enrollments (student_id, course_id) VALUES (?, ?)";
                $stmt = $conn->prepare($enroll_sql);
                $stmt->bind_param("ii", $student_id, $course_id);
                $stmt->execute();
            }

            // Update the student's registration status to 'completed'
            $update_status_sql = "UPDATE students SET registration_status = 'completed' WHERE student_id = ?";
            $stmt = $conn->prepare($update_status_sql);
            $stmt->bind_param("i", $student_id);
            $stmt->execute();

            
            // Redirect to profile page after successful enrollment
            header("Location: profile.php");
            exit();
        } else {
            $error_message = "You must select exactly 5 courses.";
        }
    } else {
        $error_message = "Please select 5 courses to enroll.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Enrollment</title>
    <link rel="stylesheet" href="/css/enrollment.css">
</head>
<body>
<?php include 'header.php'; ?>

<div class="enroll-container">
<h2>Course Enrollment - <?php echo $current_semester; ?></h2>
<p>COURSE PROVIDED FOR THE PURPOSE OF THIS PROJECT ARE JUST FOR THE FIRST SEMESTER OF FIRST YEAR</p>

<?php if (isset($success_message)): ?>
    <p style="color: green;"><?php echo $success_message; ?></p>
<?php elseif (isset($error_message)): ?>
    <p style="color: red;"><?php echo $error_message; ?></p>
<?php endif; ?>

<?php if ($already_enrolled): ?>
    <h3>You are already enrolled in the following courses:</h3>
    <ul>
        <?php while ($course = $enrolled_courses->fetch_assoc()): ?>
            <li><?php echo $course['course_name']; ?></li>
        <?php endwhile; ?>
    </ul>

    <!-- Option to delete enrolled courses -->
    <form action="course_enrollment.php" method="POST">
        <input type="submit" name="delete_enrollment" value="Delete Enrollments">
    </form>
<?php else: ?>
    <form action="course_enrollment.php" method="POST">
        <h3>Select 5 Courses:</h3>
        <ul>
            <?php while ($course = $courses->fetch_assoc()): ?>
                <li>
                    <input type="checkbox" name="courses[]" value="<?php echo $course['course_id']; ?>">
                    <?php echo $course['course_name']; ?>
                </li>
            <?php endwhile; ?>
        </ul>
        <input type="submit" value="Enroll">
    </form>
<?php endif; ?>
</div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
