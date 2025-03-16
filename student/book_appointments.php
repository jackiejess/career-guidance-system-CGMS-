<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=career', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Ensure student is logged in
if (!isset($_SESSION['student_id'])) {
    die("You must be logged in as a student to book an appointment.");
}

$student_id = $_SESSION['student_id'];
$message = "";

// Fetch student's counselor
$stmt = $pdo->prepare("SELECT counselor_id FROM students WHERE id = ?");
$stmt->execute([$student_id]);
$counselor = $stmt->fetch();

if (!$counselor || !$counselor['counselor_id']) {
    die("You haven't chosen a counselor yet.");
}
$counselor_id = $counselor['counselor_id'];

// Handle AJAX appointment booking
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $appointment_date = $_POST['appointment_date'];

    if (empty($appointment_date)) {
        echo json_encode(["status" => "error", "message" => "Please select a date and time."]);
        exit;
    } else {
        $stmt = $pdo->prepare("INSERT INTO appointments (student_id, counselor_id, appointment_date) VALUES (?, ?, ?)");
        $stmt->execute([$student_id, $counselor_id, $appointment_date]);
        echo json_encode(["status" => "success", "message" => "Appointment request sent!"]);
        exit;
    }
}

// Fetch student's appointments
$stmt = $pdo->prepare("SELECT a.id, a.appointment_date, a.status, c.name AS counselor_name 
                       FROM appointments a
                       JOIN counselors c ON a.counselor_id = c.id
                       WHERE a.student_id = ?");
$stmt->execute([$student_id]);
$appointments = $stmt->fetchAll();

// Fetch pending notifications count
$stmt = $pdo->prepare("SELECT COUNT(*) AS count FROM appointments WHERE student_id = ? AND status IN ('approved', 'rejected')");
$stmt->execute([$student_id]);
$notification_count = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .container { max-width: 600px; margin: auto; }
        .notification { position: relative; display: inline-block; cursor: pointer; }
        .notification .badge { position: absolute; top: -5px; right: -5px; background: red; color: white; border-radius: 50%; padding: 5px 8px; font-size: 12px; }
        .alert { padding: 10px; margin-top: 10px; border-radius: 5px; }
        .alert-success { background-color: #d4edda; color: #155724; }
        .alert-danger { background-color: #f8d7da; color: #721c24; }
        .appointments { margin-top: 20px; }
        .appointments p { padding: 10px; border: 1px solid #ccc; border-radius: 5px; background: #f9f9f9; }
    </style>
</head>
<body>

    <div class="container">
        <h2>Book an Appointment with Your Counselor</h2>

        <!-- Notification Icon -->
        <div class="notification">
            <i class="fas fa-bell fa-2x"></i>
            <?php if ($notification_count > 0): ?>
                <span class="badge"><?= $notification_count ?></span>
            <?php endif; ?>
        </div>

        <form id="appointmentForm">
            <label>Select Date & Time:</label>
            <input type="datetime-local" name="appointment_date" id="appointment_date" required>
            <button type="submit">Request Appointment</button>
        </form>

        <div id="message"></div>

        <!-- List of Appointments -->
        <div class="appointments">
            <h3>Your Appointments</h3>
            <?php foreach ($appointments as $appointment): ?>
                <p>
                    <strong>With: <?= htmlspecialchars($appointment['counselor_name']) ?></strong><br>
                    Date: <?= htmlspecialchars($appointment['appointment_date']) ?><br>
                    Status: 
                    <span style="color: <?= $appointment['status'] === 'approved' ? 'green' : ($appointment['status'] === 'rejected' ? 'red' : 'orange') ?>;">
                        <?= ucfirst($appointment['status']) ?>
                    </span>
                </p>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        document.getElementById("appointmentForm").addEventListener("submit", function(event) {
            event.preventDefault();
            
            let appointmentDate = document.getElementById("appointment_date").value;

            fetch("book_appointment.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "appointment_date=" + encodeURIComponent(appointmentDate)
            })
            .then(response => response.json())
            .then(data => {
                let messageDiv = document.getElementById("message");
                if (data.status === "success") {
                    messageDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                    setTimeout(() => location.reload(), 2000); // Reload after 2 seconds
                } else {
                    messageDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                }
            });
        });

        // Function to update the notification icon dynamically
        function updateNotifications() {
            fetch("get_notifications.php")
            .then(response => response.json())
            .then(data => {
                let badge = document.querySelector(".notification .badge");
                if (data.count > 0) {
                    if (!badge) {
                        let newBadge = document.createElement("span");
                        newBadge.classList.add("badge");
                        newBadge.textContent = data.count;
                        document.querySelector(".notification").appendChild(newBadge);
                    } else {
                        badge.textContent = data.count;
                    }
                } else if (badge) {
                    badge.remove();
                }
            });
        }

        setInterval(updateNotifications, 5000); // Check every 5 seconds
    </script>

</body>
</html>
