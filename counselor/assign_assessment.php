<?php
session_start();

// Ensure the counselor is logged in
if (!isset($_SESSION['counselor_id'])) {
    die("Error: You must be logged in to access this page.");
}

$counselor_id = $_SESSION['counselor_id'];

// Connect to the database
$pdo = new PDO('mysql:host=localhost;dbname=career', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$message = ""; // Message for success/errors

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['assessment_id']) || !isset($_POST['students']) || empty($_POST['students'])) {
        $message = "<div class='alert alert-danger'>Please select an assessment and at least one student.</div>";
    } else {
        try {
            $assessment_id = htmlspecialchars($_POST['assessment_id']);
            $students = $_POST['students'];

            // Verify if assessment exists
            $stmt = $pdo->prepare("SELECT id FROM assessments WHERE id = ?");
            $stmt->execute([$assessment_id]);

            if ($stmt->rowCount() === 0) {
                throw new Exception("Error: The selected assessment does not exist.");
            }

            // Assign students to the assessment
            $stmt = $pdo->prepare("INSERT INTO assessment_students (assessment_id, student_id) VALUES (?, ?)");

            foreach ($students as $student_id) {
                $stmt->execute([$assessment_id, htmlspecialchars($student_id)]);
            }

            $message = "<div class='alert alert-success'>Students assigned successfully!</div>";
        } catch (Exception $e) {
            $message = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
        }
    }
}

// Fetch assessments assigned to this counselor
$stmt = $pdo->prepare("
    SELECT id, title 
    FROM assessments 
    WHERE counselor_id = ?
    ORDER BY title ASC
");
$stmt->execute([$counselor_id]);
$assessments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch only students assigned to the logged-in counselor
$stmt = $pdo->prepare("
    SELECT id, name 
    FROM students
    WHERE counselor_id = ?
    ORDER BY name ASC
");
$stmt->execute([$counselor_id]);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Students</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 600px; margin: auto; }
        .alert { padding: 10px; border-radius: 5px; margin-bottom: 10px; }
        .alert-danger { background-color: #f8d7da; color: #721c24; }
        .alert-success { background-color: #d4edda; color: #155724; }
        select, button { width: 100%; padding: 10px; margin-top: 10px; }
        #student-list { max-height: 200px; overflow-y: auto; border: 1px solid #ccc; padding: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Assign Students to Assessment</h2>
        <?php echo $message; ?>

        <form action="" method="POST">
            <label for="assessment_id"><strong>Select Assessment:</strong></label>
            <select name="assessment_id" id="assessment_id" required>
                <option value="">-- Select Assessment --</option>
                <?php foreach ($assessments as $assessment): ?>
                    <option value="<?= htmlspecialchars($assessment['id']) ?>"><?= htmlspecialchars($assessment['title']) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="students"><strong>Select Students:</strong></label>
            <div id="student-list">
                <input type="checkbox" id="select-all"> <label for="select-all">Select All</label><br>
                <?php foreach ($students as $student): ?>
                    <input type="checkbox" name="students[]" value="<?= htmlspecialchars($student['id']) ?>">
                    <?= htmlspecialchars($student['name']) ?><br>
                <?php endforeach; ?>
            </div>

            <button type="submit">Assign Students</button>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            $("#select-all").change(function() {
                $("input[name='students[]']").prop("checked", this.checked);
            });
        });
    </script>
</body>
</html>
