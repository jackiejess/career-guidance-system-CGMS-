<?php
// Connect to the database
$pdo = new PDO('mysql:host=localhost;dbname=career', 'root', '');

$assessment_id = $_POST['assessment_id'];
$student_id = $_POST['student_id'];
$scores = $_POST['score'];
$feedbacks = $_POST['feedback'];

// Calculate total score
$total_score = array_sum($scores);

// Save results
$stmt = $pdo->prepare("INSERT INTO results (student_id, assessment_id, score, feedback) VALUES (?, ?, ?, ?)");
$stmt->execute([$student_id, $assessment_id, $total_score, implode("\n", $feedbacks)]);

echo "Grades and feedback saved successfully!";
?>