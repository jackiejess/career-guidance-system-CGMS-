<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        /* Profile dropdown container */
        .profile-container {
            position: absolute;
            top: 60px;
            right: 10px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            padding: 15px;
            display: none;
            width: 200px;
            text-align: center;
        }

        .profile-container img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 2px solid #ccc;
        }

        .profile-container a {
            display: block;
            text-decoration: none;
            color: #333;
            margin-top: 10px;
            font-size: 14px;
            padding: 8px;
            border-radius: 5px;
        }

        .profile-container a:hover {
            background: #f0f0f0;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Career Guide</h2>
       <ul class="nav-menu">
    <li><a href="#"><i class="fa-solid fa-house"></i> Dashboard</a></li>
    <li><a href="assessment.php"><i class="fa-solid fa-clipboard-question"></i> Assessments</a></li>
    <li><a href="resources.php"><i class="fa-solid fa-book"></i> Career Resources</a></li>
    <li><a href="book_appointments.php"><i class="fa-solid fa-calendar-check"></i> Bookings</a></li>
    <li><a href="#"><i class="fa-solid fa-lightbulb"></i> Career Suggestions</a></li>
    <li><a href="#"><i class="fa-solid fa-chart-line"></i> Progress</a></li>
    <li><a href="logout.php"><i class="fa-solid fa-sign-out-alt"></i> Logout</a></li>
</ul>

    </div>

    <!-- Main Content -->
    <div class="main-content">
        <header>
            <h1>Welcome, <span id="studentName">
                <?php echo htmlspecialchars($_SESSION['student_name']); ?>
            </span>!</h1>

            <!-- Theme Toggle & Profile -->
            <div class="header-right">
                <button id="themeToggle">
                    <i class="fa-solid fa-moon"></i>
                </button>

                <div class="profile">
                    <img src="uploads/<?php echo $_SESSION['profile_picture'] ?: 'default.png'; ?>" alt="Profile" id="profilePic" style="width: 40px; height: 40px; border-radius: 50%; cursor: pointer;">
                    
                    <!-- Profile Dropdown -->
                    <div class="profile-container" id="profileContainer">
                        <img src="uploads/<?php echo $_SESSION['profile_picture'] ?: 'default.png'; ?>" alt="Profile">
                        <a href="edit_profile.php"><i class="fa-solid fa-user-pen"></i> Edit Profile</a>
                        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                    </div>
                </div>
            </div>
        </header>

        <div class="cards">
            <div class="card">
                <i class="fa-solid fa-clipboard-question"></i>
                <h3>Take Assessment</h3>
                <p>Start your career assessment to get personalized suggestions.</p>
                <a href="assessment.php">Start Now</a>
            </div>
            <div class="card">
                <i class="fa-solid fa-briefcase"></i>
                <h3>Career Suggestions</h3>
                <p>View careers based on your interests and skills.</p>
                <a href="#">Explore</a>
            </div>
            <div class="card">
                <i class="fa-solid fa-chart-line"></i>
                <h3>Progress</h3>
                <p>Track your career journey and completed assessments.</p>
                <a href="#">View Progress</a>
            </div>
        </div>
    </div>

    <script>
        // Dark Mode Toggle
        document.getElementById("themeToggle").addEventListener("click", function () {
            document.body.classList.toggle("dark-mode");
        });

        // Profile dropdown toggle
        document.getElementById("profilePic").addEventListener("click", function () {
            var profileContainer = document.getElementById("profileContainer");
            profileContainer.style.display = (profileContainer.style.display === "block") ? "none" : "block";
        });

        // Close profile dropdown when clicking outside
        document.addEventListener("click", function (event) {
            var profileContainer = document.getElementById("profileContainer");
            var profilePic = document.getElementById("profilePic");

            if (!profileContainer.contains(event.target) && event.target !== profilePic) {
                profileContainer.style.display = "none";
            }
        });
    </script>

</body>
</html>
