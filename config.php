<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'km_bank');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

define('SITE_NAME', 'KM BANK PVT LTD');
define('SITE_URL', 'http://localhost/KM%20BANK%20PTD%20LTD');
define('CURRENCY_SYMBOL', '₹');
define('CURRENCY_CODE', 'INR');

date_default_timezone_set('Asia/Kolkata');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function sanitize_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = mysqli_real_escape_string($conn, $data);
    return $data;
}

function generate_account_number() {
    return '10' . str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
}

function generate_reference_number() {
    return 'REF' . date('YmdHis') . rand(1000, 9999);
}

function format_currency($amount) {
    return CURRENCY_SYMBOL . number_format($amount, 2);
}

function format_date($date) {
    return date('M d, Y H:i:s', strtotime($date));
}

function is_user_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function is_admin_logged_in() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

function require_user_login() {
    if (!is_user_logged_in()) {
        header('Location: login.php');
        exit();
    }
}

function require_admin_login() {
    if (!is_admin_logged_in()) {
        header('Location: admin_login.php');
        exit();
    }
}

function get_user_account($user_id) {
    global $conn;
    $query = "SELECT a.*, u.full_name, u.email FROM accounts a 
              JOIN users u ON a.user_id = u.user_id 
              WHERE a.user_id = " . intval($user_id);
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}

function get_account_balance($account_id) {
    global $conn;
    $query = "SELECT balance FROM accounts WHERE account_id = " . intval($account_id);
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row ? $row['balance'] : 0;
}

function update_account_balance($account_id, $new_balance) {
    global $conn;
    $query = "UPDATE accounts SET balance = " . floatval($new_balance) . " 
              WHERE account_id = " . intval($account_id);
    return mysqli_query($conn, $query);
}

function record_transaction($account_id, $type, $amount, $balance_before, $balance_after, $description, $recipient_account = null) {
    global $conn;
    $reference = generate_reference_number();
    
    $query = "INSERT INTO transactions (account_id, transaction_type, amount, balance_before, balance_after, description, reference_number, recipient_account) 
              VALUES (" . intval($account_id) . ", '" . sanitize_input($type) . "', " . floatval($amount) . ", 
              " . floatval($balance_before) . ", " . floatval($balance_after) . ", 
              '" . sanitize_input($description) . "', '" . $reference . "', " . 
              ($recipient_account ? "'" . sanitize_input($recipient_account) . "'" : "NULL") . ")";
    
    return mysqli_query($conn, $query);
}

function get_dashboard_stats() {
    global $conn;
    
    $stats = [];
    
    $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE status = 'active'");
    $stats['total_users'] = mysqli_fetch_assoc($result)['count'];
    
    $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM accounts WHERE status = 'active'");
    $stats['total_accounts'] = mysqli_fetch_assoc($result)['count'];
    
    $result = mysqli_query($conn, "SELECT SUM(balance) as total FROM accounts WHERE status = 'active'");
    $stats['total_deposits'] = mysqli_fetch_assoc($result)['total'] ?? 0;
    
    $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM transactions WHERE DATE(transaction_date) = CURDATE()");
    $stats['today_transactions'] = mysqli_fetch_assoc($result)['count'];
    
    $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM withdrawal_requests WHERE status = 'pending'");
    $stats['pending_withdrawals'] = mysqli_fetch_assoc($result)['count'];
    
    $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM transfer_requests WHERE status = 'pending'");
    $stats['pending_transfers'] = mysqli_fetch_assoc($result)['count'];
    
    return $stats;
}
?>
