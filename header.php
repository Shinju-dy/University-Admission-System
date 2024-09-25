<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University System</title>
    <link rel="stylesheet" href="/css/header.css">
</head>
<body>
<header>
    <nav class="navbar">
        <div class="logo">
            <a href="index.php">University Admission System</a>
        </div>
        <div class="menu-icon" onclick="toggleMenu()">
            <span>&#9776;</span> <!-- Hamburger icon -->
        </div>
        <ul class="nav-links" id="nav-links">
            <li><a href="index.php">Student Application</a></li>
            <li><a href="admin.php">Admission Dashboard</a></li>
            <li><a href="student_Pay_fees.php">Fee Payment</a></li>
            <li><a href="check_fees_admin.php">Admin Fee Payment</a></li>
            <li><a href="manage_students.php">Manage Students</a></li>
            <li><a href="portal.php">Student Portal</a></li>
        </ul>
    </nav>
</header>

<script>
    function toggleMenu() {
        const navLinks = document.getElementById('nav-links');
        if (navLinks.style.display === 'block') {
            navLinks.style.display = 'none';
        } else {
            navLinks.style.display = 'block';
        }
    }
</script>
</body>
</html>
