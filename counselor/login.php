<?php
include '../db.php'; // Include your database connection file

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Fetch counselor data
    $result = $conn->query("SELECT * FROM counselors WHERE email='$email'");
    if ($result->num_rows > 0) {
        $counselor = $result->fetch_assoc();
        if (password_verify($password, $counselor["password"])) {
            // Login successful
            $_SESSION['counselor_id'] = $counselor["id"];
            $_SESSION['counselor_name'] = $counselor["name"];
            $_SESSION['profile_picture'] = $counselor["profile_picture"];
            header("Location: dashboard.php"); // Redirect to dashboard
            exit();
        } else {
            $error_message = "Incorrect password!";
        }
    } else {
        $error_message = "Email not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Counselor Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="css/auth.css">
</head>
<body>
    <div class="auth-container">
        <h2>Counselor Login</h2>
        <?php if ($error_message): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>