<?php
session_start();

// Ensure the student is logged in
if (!isset($_SESSION['student_id'])) {
    die("Error: You must be logged in as a student to select a counselor.");
}

$student_id = $_SESSION['student_id'];

// Database connection
try {
    $pdo = new PDO('mysql:host=localhost;dbname=career', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if the student has already selected a counselor
$stmt = $pdo->prepare("SELECT counselor_id FROM students WHERE id = ?");
$stmt->execute([$student_id]);
$counselor_id = $stmt->fetchColumn();

if ($counselor_id) {
    // If the student already has a counselor, redirect to assessments
    header("Location: assessment.php");
    exit();
}

// Fetch available counselors
$counselors = $pdo->query("SELECT * FROM counselors")->fetchAll(PDO::FETCH_ASSOC);

// Handle counselor selection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['counselor_id'])) {
    $counselor_id = $_POST['counselor_id'];

    // Update the student's record with the selected counselor
    $stmt = $pdo->prepare("UPDATE students SET counselor_id = ? WHERE id = ?");
    $stmt->execute([$counselor_id, $student_id]);

    // Redirect to the assessment page after selecting the counselor
    header("Location: assessment.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select a Counselor</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: auto; text-align: center; }
        .counselor-card {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 10px;
            background: #f9f9f9;
            transition: 0.3s;
        }
        .counselor-card:hover {
            background: #e3e3e3;
        }
        .counselor-card img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-right: 15px;
        }
        .counselor-card button {
            margin-left: auto;
            padding: 8px 12px;
            border: none;
            background: blue;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        .counselor-card button:hover {
            background: darkblue;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Select Your Counselor</h2>

    <?php foreach ($counselors as $counselor): ?>
        <form method="POST" class="counselor-card">
            <img src="uploads/<?= htmlspecialchars($counselor['profile_picture']) ?>" alt="Counselor Picture">
            <div>
                <strong><?= htmlspecialchars($counselor['name']) ?></strong><br>
                <?= htmlspecialchars($counselor['email']) ?>
            </div>
            <input type="hidden" name="counselor_id" value="<?= $counselor['id'] ?>">
            <button type="submit">Select</button>
        </form>
    <?php endforeach; ?>
</div>

</body>
</html>
