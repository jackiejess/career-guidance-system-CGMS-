<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    die("Access Denied");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #2c3e50;
            color: white;
            padding-top: 20px;
            position: fixed;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 22px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            padding: 15px;
            text-align: left;
            transition: 0.3s;
        }

        .sidebar ul li:hover {
            background: #1a252f;
        }

        .sidebar ul li a {
            text-decoration: none;
            color: white;
            display: flex;
            align-items: center;
        }

        .sidebar ul li a i {
            margin-right: 10px;
            font-size: 18px;
        }

        /* Main Content */
        .content {
            margin-left: 260px;
            padding: 20px;
            flex-grow: 1;
        }

        .content h2 {
            font-size: 28px;
            color: #333;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100px;
            }
            .sidebar h2 {
                display: none;
            }
            .sidebar ul li a span {
                display: none;
            }
            .content {
                margin-left: 110px;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Admin Panel</h2>
      <ul>
    <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
    <li><a href="manage_students.php"><i class="fas fa-user-graduate"></i> <span>Manage Students</span></a></li>
    <li><a href="manage_counselors.php"><i class="fas fa-user-tie"></i> <span>Manage Counselors</span></a></li>
    <li><a href="view_assessments.php"><i class="fas fa-file-alt"></i> <span>View Assessments</span></a></li>
    <li><a href="manage_appointments.php"><i class="fas fa-calendar-check"></i> <span>Manage Appointments</span></a></li>
    <li><a href="analysis.php"><i class="fas fa-chart-line"></i> <span>Reports & Analytics</span></a></li>
    <li><a href="settings.php"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
</ul>

    </div>

    <!-- Main Content -->
    <div class="content">
        <h2>Welcome to the Admin Dashboard</h2>
        <p>Use the menu to manage students, counselors, assessments, and appointments.</p>
    </div>

</body>
</html>
