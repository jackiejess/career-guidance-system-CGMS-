<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=career', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Ensure student is logged in
if (!isset($_SESSION['student_id'])) {
    die("You must be logged in as a student.");
}

$student_id = $_SESSION['student_id'];

$stmt = $pdo->prepare("SELECT a.appointment_date, a.status, c.name AS counselor_name 
    FROM appointments a
    JOIN counselors c ON a.counselor_id = c.id
    WHERE a.student_id = ?");
$stmt->execute([$student_id]);
$appointments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Appointments</title>
</head>
<body>
    <h2>My Appointments</h2>
    <?php foreach ($appointments as $appointment): ?>
        <p>
            <strong>With: <?= htmlspecialchars($appointment['counselor_name']) ?></strong> - <?= htmlspecialchars($appointment['appointment_date']) ?> 
            <span style="color: <?= $appointment['status'] === 'approved' ? 'green' : ($appointment['status'] === 'rejected' ? 'red' : 'orange') ?>;">
                (<?= ucfirst($appointment['status']) ?>)
            </span>
        </p>
    <?php endforeach; ?>
</body>
</html>
