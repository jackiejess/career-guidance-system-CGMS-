<?php
include '../db.php'; // Ensure you have a database connection file

// Redirect if not logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user details from the database
$student_id = $_SESSION['student_id'];
$query = "SELECT * FROM students WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Handle profile picture upload
    if (!empty($_FILES['profile_picture']['name'])) {
        $target_dir = "uploads/";
        $imageFileType = strtolower(pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION));
        $new_filename = "profile_" . $student_id . "." . $imageFileType;
        $target_file = $target_dir . $new_filename;

        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            $profile_picture = $new_filename;
            $update_query = "UPDATE students SET name = ?, email = ?, profile_picture = ? WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("sssi", $name, $email, $profile_picture, $student_id);
        }
    } else {
        $update_query = "UPDATE students SET name = ?, email = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ssi", $name, $email, $student_id);
    }

    if ($stmt->execute()) {
        $_SESSION['student_name'] = $name;
        $_SESSION['student_email'] = $email;
        if (!empty($profile_picture)) {
            $_SESSION['profile_picture'] = $profile_picture;
        }
        header("Location: dashboard.php");
        exit();
    } else {
        $error_message = "Error updating profile. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <style>
        .edit-profile-container {
            width: 50%;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .edit-profile-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .edit-profile-container input, .edit-profile-container button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
        }
        .edit-profile-container button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        .edit-profile-container button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="edit-profile-container">
    <h2>Edit Profile</h2>
    <?php if (isset($error_message)) echo "<p style='color: red;'>$error_message</p>"; ?>
    <form action="" method="POST" enctype="multipart/form-data">
        <label>Full Name</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

        <label>Profile Picture</label>
        <input type="file" name="profile_picture" accept="image/*">

        <button type="submit">Save Changes</button>
    </form>
</div>

</body>
</html>
