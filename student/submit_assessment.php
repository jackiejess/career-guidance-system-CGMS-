<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die(json_encode(["success" => false, "message" => "Invalid request method."]));
}

// Ensure student is logged in
if (!isset($_SESSION['student_id'])) {
    echo json_encode(["success" => false, "message" => "You must be logged in to submit an assessment."]);
    exit;
}

$student_id = $_SESSION['student_id'];
$assessment_id = $_POST['assessment_id'] ?? null;
$responses = $_POST['response'] ?? [];

if (!$assessment_id || empty($responses)) {
    echo json_encode(["success" => false, "message" => "All questions must be answered."]);
    exit;
}

// Connect to the database
$pdo = new PDO('mysql:host=localhost;dbname=career', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

try {
    $pdo->beginTransaction();

    // Insert responses
    $stmt = $pdo->prepare("INSERT INTO responses (student_id, question_id, answer) VALUES (?, ?, ?)");
    
    foreach ($responses as $question_id => $answer) {
        // Retrieve question type
        $qstmt = $pdo->prepare("SELECT question_type FROM questions WHERE id = ?");
        $qstmt->execute([$question_id]);
        $question = $qstmt->fetch(PDO::FETCH_ASSOC);

        if (!$question) {
            throw new Exception("Invalid question ID.");
        }

        // Ensure proper data handling
        if ($question['question_type'] === 'multiple_choice') {
            if (!is_numeric($answer)) {
                throw new Exception("Invalid multiple-choice answer.");
            }
        } else {
            if (trim($answer) === "") {
                throw new Exception("Short answer questions cannot be empty.");
            }
        }

        $stmt->execute([$student_id, $question_id, $answer]);
    }

    $pdo->commit();
    echo json_encode(["success" => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
exit;
?>
