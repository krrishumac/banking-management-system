<?php
require_once 'config.php';

if (is_user_logged_in()) {
    header('Location: dashboard.php');
    exit();
} elseif (is_admin_logged_in()) {
    header('Location: admin_dashboard.php');
    exit();
}

$page_title = 'Welcome to KM Bank';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="animated-bg"></div>
    
    <div class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">🏦 KM BANK PVT LTD</h1>
            <p class="hero-subtitle">Professional Banking System - Secure, Fast & Reliable</p>
            
            <div class="hero-features">
                <div class="feature-item">
                    <div class="feature-icon">💰</div>
                    <h3>Secure Transactions</h3>
                    <p>Bank-grade security for all your financial operations</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">⚡</div>
                    <h3>Instant Transfers</h3>
                    <p>Transfer money quickly to any account</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">📊</div>
                    <h3>Real-time Reports</h3>
                    <p>Track all your transactions in real-time</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">🔒</div>
                    <h3>Privacy Protected</h3>
                    <p>Your data is encrypted and secure</p>
                </div>
            </div>
            
            <div class="hero-actions">
                <a href="login.php" class="btn btn-primary btn-large">
                    🔑 Customer Login
                </a>
                <a href="register.php" class="btn btn-success btn-large">
                    📝 Create Account
                </a>
                <a href="admin_login.php" class="btn btn-info btn-large">
                    👨‍💼 Admin Login
                </a>
            </div>
            
            <div style="margin-top: 3rem; padding: 1.5rem; background: rgba(255,255,255,0.1); border-radius: 15px;" >
                <p style="color: white; font-size: 1.1rem; margin: 0;">
                    💱 All transactions in Indian Rupees (₹) | 🕐 IST Timezone
                </p>
            </div>
        </div>
    </div>
    
    <footer>
        <div class="footer-content">
            <p>&copy; <?php echo date('Y'); ?> KM BANK PVT LTD. All rights reserved.</p>
            <div class="footer-links">
                <a href="privacy_policy.php">Privacy Policy</a>
                <a href="terms_of_service.php">Terms of Service</a>
                <a href="contact_us.php">Contact Us</a>
                <a href="help_center.php">Help Center</a>
            </div>
        </div>
    </footer>
</body>
</html>
