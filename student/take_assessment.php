<?php
session_start();

// Ensure student is logged in
if (!isset($_SESSION['student_id'])) {
    die("Error: You must be logged in to access this page.");
}
$student_id = $_SESSION['student_id'];

// Ensure assessment_id is provided
if (!isset($_GET['assessment_id'])) {
    die("Error: Assessment ID is missing.");
}
$assessment_id = $_GET['assessment_id'];

// Connect to the database
$pdo = new PDO('mysql:host=localhost;dbname=career', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Check if the student has already taken the assessment
$stmt = $pdo->prepare("SELECT COUNT(*) FROM responses WHERE student_id = ? AND question_id IN (SELECT id FROM questions WHERE assessment_id = ?)");
$stmt->execute([$student_id, $assessment_id]);
$hasTakenAssessment = $stmt->fetchColumn() > 0;

if ($hasTakenAssessment) {
    echo "<p style='color: red; font-weight: bold;'>You have already taken this assessment.</p>";
    exit;
}

// Fetch assessment details
$stmt = $pdo->prepare("SELECT title, deadline FROM assessments WHERE id = ?");
$stmt->execute([$assessment_id]);
$assessment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$assessment) {
    die("Error: Assessment not found.");
}

// Fetch assessment questions
$stmt = $pdo->prepare("SELECT id, question_text, question_type, options FROM questions WHERE assessment_id = ?");
$stmt->execute([$assessment_id]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Assessment</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: auto; }
        .question { margin-bottom: 15px; }
        textarea { width: 100%; height: 80px; }
        button { padding: 10px 15px; background: blue; color: white; border: none; cursor: pointer; }
        .error { color: red; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <h2><?php echo htmlspecialchars($assessment['title']); ?></h2>
    <p><strong>Deadline:</strong> <?php echo htmlspecialchars($assessment['deadline']); ?></p>

    <form id="assessmentForm" action="submit_assessment.php" method="POST">
        <input type="hidden" name="assessment_id" value="<?php echo htmlspecialchars($assessment_id); ?>">
        <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>">

        <?php foreach ($questions as $question): ?>
            <div class="question">
                <p><?php echo htmlspecialchars($question['question_text']); ?></p>

                <?php if ($question['question_type'] === 'multiple_choice'): ?>
                    <?php
                    $options = explode(',', $question['options']);
                    foreach ($options as $index => $option): ?>
                        <label>
                            <input type="radio" name="response[<?php echo $question['id']; ?>]" value="<?php echo $index + 1; ?>">
                            <?php echo htmlspecialchars($option); ?>
                        </label><br>
                    <?php endforeach; ?>

                <?php else: ?>
                    <textarea name="response[<?php echo $question['id']; ?>]" required></textarea>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <p class="error" id="errorMessage"></p>
        <button type="submit">Submit Assessment</button>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("assessmentForm").addEventListener("submit", function (e) {
        e.preventDefault();

        let isValid = true;
        let errorMessage = "";
        let formData = new FormData(this);

        document.querySelectorAll(".question").forEach(function (question) {
            let inputs = question.querySelectorAll("input[type=radio]:checked, textarea");
            if (inputs.length === 0 || (inputs[0].tagName === "TEXTAREA" && inputs[0].value.trim() === "")) {
                isValid = false;
                errorMessage = "Please answer all questions before submitting.";
            }
        });

        if (!isValid) {
            document.getElementById("errorMessage").innerText = errorMessage;
            return;
        }

        fetch("submit_assessment.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById("assessmentForm").innerHTML = "<p><strong>Assessment Submitted Successfully!</strong></p>";
            } else {
                document.getElementById("errorMessage").innerText = "Error submitting assessment.";
            }
        })
        .catch(error => console.error("Error:", error));
    });
});
</script>

</body>
</html>
