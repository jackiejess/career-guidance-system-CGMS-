<?php
session_start();

// Ensure admin is logged in
if (!isset($_SESSION['counselor_id'])) {
    die("Error: You must be logged in as an admin to view responses.");
}

// Database connection with error handling
try {
    $pdo = new PDO('mysql:host=localhost;dbname=career', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Fetch students who have responded
$students = $pdo->query("SELECT DISTINCT students.id, students.name FROM responses 
                        JOIN students ON responses.student_id = students.id")->fetchAll(PDO::FETCH_ASSOC);

// Fetch responses if a student is selected
$responses = [];
if (!empty($_GET['student_id'])) {
    $stmt = $pdo->prepare("SELECT assessments.title AS assessment_title, questions.question_text, 
                            questions.question_type, responses.answer, responses.submitted_at
                            FROM responses
                            JOIN questions ON responses.question_id = questions.id
                            JOIN assessments ON questions.assessment_id = assessments.id
                            WHERE responses.student_id = ?");
    $stmt->execute([$_GET['student_id']]);
    $responses = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Responses</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f4f4f4; }
        .container { max-width: 900px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); }
        h2 { text-align: center; color: #333; }
        .student-list, .response-table { margin-top: 20px; }
        .student-list ul { list-style: none; padding: 0; }
        .student-list li { background: #007bff; color: white; padding: 10px; margin: 5px 0; border-radius: 5px; }
        .student-list a { color: white; text-decoration: none; display: block; }
        .student-list a:hover { background: #0056b3; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #333; color: white; }
    </style>
</head>
<body>

<div class="container">
    <h2>Student Responses</h2>

    <!-- Student List -->
    <div class="student-list">
        <h3>Students who responded:</h3>
        <ul>
            <?php foreach ($students as $student): ?>
                <li><a href="?student_id=<?= htmlspecialchars($student['id']); ?>"> <?= htmlspecialchars($student['name']); ?> </a></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Responses Table -->
    <?php if (!empty($_GET['student_id'])): ?>
    <div class="response-table">
        <h3>Responses from <?= htmlspecialchars($students[array_search($_GET['student_id'], array_column($students, 'id'))]['name']); ?></h3>
        <table>
            <thead>
                <tr>
                    <th>Assessment Title</th>
                    <th>Question</th>
                    <th>Question Type</th>
                    <th>Response</th>
                    <th>Submitted At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($responses as $response): ?>
                    <tr>
                        <td><?= htmlspecialchars($response['assessment_title']); ?></td>
                        <td><?= htmlspecialchars($response['question_text']); ?></td>
                        <td><?= htmlspecialchars($response['question_type']); ?></td>
                        <td><?= htmlspecialchars($response['answer']); ?></td>
                        <td><?= htmlspecialchars($response['submitted_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

</body>
</html>
