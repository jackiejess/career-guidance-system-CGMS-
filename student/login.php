<?php
include '../db.php';

$error = ""; // To store error messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, name, password, profile_picture FROM students WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user["password"])) {
            $_SESSION["student_id"] = $user["id"];
            $_SESSION["student_name"] = $user["name"];
            $_SESSION["profile_picture"] = $user["profile_picture"];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Incorrect password!";
        }
    } else {
        $error = "User not found!";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student Login</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if ($error) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <div class="input-group">
                <label>Email:</label>
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="input-group">
                <label>Password:</label>
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="login-btn">Login</button>
        </form>
        <p class="register-link">Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>
