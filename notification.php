<?php
// After admin accepts the application

$to = $student_email;  // Student's email fetched from database
$subject = "Your Application to [University Name] has been Accepted!";
$message = "
Dear $student_first_name,

Congratulations! Your application to [University Name] has been accepted.

Your Student ID is: $student_id
Your temporary password is: $student_dob

You can now log in to the student portal using the following link: [Portal URL]

Best regards,
[University Name] Admissions Office
";

// Headers
$headers = "From: admissions@university.com";

// Send email
mail($to, $subject, $message, $headers);
?>
