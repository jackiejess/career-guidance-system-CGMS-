<?php
session_start();

// Ensure student is logged in
if (!isset($_SESSION['student_id'])) {
    die("Error: You must be logged in to access this page.");
}

$student_id = $_SESSION['student_id'];

// Connect to the database
$pdo = new PDO('mysql:host=localhost;dbname=career', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Get the student's assigned counselor
$stmt = $pdo->prepare("SELECT counselor_id FROM students WHERE id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student || !$student['counselor_id']) {
    die("Error: You are not assigned to a counselor yet.");
}

$counselor_id = $student['counselor_id'];

// Fetch resources shared by the assigned counselor
$stmt = $pdo->prepare("SELECT * FROM resources WHERE counselor_id = ?");
$stmt->execute([$counselor_id]);
$resources = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 700px; margin: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f4f4f4; }
        a { color: blue; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Student Dashboard</h2>
        <h3>Shared Resources</h3>
        <table>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>File</th>
                <th>Link</th>
            </tr>
            <?php foreach ($resources as $resource): ?>
                <tr>
                    <td><?= htmlspecialchars($resource['title']) ?></td>
                    <td><?= htmlspecialchars($resource['description']) ?></td>
                    <td>
                        <?php if ($resource['file_path']): ?>
                            <a href="../counselor/uploads/resources/<?= htmlspecialchars(basename($resource['file_path'])) ?>" download>Download</a>

                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($resource['resource_link']): ?>
                            <a href="<?= htmlspecialchars($resource['resource_link']) ?>" target="_blank">View</a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
