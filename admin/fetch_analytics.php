<?php
$pdo = new PDO('mysql:host=localhost;dbname=career', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Fetch counselors' session count
$counselorQuery = $pdo->query("
    SELECT c.name AS counselor_name, COUNT(a.id) AS session_count
    FROM counselors c
    LEFT JOIN appointments a ON c.id = a.counselor_id
    GROUP BY c.id
");
$counselorData = $counselorQuery->fetchAll(PDO::FETCH_ASSOC);

// Fetch students' assessment count
$studentQuery = $pdo->query("
    SELECT s.name AS student_name, COUNT(assessments.id) AS assessment_count
    FROM students s
    LEFT JOIN assessments ON s.id = assessments.student_id
    GROUP BY s.id
");
$studentData = $studentQuery->fetchAll(PDO::FETCH_ASSOC);

// Format data for Chart.js
$response = [
    'counselor_names' => array_column($counselorData, 'counselor_name'),
    'session_counts' => array_column($counselorData, 'session_count'),
    'student_names' => array_column($studentData, 'student_name'),
    'assessment_counts' => array_column($studentData, 'assessment_count')
];

echo json_encode($response);
?>
