<?php
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $query = "SELECT * FROM admins WHERE username = '$username' AND status = 'active'";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) == 1) {
            $admin = mysqli_fetch_assoc($result);
            
            if ($password === $admin['password']) {
                $_SESSION['admin_id'] = $admin['admin_id'];
                $_SESSION['admin_name'] = $admin['full_name'];
                $_SESSION['admin_username'] = $admin['username'];
                
                mysqli_query($conn, "UPDATE admins SET last_login = NOW() WHERE admin_id = " . $admin['admin_id']);
                
                mysqli_query($conn, "INSERT INTO login_attempts (username, user_type, ip_address, success) 
                                   VALUES ('$username', 'admin', '{$_SERVER['REMOTE_ADDR']}', 1)");
                
                header('Location: admin_dashboard.php');
                exit();
            } else {
                $error = 'Invalid username or password';
            }
        } else {
            $error = 'Invalid username or password';
        }
        
        if ($error) {
            mysqli_query($conn, "INSERT INTO login_attempts (username, user_type, ip_address, success) 
                               VALUES ('$username', 'admin', '{$_SERVER['REMOTE_ADDR']}', 0)");
        }
    }
}

$page_title = 'Admin Login';
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
                <h1>🏦 Admin Login</h1>
                <p>KM BANK PVT LTD - Admin Panel</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
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
            
            <p style="text-align: center; margin-top: 1rem; color: var(--light-text);">
                <a href="index.php" style="color: var(--primary-color);">← Back to User Login</a>
            </p>
        </div>
    </div>
</body>
</html>
