<?php
require_once 'config.php';

if (is_user_logged_in()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$success = '';

if (isset($_GET['logout']) && $_GET['logout'] == 'success') {
    $success = 'You have been logged out successfully!';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $query = "SELECT * FROM users WHERE username = '$username' AND status = 'active'";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            
            if ($password === $user['password']) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['username'] = $user['username'];
                
                mysqli_query($conn, "UPDATE users SET last_login = NOW() WHERE user_id = " . $user['user_id']);
                
                mysqli_query($conn, "INSERT INTO login_attempts (username, user_type, ip_address, success) 
                                   VALUES ('$username', 'user', '{$_SERVER['REMOTE_ADDR']}', 1)");
                
                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Invalid username or password';
            }
        } else {
            $error = 'Invalid username or password';
        }
        
        if ($error) {
            mysqli_query($conn, "INSERT INTO login_attempts (username, user_type, ip_address, success) 
                               VALUES ('$username', 'user', '{$_SERVER['REMOTE_ADDR']}', 0)");
        }
    }
}

$page_title = 'Login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - KM BANK</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="animated-bg"></div>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>🏦 Welcome to KM BANK</h1>
                <p>Login to access your account</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (isset($_GET['registered'])): ?>
                <div class="alert alert-success">Registration successful! Please login.</div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
                </div>
            </form>
            
            <p style="text-align: center; margin-top: 1.5rem;">
                Don't have an account? 
                <a href="register.php" style="color: var(--primary-color); font-weight: 600;">Register Now</a>
            </p>
            
            <p style="text-align: center; margin-top: 0.5rem;">
                <a href="admin_login.php" style="color: var(--light-text); font-size: 0.875rem;">Admin Login →</a>
            </p>
        </div>
    </div>
</body>
</html>
