<?php
session_start(); // Start the session

// Check if counselor is logged in
if (!isset($_SESSION['counselor_id'])) {
    die("Error: Counselor not logged in.");
}

// Connect to the database
$pdo = new PDO('mysql:host=localhost;dbname=career', 'root', '');
$counselor_id = $_SESSION['counselor_id'];

// Fetch assessments created by the counselor
$stmt = $pdo->prepare("
    SELECT a.id, a.title, COUNT(r.id) AS submissions
    FROM assessments a
    LEFT JOIN responses r ON a.id = (SELECT assessment_id FROM questions WHERE id = r.question_id)
    WHERE a.counselor_id = ?
    GROUP BY a.id
");
$stmt->execute([$counselor_id]);
$assessments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submitted Assessments</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 20px;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            background: #fff;
            margin: 10px 0;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }
        a:hover {
            color: #0056b3;
        }
    </style>
</head>
<body>
    <h2>Submitted Assessments</h2>
    <ul>
        <?php foreach ($assessments as $assessment): ?>
            <li>
                <a href="view_submissions.php?assessment_id=<?php echo $assessment['id']; ?>">
                    <?php echo htmlspecialchars($assessment['title']); ?>
                </a>
                (Submissions: <?php echo $assessment['submissions']; ?>)
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
