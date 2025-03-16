<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=career', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Handle status update (Approve/Reject)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $appointment_id = $_POST['appointment_id'];
    $new_status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE appointments SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $appointment_id]);
}

// Handle appointment deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_appointment'])) {
    $appointment_id = $_POST['appointment_id'];

    $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
    $stmt->execute([$appointment_id]);
}

// Fetch all appointments
$stmt = $pdo->query("
    SELECT a.id, s.name AS student_name, c.name AS counselor_name, a.appointment_date, a.status
    FROM appointments a
    JOIN students s ON a.student_id = s.id
    JOIN counselors c ON a.counselor_id = c.id
    ORDER BY a.appointment_date DESC
");
$appointments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Appointments</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background: #f4f4f9;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        .badge-approved { background-color: #28a745; }
        .badge-rejected { background-color: #dc3545; }
        .badge-pending { background-color: #007bff; }
    </style>
</head>
<body>

<div class="container">
    <h2><i class="fas fa-calendar-check"></i> Manage Appointments</h2>
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>Student</th>
                <th>Counselor</th>
                <th>Appointment Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($appointments as $appointment): ?>
                <tr>
                    <td><?= htmlspecialchars($appointment['student_name']) ?></td>
                    <td><?= htmlspecialchars($appointment['counselor_name']) ?></td>
                    <td><?= htmlspecialchars($appointment['appointment_date']) ?></td>
                    <td>
                        <span class="badge 
                            <?= $appointment['status'] == 'approved' ? 'badge-approved' : 
                               ($appointment['status'] == 'rejected' ? 'badge-rejected' : 'badge-pending') ?>">
                            <?= ucfirst($appointment['status']) ?>
                        </span>
                    </td>
                    <td>
                        <!-- Approve Button -->
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="appointment_id" value="<?= $appointment['id'] ?>">
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" name="update_status" class="btn btn-success btn-sm">
                                <i class="fas fa-check"></i> Approve
                            </button>
                        </form>

                        <!-- Reject Button -->
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="appointment_id" value="<?= $appointment['id'] ?>">
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" name="update_status" class="btn btn-danger btn-sm">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        </form>

                        <!-- Delete Button with Confirmation Modal -->
                        <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $appointment['id'] ?>">
                            <i class="fas fa-trash"></i> Delete
                        </button>

                        <!-- Delete Confirmation Modal -->
                        <div class="modal fade" id="deleteModal<?= $appointment['id'] ?>" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Confirm Deletion</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete this appointment?
                                    </div>
                                    <div class="modal-footer">
                                        <form method="post">
                                            <input type="hidden" name="appointment_id" value="<?= $appointment['id'] ?>">
                                            <button type="submit" name="delete_appointment" class="btn btn-danger">Delete</button>
                                        </form>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Modal -->

                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
