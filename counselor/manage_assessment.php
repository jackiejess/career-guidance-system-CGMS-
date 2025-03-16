<?php
session_start();

// Ensure counselor is logged in
if (!isset($_SESSION['counselor_id'])) {
    die("Error: You must be logged in to access this page.");
}
$counselor_id = $_SESSION['counselor_id'];

// Connect to the database
$pdo = new PDO('mysql:host=localhost;dbname=career', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Fetch assessments created by the logged-in counselor
$stmt = $pdo->prepare("SELECT id, title, deadline FROM assessments WHERE counselor_id = ?");
$stmt->execute([$counselor_id]);
$assessments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Assessments</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 10px; text-align: left; }
        th { background: #f4f4f4; }
        .actions { display: flex; gap: 10px; }
        .btn { padding: 5px 10px; cursor: pointer; text-decoration: none; border-radius: 5px; }
        .edit { background: orange; color: white; }
        .delete { background: red; color: white; }
    </style>
</head>
<body>

<div class="container">
    <h2>Manage Assessments</h2>
    <a href="create_assessment.php" class="btn" style="background: green; color: white;">Create New Assessment</a>
    <table>
        <tr>
            <th>Title</th>
            <th>Deadline</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($assessments as $assessment): ?>
            <tr>
                <td><?php echo htmlspecialchars($assessment['title']); ?></td>
                <td><?php echo htmlspecialchars($assessment['deadline']); ?></td>
                <td class="actions">
                    <a href="edit_assessments.php?id=<?php echo $assessment['id']; ?>" class="btn edit">Edit</a>
                    <a href="delete_assessment.php?id=<?php echo $assessment['id']; ?>" class="btn delete" onclick="return confirm('Are you sure you want to delete this assessment?');">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

</body>
</html>
