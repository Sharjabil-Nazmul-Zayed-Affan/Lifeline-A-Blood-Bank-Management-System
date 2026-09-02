<?php
session_start();

if (isset($_SESSION['admin_user'])) {
    header("Location: adminDashboard.php");
    exit();
}

$saved_user = isset($_COOKIE['admin_user']) ? $_COOKIE['admin_user'] : '';
$is_remembered = !empty($saved_user);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Lifeline Blood Bank</title>
    <link rel="stylesheet" href="css/adminStyle.css">
</head>
<body>

    <div class="login-box">
        <h2>Admin Login</h2>
        <p class="subtitle">Lifeline Blood Bank Management System</p>

        <div id="ajaxAlert" style="display: none;"></div>

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

        <form action="../Controller/adminLoginController.php" method="POST" onsubmit="return handleAdminLogin(event);">
            <div class="form-group">
                <label for="username">Admin Username</label>
                <input type="text" id="username" name="username" placeholder="Enter admin username" value="<?php echo htmlspecialchars($saved_user); ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>

            <div class="remember-me">
                <input type="checkbox" id="remember_me" name="remember_me" value="1" <?php echo $is_remembered ? 'checked' : ''; ?>>
                <label for="remember_me">Remember Me (Save in Cookies)</label>
            </div>

            <button type="submit" class="btn btn-primary btn-full">Login to Dashboard</button>
        </form>
    </div>

    <script src="js/adminAjax.js"></script>

</body>
</html>
