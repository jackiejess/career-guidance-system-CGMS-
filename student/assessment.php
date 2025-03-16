<?php
session_start();

// Ensure the student is logged in
if (!isset($_SESSION['student_id'])) {
    die("Error: You must be logged in to access this page.");
}

$student_id = $_SESSION['student_id'];

// Connect to the database
try {
    $pdo = new PDO('mysql:host=localhost;dbname=career', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if the student has a counselor assigned
$stmt = $pdo->prepare("SELECT counselor_id FROM students WHERE id = ? LIMIT 1");
$stmt->execute([$student_id]);
$counselor_id = $stmt->fetchColumn();

if (!$counselor_id) {
    // Redirect to the counselor selection page if no counselor is assigned
    header("Location: student_select_counselor.php");
    exit();
}

// Fetch assessments assigned to the student
$stmt = $pdo->prepare("
    SELECT a.id, a.title, a.deadline 
    FROM assessments a
    JOIN assessment_students s ON a.id = s.assessment_id
    WHERE s.student_id = ?
");
$stmt->execute([$student_id]);
$assessments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Assessments</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        button { padding: 5px 10px; background: blue; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>

<h2>Available Assessments</h2>

<?php if (empty($assessments)): ?>
    <p>No assessments assigned yet.</p>
<?php else: ?>
    <table>
        <tr>
            <th>Title</th>
            <th>Deadline</th>
            <th>Action</th>
        </tr>
        <?php foreach ($assessments as $assessment): ?>
            <tr>
                <td><?php echo htmlspecialchars($assessment['title']); ?></td>
                <td><?php echo htmlspecialchars($assessment['deadline']); ?></td>
                <td>
                    <a href="take_assessment.php?assessment_id=<?php echo $assessment['id']; ?>">
                        <button>Take Assessment</button>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

</body>
</html>
