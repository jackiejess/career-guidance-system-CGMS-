<?php
session_start();

// Ensure counselor is logged in
if (!isset($_SESSION['counselor_id'])) {
    die("Error: You must be logged in to access this page.");
}

$counselor_id = $_SESSION['counselor_id'];

// Connect to the database
$pdo = new PDO('mysql:host=localhost;dbname=career', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$message = "";

// Handle resource upload
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['upload_resource'])) {
    try {
        $title = htmlspecialchars($_POST['title']);
        $description = htmlspecialchars($_POST['description']);
        $resource_link = !empty($_POST['resource_link']) ? htmlspecialchars($_POST['resource_link']) : NULL;
        $file_path = NULL;

        // Handle file upload
        if (!empty($_FILES['resource_file']['name'])) {
            $target_dir = "uploads/resources/";
            $file_name = basename($_FILES["resource_file"]["name"]);
            $target_file = $target_dir . time() . "_" . $file_name;
            move_uploaded_file($_FILES["resource_file"]["tmp_name"], $target_file);
            $file_path = $target_file;
        }

        // Insert resource into database
        $stmt = $pdo->prepare("
            INSERT INTO resources (counselor_id, title, description, file_path, resource_link) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$counselor_id, $title, $description, $file_path, $resource_link]);

        $message = "<div class='alert alert-success'>Resource shared successfully!</div>";
    } catch (Exception $e) {
        $message = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}

// Fetch students assigned to this counselor
$stmt = $pdo->prepare("SELECT id, name, email FROM students WHERE counselor_id = ?");
$stmt->execute([$counselor_id]);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch shared resources
$stmt = $pdo->prepare("SELECT * FROM resources WHERE counselor_id = ?");
$stmt->execute([$counselor_id]);
$resources = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Counselor Dashboard</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 700px; margin: auto; }
        .alert { padding: 10px; border-radius: 5px; margin-bottom: 10px; }
        .alert-danger { background-color: #f8d7da; color: #721c24; }
        .alert-success { background-color: #d4edda; color: #155724; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f4f4f4; }
        input, textarea, button { width: 100%; padding: 10px; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Counselor Dashboard</h2>
        <?php echo $message; ?>

        <h3>Assigned Students</h3>
        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
            </tr>
            <?php foreach ($students as $student): ?>
                <tr>
                    <td><?= htmlspecialchars($student['name']) ?></td>
                    <td><?= htmlspecialchars($student['email']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h3>Share a Resource</h3>
        <form action="" method="POST" enctype="multipart/form-data">
            <label>Title:</label>
            <input type="text" name="title" required>

            <label>Description:</label>
            <textarea name="description"></textarea>

            <label>Upload File (Optional):</label>
            <input type="file" name="resource_file">

            <label>Resource Link (Optional):</label>
            <input type="url" name="resource_link">

            <button type="submit" name="upload_resource">Share Resource</button>
        </form>

        <h3>Shared Resources</h3>
        <table>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>File</th>
                <th>Link</th>
            </tr>
            <?php foreach ($resources as $resource): ?>
                <tr>
                    <td><?= htmlspecialchars($resource['title']) ?></td>
                    <td><?= htmlspecialchars($resource['description']) ?></td>
                    <td>
                        <?php if ($resource['file_path']): ?>
                            <a href="<?= htmlspecialchars($resource['file_path']) ?>" download>Download</a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($resource['resource_link']): ?>
                            <a href="<?= htmlspecialchars($resource['resource_link']) ?>" target="_blank">View</a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
``
