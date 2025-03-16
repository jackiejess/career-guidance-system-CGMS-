<?php
session_start();

// Ensure counselor is logged in
if (!isset($_SESSION['counselor_id'])) {
    die("Error: You must be logged in to access this page.");
}
$counselor_id = $_SESSION['counselor_id'];

// Ensure assessment ID is provided
if (!isset($_GET['id'])) {
    die("Error: Assessment ID is missing.");
}
$assessment_id = $_GET['id'];

// Connect to the database
$pdo = new PDO('mysql:host=localhost;dbname=career', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Fetch assessment details
$stmt = $pdo->prepare("SELECT * FROM assessments WHERE id = ? AND counselor_id = ?");
$stmt->execute([$assessment_id, $counselor_id]);
$assessment = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if assessment exists
if (!$assessment) {
    die("Error: Assessment not found or you do not have permission to edit it.");
}

// Fetch questions for this assessment
$stmt = $pdo->prepare("SELECT * FROM questions WHERE assessment_id = ?");
$stmt->execute([$assessment_id]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST['title'] ?? '';
    $deadline = $_POST['deadline'] ?? '';

    // Validate input
    if (empty($title) || empty($deadline)) {
        $error = "All fields are required.";
    } else {
        // Update assessment details
        $stmt = $pdo->prepare("UPDATE assessments SET title = ?, deadline = ? WHERE id = ? AND counselor_id = ?");
        $stmt->execute([$title, $deadline, $assessment_id, $counselor_id]);

        // Update questions
        foreach ($_POST['questions'] as $question_id => $data) {
            $question_text = $data['text'] ?? '';
            $question_type = $data['type'] ?? '';
            $options = $data['options'] ?? '';

            if ($question_type == 'multiple_choice' && !empty($options)) {
                $options = implode(',', array_map('trim', explode("\n", $options))); // Store as CSV
            } else {
                $options = null;
            }

            $stmt = $pdo->prepare("UPDATE questions SET question_text = ?, question_type = ?, options = ? WHERE id = ? AND assessment_id = ?");
            $stmt->execute([$question_text, $question_type, $options, $question_id, $assessment_id]);
        }

        // Redirect back to manage page
        header("Location: manage_assessment.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Assessment</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 600px; margin: auto; }
        input, textarea, select, button { width: 100%; padding: 10px; margin-top: 10px; }
        button { background: blue; color: white; border: none; cursor: pointer; }
        .error { color: red; }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Assessment</h2>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    
    <form method="POST">
        <label>Title:</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($assessment['title']); ?>" required>

        <label>Deadline:</label>
        <input type="date" name="deadline" value="<?php echo htmlspecialchars($assessment['deadline']); ?>" required>

        <h3>Edit Questions</h3>
        <?php foreach ($questions as $question): ?>
            <div class="question">
                <label>Question:</label>
                <textarea name="questions[<?php echo $question['id']; ?>][text]" required><?php echo htmlspecialchars($question['question_text']); ?></textarea>

                <label>Type:</label>
                <select name="questions[<?php echo $question['id']; ?>][type]" required>
                    <option value="text" <?php echo $question['question_type'] === 'text' ? 'selected' : ''; ?>>Text</option>
                    <option value="multiple_choice" <?php echo $question['question_type'] === 'multiple_choice' ? 'selected' : ''; ?>>Multiple Choice</option>
                </select>

                <label>Options (if multiple choice, separate with new lines):</label>
                <textarea name="questions[<?php echo $question['id']; ?>][options]"><?php echo $question['question_type'] === 'multiple_choice' ? htmlspecialchars(str_replace(',', "\n", $question['options'])) : ''; ?></textarea>
            </div>
        <?php endforeach; ?>

        <button type="submit">Update Assessment</button>
    </form>
</div>

</body>
</html>
