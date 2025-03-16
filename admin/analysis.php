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
    SELECT s.name AS student_name, COUNT(ast.assessment_id) AS assessment_count
    FROM students s
    LEFT JOIN assessment_students ast ON s.id = ast.student_id
    GROUP BY s.id
");
$studentData = $studentQuery->fetchAll(PDO::FETCH_ASSOC);

// Convert data to JSON for Chart.js
$counselorNames = json_encode(array_column($counselorData, 'counselor_name'));
$sessionCounts = json_encode(array_column($counselorData, 'session_count'));
$studentNames = json_encode(array_column($studentData, 'student_name'));
$assessmentCounts = json_encode(array_column($studentData, 'assessment_count'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }
        .chart-container {
            width: 80%;
            margin: 20px auto;
        }
    </style>
</head>
<body>

    <h2>📊 Analytics Dashboard</h2>

    <!-- Counselors Sessions Chart -->
    <div class="chart-container">
        <h3>🧑‍🏫 Sessions Created by Counselors</h3>
        <canvas id="counselorChart"></canvas>
    </div>

    <!-- Students Assessment Chart -->
    <div class="chart-container">
        <h3>🎓 Students Who Took Assessments</h3>
        <canvas id="studentChart"></canvas>
    </div>

    <script>
        // Counselors Chart
        const ctx1 = document.getElementById('counselorChart').getContext('2d');
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: <?= $counselorNames ?>,
                datasets: [{
                    label: 'Sessions Created',
                    data: <?= $sessionCounts ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Students Assessment Chart
        const ctx2 = document.getElementById('studentChart').getContext('2d');
        new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: <?= $studentNames ?>,
                datasets: [{
                    label: 'Assessments Taken',
                    data: <?= $assessmentCounts ?>,
                    backgroundColor: ['#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#9966ff'],
                }]
            },
            options: {
                responsive: true
            }
        });
    </script>

</body>
</html>
