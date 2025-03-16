<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=career', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$stmt = $pdo->query("
    SELECT a.id, s.name AS student_name, a.title, a.deadline, a.description
    FROM assessments a
    JOIN students s ON a.counselor_id = s.id
");
$assessments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Assessments</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body {
            background: #f4f4f9;
            padding: 20px;
        }

        .container {
            max-width: 900px;
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

        .table {
            width: 100%;
        }

        th {
            background: #2c3e50;
            color: white;
        }

        tr:hover {
            background: #f1f1f1;
        }
    </style>
</head>
<body>

<div class="container">
    <h2><i class="fas fa-clipboard-list"></i> View Assessments</h2>
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Student</th>
                <th>Assessment</th>
                <th>Date Taken</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($assessments as $assessment): ?>
                <tr>
                    <td><?= htmlspecialchars($assessment['student_name']) ?></td>
                    <td><?= htmlspecialchars($assessment['title']) ?></td>
                    <td><?= htmlspecialchars($assessment['deadline']) ?></td>
                    <td><?= htmlspecialchars($assessment['description']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
