<?php 
// Database connection
$servername = "localhost:3307";
$username = "root";  // Use your database username
$password = "";  // Use your database password
$dbname = "UniversityRegistration";  // Replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submissions for accepting or rejecting an applicant
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $applicant_id = $_POST['applicant_id'];

    if (isset($_POST['accept'])) {
        // Generate an index number for the student
        $index_sql = "SELECT MAX(student_id) AS last_index FROM students";
        $index_result = $conn->query($index_sql);
        $last_index = $index_result->fetch_assoc()['last_index'] ?? 10110000;
        $new_index = $last_index + 1;

    
        // Move the applicant's data to the students table, pulling data from application, guardian, and address tables
        $move_sql = "INSERT INTO students (student_id, first_name, middle_name, last_name, student_level, birthdate, gender, phone_number, email, nationality,
        shs_name, wassce_index_number, programme_id, session, fees_paid, registration_status, registration_date, 
        consent_to_keep_data, guardian_id, address_id)

        SELECT $new_index, a.first_name, a.middle_name, a.last_name, 100, a.birthdate, a.gender, a.phone_number, a.email, a.nationality, 
        a.shs_name, a.wassce_index_number, a.programme_id, a.session, FALSE, 'pending', NOW(), 
        a.consent_to_keep_data, a.guardian_id, a.address_id
        FROM application a
        WHERE a.applicant_id = $applicant_id";

        if ($conn->query($move_sql) === TRUE) {
            // Insert the initial record into the Fees table for this new student
            $fee_sql = "INSERT INTO Fees (student_id, semester, total_due, amount_paid, status) 
                        VALUES ($new_index, 'Semester 1', 2000.00, 0.00, 'pending')";
    
            if ($conn->query($fee_sql) === TRUE) {
                // Delete the applicant from the application table after moving
                $delete_sql = "DELETE FROM application WHERE applicant_id = $applicant_id";
                $conn->query($delete_sql);
                header("Location: admin.php?success=accepted");
                exit;
            } else {
                echo "Error inserting fee record: " . $conn->error;
            }
        } else {
            echo "Error: " . $conn->error;
        }
    }
    

    if (isset($_POST['reject'])) {
        // Check if the applicant gave consent to keep their data
        $consent_sql = "SELECT consent_to_keep_data FROM application WHERE applicant_id = $applicant_id";
        $consent_result = $conn->query($consent_sql);
        $consent = $consent_result->fetch_assoc()['consent_to_keep_data'];

        if ($consent) {
            // Update the status to rejected, keep the data
            $reject_sql = "UPDATE application SET status = 'rejected' WHERE applicant_id = $applicant_id";
            $conn->query($reject_sql);
            header("Location: admin.php?success=rejected_kept");
            exit;
        } else {
            // Delete the applicant's data
            $delete_sql = "DELETE FROM application WHERE applicant_id = $applicant_id";
            $conn->query($delete_sql);
            header("Location: admin.php?success=rejected_deleted");
            exit;
        }
    }
}

// Fetch pending applications and program name
$sql = "SELECT a.applicant_id, a.first_name, a.last_name, a.wassce_index_number, p.Name as programme
        FROM application a
        JOIN Programme p ON a.programme_id = p.Programme_id
        WHERE a.status = 'pending'";
$result = $conn->query($sql);

// Fetch accepted students
$accepted_sql = "SELECT student_id, first_name, last_name, birthdate, registration_date FROM students ORDER BY registration_date DESC";
$accepted_result = $conn->query($accepted_sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Application Review</title>
    <link rel="stylesheet" href="/css/admin.css">
</head>
<body>


<?php include 'header.php'; ?>

<?php
// Check if 'success' query parameter is present
if (isset($_GET['success'])) {
    $message = '';
    if ($_GET['success'] == 'accepted') {
        $message = 'Applicant admitted.';
    } elseif ($_GET['success'] == 'rejected_kept') {
        $message = 'Applicant rejected, data kept.';
    } elseif ($_GET['success'] == 'rejected_deleted') {
        $message = 'Applicant rejected and data deleted.';
    }

    // Show the banner if there's a message
    if ($message != '') {
        echo '<div class="success-banner" id="success-banner">
                <span>' . $message . '</span>
                <button class="close" onclick="closeBanner()">X</button>
              </div>';
    }
}
?>

<div class="container">
    <h1>Pending Applications</h1>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Applicant ID</th>
                <th>Name</th>
                <th>Programme</th>
                <th>WASSCE Index</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['applicant_id']; ?></td>
                <td><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
                <td><?php echo $row['programme']; ?></td>
                <td><?php echo $row['wassce_index_number']; ?></td>

                <td>
                    <form action="admin.php" method="POST"> <!-- Handled within the same file -->
                        <input type="hidden" name="applicant_id" value="<?php echo $row['applicant_id']; ?>">
                        <button type="submit" name="accept" class='greenb'>Accept</button>
                        <button type="submit" name="reject">Reject</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No pending applications.</p>
    <?php endif; ?>

</div>

<!-- Accepted Students Section -->
<div class="container">
    <h1>Accepted Students</h1>

    <?php if ($accepted_result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Birthdate</th>
                <th>Accepted On</th>
            </tr>
            <?php while ($accepted_row = $accepted_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $accepted_row['student_id']; ?></td>
                <td><?php echo $accepted_row['first_name'] . ' ' . $accepted_row['last_name']; ?></td>
                <td><?php echo $accepted_row['birthdate']; ?></td>
                <td><?php echo $accepted_row['registration_date']; ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No students have been accepted yet.</p>
    <?php endif; ?>
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

<?php
$conn->close();
?>
