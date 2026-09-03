<?php
// ==========================================
// View/login.php - Admin Login Page
// ==========================================

session_start();

// If already logged in via session, redirect directly to dashboard
if (isset($_SESSION['admin_user'])) {
    header("Location: dashboard.php");
    exit();
}

// Check if cookie exists to auto-fill username and check Remember Me
$saved_user = isset($_COOKIE['admin_user']) ? $_COOKIE['admin_user'] : '';
$is_remembered = !empty($saved_user);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Lifeline Blood Bank</title>
    <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>

    <div class="login-box">
        <h2>Admin Login</h2>
        <p class="subtitle">Lifeline Blood Bank Management System</p>

        <!-- Display Error or Success Messages -->
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_GET['msg']); ?>
            </div>
        <?php endif; ?>

        <!-- Login Form sending to loginController.php -->
        <form action="../Controller/loginController.php" method="POST">
            <div class="form-group">
                <label for="username">Admin Username</label>
                <input type="text" id="username" name="username" placeholder="Enter admin username" value="<?php echo htmlspecialchars($saved_user); ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>

            <div class="remember-me">
                <input type="checkbox" id="remember_me" name="remember_me" <?php echo $is_remembered ? 'checked' : ''; ?>>
                <label for="remember_me">Remember Me (Save in Cookies)</label>
            </div>

            <button type="submit" class="btn btn-primary btn-full">Login to Dashboard</button>
        </form>
    </div>

</body>
</html>
