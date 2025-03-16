<?php
session_start();

// Check if counselor is logged in
if (!isset($_SESSION['counselor_id'])) {
    header("Location: login.php");
    exit();
}

include '../db.php'; // Include your database connection file

$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];

    // Handle file upload
    if (!empty($_FILES["profile_picture"]["name"])) {
        $target_dir = "uploads/";
        $file_name = time() . "_" . basename($_FILES["profile_picture"]["name"]);
        $target_file = $target_dir . $file_name;

        // Check if the file is an image
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ["jpg", "jpeg", "png", "gif"];

        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                $_SESSION['profile_picture'] = $file_name;
                // Update the database
                $sql = "UPDATE counselors SET name='$name', email='$email', profile_picture='$file_name' WHERE id=" . $_SESSION['counselor_id'];
                if ($conn->query($sql) === TRUE) {
                    $success_message = "Profile updated successfully!";
                } else {
                    $error_message = "Error updating profile: " . $conn->error;
                }
            } else {
                $error_message = "Error uploading file.";
            }
        } else {
            $error_message = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
        }
    } else {
        // Update without changing the profile picture
        $sql = "UPDATE counselors SET name='$name', email='$email' WHERE id=" . $_SESSION['counselor_id'];
        if ($conn->query($sql) === TRUE) {
            $success_message = "Profile updated successfully!";
        } else {
            $error_message = "Error updating profile: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="css/update_profile.css">
</head>
<body>
    <div class="update-profile-container">
        <h2>Update Profile</h2>
        <?php if ($error_message): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <p class="success-message"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <form method="POST" action="update_profile.php" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Full Name:</label>
                <input type="text" id="name" name="name" value="<?php echo $_SESSION['counselor_name']; ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo $_SESSION['counselor_email']; ?>" required>
            </div>
            <div class="form-group">
                <label for="profile_picture">Profile Picture:</label>
                <div class="profile-picture-preview">
                    <img src="uploads/<?php echo $_SESSION['profile_picture'] ?: 'default.png'; ?>" alt="Profile Picture" id="profilePicturePreview">
                </div>
                <input type="file" id="profile_picture" name="profile_picture" accept="image/*" onchange="previewImage(event)">
            </div>
            <button type="submit">Save Changes</button>
        </form>
    </div>

    <script>
        // Preview uploaded image
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function () {
                const preview = document.getElementById("profilePicturePreview");
                preview.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>
</html>