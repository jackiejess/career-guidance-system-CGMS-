<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['counselor_id'])) {
    header("Location: login.php");
    exit();
}

// Set default profile picture if none exists
$profile_picture = !empty($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : 'default.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Counselor Dashboard</title>
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- External CSS -->
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
   <style>/* General styles */

</style>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2><i class="fa-solid fa-user-tie"></i> Counselor</h2>
        <ul>
            <li><a href="#"><i class="fa-solid fa-house"></i> Dashboard</a></li>
            <li><a href="assessment.php"><i class="fa-solid fa-clipboard-list"></i> Assessments</a></li>
            <li><a href="students.php"><i class="fa-solid fa-user-graduate"></i> Students</a></li>
            <li><a href="manage_appointments.php"><i class="fa-solid fa-user-graduate"></i> manage appointments</a></li>
            <li><a href="#"><i class="fa-solid fa-book-open"></i> Career Advice</a></li>
            <li><a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <header>
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['counselor_name']); ?> 👋</h1>

            <!-- Profile Section -->
            <div class="profile-container">
                <img src="uploads/<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile" class="profile-img">
                <div class="dropdown">
                    <a href="#">View Profile</a>
                    <a href="#">Settings</a>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        </header>

        <!-- Dashboard Cards -->
        <div class="dashboard-cards">
            <div class="card">
                <i class="fa-solid fa-clipboard-check"></i>
                <h3>New Assessments</h3>
                <p>Manage recent student assessments.</p>
                <a href="#">View</a>
            </div>
            <div class="card">
                <i class="fa-solid fa-user-graduate"></i>
                <h3>Student Profiles</h3>
                <p>Check student progress and guidance history.</p>
                <a href="#">View</a>
            </div>
            <div class="card">
                <i class="fa-solid fa-chart-line"></i>
                <h3>Reports & Insights</h3>
                <p>Analyze career assessment trends.</p>
                <a href="#">View</a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const profileContainer = document.querySelector(".profile-container");
            const dropdown = document.querySelector(".dropdown");

            profileContainer.addEventListener("click", function (event) {
                event.stopPropagation();
                dropdown.classList.toggle("show");
            });

            document.addEventListener("click", function () {
                dropdown.classList.remove("show");
            });
        });
    </script>

</body>
</html>
