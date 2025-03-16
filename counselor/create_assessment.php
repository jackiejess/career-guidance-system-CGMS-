<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=career;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Redirect if not logged in
if (!isset($_SESSION['counselor_id'])) {
    header("Location: login.php");
    exit;
}

$message = ""; // Store success or error messages

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $counselor_id = $_SESSION['counselor_id'];

        // Validate and sanitize input
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
        $deadline = filter_input(INPUT_POST, 'deadline', FILTER_SANITIZE_STRING);

        if (!$title || !$deadline) {
            throw new Exception("Assessment title and deadline are required.");
        }

        // Insert assessment
        $stmt = $pdo->prepare("INSERT INTO assessments (title, description, deadline, counselor_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $description, $deadline, $counselor_id]);
        $assessment_id = $pdo->lastInsertId();

        // Insert questions if provided
        if (!empty($_POST['question_text'])) {
            foreach ($_POST['question_text'] as $index => $question_text) {
                $question_text = filter_var(trim($question_text), FILTER_SANITIZE_STRING);
                $question_type = $_POST['question_type'][$index];
                $options = isset($_POST['options'][$index]) ? filter_var(trim($_POST['options'][$index]), FILTER_SANITIZE_STRING) : null;

                if (!$question_text) {
                    throw new Exception("All questions must have text.");
                }
                if ($question_type === "multiple_choice" && !$options) {
                    throw new Exception("Multiple-choice questions must have options.");
                }

                $stmt = $pdo->prepare("INSERT INTO questions (assessment_id, question_text, question_type, options) VALUES (?, ?, ?, ?)");
                $stmt->execute([$assessment_id, $question_text, $question_type, $options]);
            }
        }

        $message = "<div class='alert alert-success'>Assessment created successfully!</div>";

    } catch (Exception $e) {
        $message = "<div class='alert alert-danger'>{$e->getMessage()}</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Assessment</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body { background-color: #f4f4f9; padding: 20px; }
        .container { max-width: 700px; background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); }
        .question { background: #f8f9fa; padding: 15px; margin-bottom: 15px; border-radius: 5px; position: relative; }
        .remove-btn { position: absolute; top: 10px; right: 10px; cursor: pointer; color: red; font-size: 16px; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center mb-4">Create New Assessment</h2>

    <!-- Display Messages -->
    <?= $message; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Assessment Title:</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description:</label>
            <textarea name="description" class="form-control" rows="3"></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Deadline:</label>
            <input type="datetime-local" name="deadline" class="form-control" required>
        </div>

        <h4 class="mt-4">Questions</h4>
        <div id="questions"></div>

        <button type="button" class="btn btn-primary mt-3" onclick="addQuestion()">➕ Add Question</button>
        <button type="submit" class="btn btn-success mt-3">✅ Create Assessment</button>
    </form>
</div>

<script>
    function addQuestion() {
        const questionsDiv = document.getElementById('questions');

        const questionHTML = `
            <div class="question">
                <span class="remove-btn" onclick="removeQuestion(this)">✖</span>
                
                <div class="mb-2">
                    <label class="form-label">Question Text:</label>
                    <input type="text" name="question_text[]" class="form-control" required>
                </div>

                <div class="mb-2">
                    <label class="form-label">Question Type:</label>
                    <select name="question_type[]" class="form-select" onchange="toggleOptions(this)">
                        <option value="multiple_choice">Multiple Choice</option>
                        <option value="short_answer">Short Answer</option>
                    </select>
                </div>

                <div class="mb-2 options-container">
                    <label class="form-label">Options (comma-separated for MCQs):</label>
                    <input type="text" name="options[]" class="form-control">
                </div>
            </div>
        `;

        questionsDiv.insertAdjacentHTML('beforeend', questionHTML);
    }

    function removeQuestion(element) {
        element.parentElement.remove();
    }

    function toggleOptions(selectElement) {
        const optionsContainer = selectElement.closest('.question').querySelector('.options-container');
        optionsContainer.style.display = selectElement.value === 'multiple_choice' ? 'block' : 'none';
    }
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

</body>
</html>
