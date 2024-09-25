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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $amount_paid = $_POST['amount'];

    // Fetch the current fee details for the student
    $sql = "SELECT total_due, amount_paid FROM Fees WHERE student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $fee_data = $result->fetch_assoc();

    if ($fee_data) {
        // Calculate the new amount paid
        $new_amount_paid = $fee_data['amount_paid'] + $amount_paid;
        $status = ($new_amount_paid >= $fee_data['total_due']) ? 'paid' : 'partially_paid';

        // Update the fees table with the new amount and status
        $update_sql = "UPDATE Fees SET amount_paid = ?, status = ?, date_paid = NOW() WHERE student_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("dsi", $new_amount_paid, $status, $student_id);

        if ($update_stmt->execute()) {
            // If the fees are fully paid, update the student's registration status in the students table
            if ($new_amount_paid >= $fee_data['total_due']) {
                $registration_status = 'completed';
            } else {
                $registration_status = 'pending';
            }

            // Update the student's registration status
            $update_student_sql = "UPDATE students SET registration_status = ? WHERE student_id = ?";
            $update_student_stmt = $conn->prepare($update_student_sql);
            $update_student_stmt->bind_param("si", $registration_status, $student_id);
            $update_student_stmt->execute();

            // Redirect back to the payment page with success message
            header("Location: student_pay_fees.php?success=1");
            exit;
        } else {
            echo "Error updating fee records: " . $conn->error;
        }
    } else {
        echo "No fee record found for the student.";
    }

    $stmt->close();
}

$conn->close();
?>
