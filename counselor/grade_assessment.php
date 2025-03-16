<?php
// Connect to the database
$pdo = new PDO('mysql:host=localhost;dbname=career', 'root', '');

$assessment_id = $_GET['assessment_id'];
$student_id = $_GET['student_id'];

// Fetch student responses
$stmt = $pdo->prepare("
    SELECT q.question_text, r.response
    FROM responses r
    JOIN questions q ON r.question_id = q.id
    WHERE q.assessment_id = ? AND r.student_id = ?
");
$stmt->execute([$assessment_id, $student_id]);
$responses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Grade Assessment</h2>
<form action="save_grade.php" method="POST">
    <input type="hidden" name="assessment_id" value="<?php echo $assessment_id; ?>">
    <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">

    <?php foreach ($responses as $response): ?>
        <div class="response">
            <p><strong>Question:</strong> <?php echo $response['question_text']; ?></p>
            <p><strong>Response:</strong> <?php echo $response['response']; ?></p>
            <label for="score">Score:</label>
            <input type="number" name="score[]" required>
            <label for="feedback">Feedback:</label>
            <textarea name="feedback[]"></textarea>
        </div>
    <?php endforeach; ?>

    <button type="submit">Save Grade</button>
</form>