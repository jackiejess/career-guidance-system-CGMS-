<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=career', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Ensure counselor is logged in
if (!isset($_SESSION['counselor_id'])) {
    die("You must be logged in as a counselor.");
}

$counselor_id = $_SESSION['counselor_id'];

// Update appointment status using AJAX
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === "update_status") {
    $appointment_id = $_POST['appointment_id'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE appointments SET status = ? WHERE id = ? AND counselor_id = ?");
    $stmt->execute([$status, $appointment_id, $counselor_id]);

    echo json_encode(['success' => true]);
    exit;
}

// Fetch pending appointments
$stmt = $pdo->prepare("SELECT a.id, s.name, a.appointment_date, a.status 
    FROM appointments a
    JOIN students s ON a.student_id = s.id
    WHERE a.counselor_id = ?");
$stmt->execute([$counselor_id]);
$appointments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Appointments</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            background: #fff;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #343a40;
            margin-bottom: 20px;
        }
        .appointment {
            margin-bottom: 15px;
        }
        .btn {
            margin-right: 10px;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
    <div class="container">
        <h2>Manage Student Appointments</h2>
        <?php foreach ($appointments as $appointment): ?>
            <div class="appointment">
                <p><strong><?= htmlspecialchars($appointment['name']) ?></strong> - <?= htmlspecialchars($appointment['appointment_date']) ?></p>
                <button class="btn btn-success update-status" data-id="<?= $appointment['id'] ?>" data-status="approved">Approve</button>
                <button class="btn btn-danger update-status" data-id="<?= $appointment['id'] ?>" data-status="rejected">Reject</button>
            </div>
        <?php endforeach; ?>
        <div id="response-message" style="display: none;" class="alert"></div>
    </div>
    <script>
        $(document).ready(function() {
            $('.update-status').on('click', function() {
                const appointmentId = $(this).data('id');
                const status = $(this).data('status');

                $.ajax({
                    url: "",
                    method: "POST",
                    data: {
                        action: "update_status",
                        appointment_id: appointmentId,
                        status: status
                    },
                    success: function(response) {
                        const result = JSON.parse(response);
                        if (result.success) {
                            $('#response-message')
                                .removeClass()
                                .addClass('alert alert-success')
                                .text('Appointment status updated successfully!')
                                .show();
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        } else {
                            $('#response-message')
                                .removeClass()
                                .addClass('alert alert-danger')
                                .text('Failed to update appointment status.')
                                .show();
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
