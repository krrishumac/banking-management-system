<?php
require_once 'config.php';

if (is_user_logged_in()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = sanitize_input($_POST['full_name']);
    $email = sanitize_input($_POST['email']);
    $phone = sanitize_input($_POST['phone']);
    $address = sanitize_input($_POST['address']);
    $dob = sanitize_input($_POST['date_of_birth']);
    $id_number = sanitize_input($_POST['id_number']);
    
    if (empty($username) || empty($password) || empty($full_name) || empty($email)) {
        $error = 'Please fill in all required fields';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } else {
        $check_query = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error = 'Username or email already exists';
        } else {
            $insert_query = "INSERT INTO users (username, password, full_name, email, phone, address, date_of_birth, id_number) 
                            VALUES ('$username', '$password', '$full_name', '$email', '$phone', '$address', '$dob', '$id_number')";
            
            if (mysqli_query($conn, $insert_query)) {
                $user_id = mysqli_insert_id($conn);
                
                $account_number = generate_account_number();
                $account_query = "INSERT INTO accounts (user_id, account_number, account_type, balance) 
                                VALUES ($user_id, '$account_number', 'savings', 0.00)";
                mysqli_query($conn, $account_query);
                
                header('Location: login.php?registered=1');
                exit();
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}

$page_title = 'Register';
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
        <div class="auth-card" style="max-width: 700px;">
            <div class="auth-header">
                <h1>🏦 Create Your Account</h1>
                <p>Join KM BANK PVT LTD today</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                    <div class="form-group">
                        <label for="username">Username *</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="full_name">Full Name *</label>
                        <input type="text" id="full_name" name="full_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number *</label>
                        <input type="tel" id="phone" name="phone" placeholder="+91XXXXXXXXXX" pattern="\+91[0-9]{10}" title="Please enter Indian phone number in format: +91XXXXXXXXXX" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="date_of_birth">Date of Birth</label>
                        <input type="date" id="date_of_birth" name="date_of_birth">
                    </div>
                    
                    <div class="form-group">
                        <label for="id_number">ID Number</label>
                        <input type="text" id="id_number" name="id_number">
                    </div>
                    
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label for="address">Address</label>
                        <input type="text" id="address" name="address">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password *</label>
                        <input type="password" id="password" name="password" required minlength="6">
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password *</label>
                        <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-success" style="width: 100%;">Create Account</button>
                </div>
            </form>
            
            <p style="text-align: center; margin-top: 1.5rem;">
                Already have an account? 
                <a href="login.php" style="color: var(--primary-color); font-weight: 600;">Login Here</a>
            </p>
        </div>
    </div>
</body>
</html>
