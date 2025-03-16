<?php
include '../db.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    // Ensure uploads directory exists
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $profile_picture = "default.png"; // Default profile image

    if (!empty($_FILES["profile_picture"]["name"])) {
        $file_name = time() . "_" . basename($_FILES["profile_picture"]["name"]);
        $target_file = $target_dir . $file_name;
        
        // Validate file type
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ["jpg", "jpeg", "png", "gif"];

        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                $profile_picture = $file_name; // Save uploaded file
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Invalid file format. Only JPG, PNG, JPEG, GIF allowed.";
        }
    }

    // Check if email already exists using prepared statement
    $stmt = $conn->prepare("SELECT id FROM students WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = "Email already registered!";
    } else {
        // Insert user using prepared statement
        $stmt = $conn->prepare("INSERT INTO students (name, email, password, profile_picture) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $password, $profile_picture);
        
        if ($stmt->execute()) {
            $success = "Registration successful! <a href='login.php'>Login here</a>";
        } else {
            $error = "Error: " . $conn->error;
        }
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student Registration</title>
    <link rel="stylesheet" href="css/register.css">
</head>
<body>
    <div class="register-container">
        <h2>Register</h2>
        <?php if ($error) echo "<p class='error'>$error</p>"; ?>
        <?php if ($success) echo "<p class='success'>$success</p>"; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="input-group">
                <label>Full Name:</label>
                <input type="text" name="name" placeholder="Enter full name" required>
            </div>
            <div class="input-group">
                <label>Email:</label>
                <input type="email" name="email" placeholder="Enter email" required>
            </div>
            <div class="input-group">
                <label>Password:</label>
                <input type="password" name="password" placeholder="Enter password" required>
            </div>
            <div class="input-group">
                <label>Upload Profile Picture:</label>
                <input type="file" name="profile_picture" accept="image/*">
            </div>
            <button type="submit" class="register-btn">Register</button>
        </form>
        <p class="login-link">Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>
