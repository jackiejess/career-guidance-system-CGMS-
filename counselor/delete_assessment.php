<?php
session_start();

// Ensure counselor is logged in
if (!isset($_SESSION['counselor_id'])) {
    die("Error: You must be logged in to delete an assessment.");
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

// Check if the assessment belongs to the logged-in counselor
$stmt = $pdo->prepare("SELECT * FROM assessments WHERE id = ? AND counselor_id = ?");
$stmt->execute([$assessment_id, $counselor_id]);
$assessment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$assessment) {
    die("Error: Assessment not found or you do not have permission to delete it.");
}

// Handle deletion confirmation
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['confirm_delete'])) {
        try {
            $pdo->beginTransaction();

            // Delete all records from `assessment_students` related to the assessment
            $stmt = $pdo->prepare("DELETE FROM assessment_students WHERE assessment_id = ?");
            $stmt->execute([$assessment_id]);

            // Delete all questions related to the assessment
            $stmt = $pdo->prepare("DELETE FROM questions WHERE assessment_id = ?");
            $stmt->execute([$assessment_id]);

            // Delete the assessment itself
            $stmt = $pdo->prepare("DELETE FROM assessments WHERE id = ? AND counselor_id = ?");
            $stmt->execute([$assessment_id, $counselor_id]);

            $pdo->commit();

            // Redirect after deletion
            header("Location: manage_assessment.php?deleted=1");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            die("Error: " . $e->getMessage());
        }
    } else {
        // Redirect if deletion is canceled
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
    <title>Delete Assessment</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 500px; margin: auto; text-align: center; }
        .btn { padding: 10px 15px; margin: 5px; border: none; cursor: pointer; }
        .btn-danger { background: red; color: white; }
        .btn-secondary { background: gray; color: white; }
    </style>
</head>
<body>

<div class="container">
    <h2>Delete Assessment</h2>
    <p>Are you sure you want to delete the assessment "<strong><?php echo htmlspecialchars($assessment['title']); ?></strong>"?</p>
    
    <form method="POST">
        <button type="submit" name="confirm_delete" class="btn btn-danger">Yes, Delete</button>
        <a href="manage_assessments.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

</body>
</html>
