<?php
require_once 'config.php';
require_user_login();

$page_title = 'My Profile';
$error = '';
$success = '';

$user_query = "SELECT * FROM users WHERE user_id = " . $_SESSION['user_id'];
$user = mysqli_fetch_assoc(mysqli_query($conn, $user_query));

$account = get_user_account($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $full_name = sanitize_input($_POST['full_name']);
    $email = sanitize_input($_POST['email']);
    $phone = sanitize_input($_POST['phone']);
    $address = sanitize_input($_POST['address']);
    
    if (empty($full_name) || empty($email) || empty($phone)) {
        $error = 'Please fill all required fields';
    } else {
        $email_check = mysqli_query($conn, "SELECT user_id FROM users WHERE email = '$email' AND user_id != " . $_SESSION['user_id']);
        if (mysqli_num_rows($email_check) > 0) {
            $error = 'Email address is already registered';
        } else {
            $update_query = "UPDATE users SET 
                           full_name = '$full_name',
                           email = '$email',
                           phone = '$phone',
                           address = '$address'
                           WHERE user_id = " . $_SESSION['user_id'];
            
            if (mysqli_query($conn, $update_query)) {
                $success = 'Profile updated successfully!';
                $_SESSION['user_name'] = $full_name;
                $user = mysqli_fetch_assoc(mysqli_query($conn, $user_query));
            } else {
                $error = 'Failed to update profile. Please try again.';
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = 'Please fill all password fields';
    } elseif (!password_verify($current_password, $user['password'])) {
        $error = 'Current password is incorrect';
    } elseif ($new_password != $confirm_password) {
        $error = 'New passwords do not match';
    } elseif (strlen($new_password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_query = "UPDATE users SET password = '$hashed_password' WHERE user_id = " . $_SESSION['user_id'];
        
        if (mysqli_query($conn, $update_query)) {
            $success = 'Password changed successfully!';
        } else {
            $error = 'Failed to change password. Please try again.';
        }
    }
}

include 'includes/header.php';
?>

<h1 style="color: white; margin: 2rem 0; font-size: 2.5rem;">👤 My Profile</h1>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<div class="dashboard-grid" style="grid-template-columns: 1fr 2fr;">
    <div class="card" style="text-align: center;">
        <div style="width: 120px; height: 120px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #667eea, #764ba2); 
                    border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 4rem; color: white;">
            <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
        </div>
        
        <h2 style="color: var(--primary-color); margin-bottom: 0.5rem;">
            <?php echo htmlspecialchars($user['full_name']); ?>
        </h2>
        <p style="color: #666; margin-bottom: 0.5rem;">@<?php echo htmlspecialchars($user['username']); ?></p>
        
        <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px; margin-top: 1.5rem;">
            <div style="font-size: 0.9rem; color: #666; margin-bottom: 0.5rem;">Account Status</div>
            <span style="padding: 0.25rem 1rem; border-radius: 20px; font-size: 0.9rem; font-weight: 600;
                background: <?php echo $user['status'] == 'active' ? '#e8f5e9' : '#ffebee'; ?>;
                color: <?php echo $user['status'] == 'active' ? '#2e7d32' : '#c62828'; ?>;">
                <?php echo ucfirst($user['status']); ?>
            </span>
        </div>
        
        <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px; margin-top: 1rem;">
            <div style="font-size: 0.9rem; color: #666; margin-bottom: 0.5rem;">Member Since</div>
            <strong><?php echo date('F Y', strtotime($user['created_at'])); ?></strong>
        </div>
        
        <?php if ($user['last_login']): ?>
            <div style="margin-top: 1rem; font-size: 0.85rem; color: #999;">
                Last Login:<br>
                <?php echo format_date($user['last_login']); ?>
            </div>
        <?php endif; ?>
    </div>
    
    <div>
        <div class="card" style="margin-bottom: 2rem;">
            <h3 style="color: var(--primary-color); margin-bottom: 1.5rem;">🏦 Account Information</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px;">
                    <div style="font-size: 0.85rem; color: #666; margin-bottom: 0.5rem;">Account Number</div>
                    <strong style="font-size: 1.1rem;"><?php echo htmlspecialchars($account['account_number']); ?></strong>
                </div>
                <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px;">
                    <div style="font-size: 0.85rem; color: #666; margin-bottom: 0.5rem;">Account Type</div>
                    <strong style="font-size: 1.1rem;"><?php echo ucfirst($account['account_type']); ?></strong>
                </div>
                <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px;">
                    <div style="font-size: 0.85rem; color: #666; margin-bottom: 0.5rem;">Current Balance</div>
                    <strong style="font-size: 1.1rem; color: #4caf50;"><?php echo format_currency($account['balance']); ?></strong>
                </div>
                <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px;">
                    <div style="font-size: 0.85rem; color: #666; margin-bottom: 0.5rem;">Currency</div>
                    <strong style="font-size: 1.1rem;"><?php echo htmlspecialchars($account['currency']); ?> (₹)</strong>
                </div>
            </div>
        </div>
        
        <div class="card" style="margin-bottom: 2rem;">
            <h3 style="color: var(--primary-color); margin-bottom: 1.5rem;">✏️ Update Profile Information</h3>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="full_name">Full Name *</label>
                    <input type="text" id="full_name" name="full_name" required
                           value="<?php echo htmlspecialchars($user['full_name']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" required
                           value="<?php echo htmlspecialchars($user['email']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number *</label>
                    <input type="tel" id="phone" name="phone" placeholder="+91XXXXXXXXXX" 
                           pattern="\+91[0-9]{10}" title="Please enter Indian phone number in format: +91XXXXXXXXXX" required
                           value="<?php echo htmlspecialchars($user['phone']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" rows="3"><?php echo htmlspecialchars($user['address']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="text" value="<?php echo $user['date_of_birth'] ? date('F d, Y', strtotime($user['date_of_birth'])) : 'Not set'; ?>" 
                           disabled style="background: #f8f9fa;">
                    <small style="color: #666; display: block; margin-top: 0.5rem;">Contact support to update date of birth</small>
                </div>
                
                <div class="form-group">
                    <label>ID Number</label>
                    <input type="text" value="<?php echo htmlspecialchars($user['id_number']); ?>" 
                           disabled style="background: #f8f9fa;">
                    <small style="color: #666; display: block; margin-top: 0.5rem;">Contact support to update ID number</small>
                </div>
                
                <button type="submit" name="update_profile" class="btn btn-primary" style="width: 100%;">
                    💾 Update Profile
                </button>
            </form>
        </div>
        
        <div class="card">
            <h3 style="color: var(--primary-color); margin-bottom: 1.5rem;">🔒 Change Password</h3>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="current_password">Current Password *</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>
                
                <div class="form-group">
                    <label for="new_password">New Password *</label>
                    <input type="password" id="new_password" name="new_password" required
                           minlength="6" placeholder="At least 6 characters">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password *</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <button type="submit" name="change_password" class="btn btn-warning" style="width: 100%;">
                    🔑 Change Password
                </button>
            </form>
        </div>
    </div>
</div>

<div class="card" style="margin-top: 2rem; background: linear-gradient(135deg, #e8f5e9, #fff);">
    <h3 style="color: var(--success-color); margin-bottom: 1rem;">🛡️ Security Tips</h3>
    <ul style="list-style: none; padding: 0;">
        <li style="padding: 0.5rem 0; border-bottom: 1px solid #e0e0e0;">
            ✓ Never share your password with anyone
        </li>
        <li style="padding: 0.5rem 0; border-bottom: 1px solid #e0e0e0;">
            ✓ Use a strong password with letters, numbers, and special characters
        </li>
        <li style="padding: 0.5rem 0; border-bottom: 1px solid #e0e0e0;">
            ✓ Change your password regularly (every 3-6 months)
        </li>
        <li style="padding: 0.5rem 0; border-bottom: 1px solid #e0e0e0;">
            ✓ Always logout after using the banking system
        </li>
        <li style="padding: 0.5rem 0;">
            ✓ Report any suspicious activity immediately to support
        </li>
    </ul>
</div>

<?php include 'includes/footer.php'; ?>
