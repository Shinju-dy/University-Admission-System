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

// Fetch outstanding fees for the student if needed (optional)
$student_id = '';
$total_due = '';
$amount_paid = '';

if (isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];

    $sql = "SELECT total_due, amount_paid FROM Fees WHERE student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $fee_data = $result->fetch_assoc();
        $total_due = $fee_data['total_due'];
        $amount_paid = $fee_data['amount_paid'];
    } else {
        echo "No fee records found for the student.";
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
    <title>Fee Payment</title>
    <link rel="stylesheet" href="/css/student_pay_fees.css"> 
</head>
<body>

<?php include 'header.php'; ?>

<?php
// Check if the 'success' query parameter is present
if (isset($_GET['success']) && $_GET['success'] == 1) {
    echo '<div class="success-banner" id="success-banner">
            <span>Fees successfully paid!</span>
            <button class="close" onclick="closeBanner()">X</button>
          </div>';
}
?>

<div class="payment-container">
    <h1>Pay Your Fees</h1>
    <form action="process_payment.php" method="POST">
        <label for="student_id">Student ID</label>
        <input type="text" id="student_id" name="student_id" value="<?php echo $student_id; ?>" required>

        <?php if ($total_due): ?>
            <p>Total Due: <?php echo $total_due; ?></p>
            <p>Amount Paid: <?php echo $amount_paid; ?></p>
        <?php endif; ?>

        <label for="amount">Amount to Pay</label>
        <input type="number" id="amount" name="amount" required>

        <input type="submit" value="Pay Now">
    </form>
</div>

<script>
    // Function to close the success banner
    function closeBanner() {
        document.getElementById('success-banner').classList.add('hidden');
    }
</script>

</body>
</html>
