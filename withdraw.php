<?php
require_once 'config.php';
require_user_login();

$page_title = 'Withdraw Funds';
$error = '';
$success = '';

$account = get_user_account($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = floatval($_POST['amount']);
    $description = sanitize_input($_POST['description']);
    
    if ($amount <= 0) {
        $error = 'Please enter a valid amount';
    } elseif ($amount > $account['balance']) {
        $error = 'Insufficient balance for this withdrawal';
    } elseif ($amount < 100) {
        $error = 'Minimum withdrawal amount is ₹100';
    } else {
        $insert_query = "INSERT INTO withdrawal_requests (account_id, amount, description) 
                       VALUES (" . $account['account_id'] . ", $amount, '$description')";
        
        if (mysqli_query($conn, $insert_query)) {
            $success = 'Withdrawal request submitted successfully! Awaiting admin approval.';
            $_POST = array();
        } else {
            $error = 'Failed to submit withdrawal request. Please try again.';
        }
    }
}

$pending_withdrawals_query = "SELECT * FROM withdrawal_requests 
                             WHERE account_id = " . $account['account_id'] . " 
                             ORDER BY created_at DESC LIMIT 5";
$pending_withdrawals = mysqli_query($conn, $pending_withdrawals_query);

include 'includes/header.php';
?>

<h1 style="color: white; margin: 2rem 0; font-size: 2.5rem;">💰 Withdraw Funds</h1>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<div class="dashboard-grid" style="grid-template-columns: 1fr 1fr;">
    <div class="card">
        <h2 style="margin-bottom: 1.5rem; color: var(--primary-color);">💵 New Withdrawal Request</h2>
        
        <div style="background: linear-gradient(135deg, #f093fb, #f5576c); padding: 1.5rem; border-radius: 10px; margin-bottom: 1.5rem; color: white;">
            <div style="font-size: 0.9rem; opacity: 0.9;">Available Balance</div>
            <div style="font-size: 2rem; font-weight: bold; margin-top: 0.5rem;">
                <?php echo format_currency($account['balance']); ?>
            </div>
        </div>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="amount">Withdrawal Amount (₹) *</label>
                <input type="number" id="amount" name="amount" step="0.01" min="100" 
                       placeholder="Minimum ₹100" required
                       value="<?php echo isset($_POST['amount']) ? htmlspecialchars($_POST['amount']) : ''; ?>">
                <small style="color: #666; display: block; margin-top: 0.5rem;">
                    Minimum withdrawal: ₹100 | Maximum: <?php echo format_currency($account['balance']); ?>
                </small>
            </div>
            
            <div class="form-group">
                <label for="description">Purpose / Description</label>
                <textarea id="description" name="description" rows="4" 
                          placeholder="Describe the purpose of withdrawal (optional)"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem;">Quick Select:</label>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem;">
                    <button type="button" class="btn" onclick="document.getElementById('amount').value = 500" 
                            style="background: #e3f2fd; color: #1976d2; padding: 0.5rem;">₹500</button>
                    <button type="button" class="btn" onclick="document.getElementById('amount').value = 1000" 
                            style="background: #e3f2fd; color: #1976d2; padding: 0.5rem;">₹1,000</button>
                    <button type="button" class="btn" onclick="document.getElementById('amount').value = 5000" 
                            style="background: #e3f2fd; color: #1976d2; padding: 0.5rem;">₹5,000</button>
                    <button type="button" class="btn" onclick="document.getElementById('amount').value = 10000" 
                            style="background: #e3f2fd; color: #1976d2; padding: 0.5rem;">₹10,000</button>
                    <button type="button" class="btn" onclick="document.getElementById('amount').value = 25000" 
                            style="background: #e3f2fd; color: #1976d2; padding: 0.5rem;">₹25,000</button>
                    <button type="button" class="btn" onclick="document.getElementById('amount').value = 50000" 
                            style="background: #e3f2fd; color: #1976d2; padding: 0.5rem;">₹50,000</button>
                </div>
            </div>
            
            <button type="submit" class="btn btn-danger" style="width: 100%; margin-top: 1rem;">
                💸 Submit Withdrawal Request
            </button>
        </form>
    </div>
    
    <div class="card">
        <h2 style="margin-bottom: 1.5rem; color: var(--primary-color);">⏳ Recent Withdrawal Requests</h2>
        
        <?php if (mysqli_num_rows($pending_withdrawals) > 0): ?>
            <div style="max-height: 500px; overflow-y: auto;">
                <?php while ($withdrawal = mysqli_fetch_assoc($pending_withdrawals)): ?>
                    <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; border-left: 4px solid 
                        <?php 
                        echo $withdrawal['status'] == 'pending' ? '#ff9800' : 
                            ($withdrawal['status'] == 'approved' ? '#4caf50' : '#f44336'); 
                        ?>;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                            <div style="font-size: 1.3rem; font-weight: bold; color: #d32f2f;">
                                <?php echo format_currency($withdrawal['amount']); ?>
                            </div>
                            <span style="padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600;
                                background: <?php 
                                echo $withdrawal['status'] == 'pending' ? '#fff3e0' : 
                                    ($withdrawal['status'] == 'approved' ? '#e8f5e9' : '#ffebee'); 
                                ?>;
                                color: <?php 
                                echo $withdrawal['status'] == 'pending' ? '#e65100' : 
                                    ($withdrawal['status'] == 'approved' ? '#2e7d32' : '#c62828'); 
                                ?>;">
                                <?php echo ucfirst($withdrawal['status']); ?>
                            </span>
                        </div>
                        <?php if ($withdrawal['description']): ?>
                            <div style="font-size: 0.9rem; color: #666; margin-top: 0.5rem;">
                                📝 <?php echo htmlspecialchars($withdrawal['description']); ?>
                            </div>
                        <?php endif; ?>
                        <div style="font-size: 0.85rem; color: #999; margin-top: 0.5rem;">
                            🕐 Requested: <?php echo format_date($withdrawal['created_at']); ?>
                        </div>
                        <?php if ($withdrawal['processed_at']): ?>
                            <div style="font-size: 0.85rem; color: #666; margin-top: 0.25rem;">
                                ✓ Processed: <?php echo format_date($withdrawal['processed_at']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 3rem; color: #999;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">📭</div>
                <p>No withdrawal requests yet</p>
            </div>
        <?php endif; ?>
        
        <a href="transactions.php" class="btn btn-info" style="width: 100%; margin-top: 1rem;">
            📊 View All Transactions
        </a>
    </div>
</div>

<div class="card" style="margin-top: 2rem; background: linear-gradient(135deg, #fff3e0, #fff);">
    <h3 style="color: var(--warning-color); margin-bottom: 1rem;">ℹ️ Withdrawal Information</h3>
    <ul style="list-style: none; padding: 0;">
        <li style="padding: 0.5rem 0; border-bottom: 1px solid #e0e0e0;">
            ✓ Minimum withdrawal amount: ₹100
        </li>
        <li style="padding: 0.5rem 0; border-bottom: 1px solid #e0e0e0;">
            ✓ All withdrawals require admin approval for security
        </li>
        <li style="padding: 0.5rem 0; border-bottom: 1px solid #e0e0e0;">
            ✓ Processing time: 1-2 business hours during banking hours
        </li>
        <li style="padding: 0.5rem 0; border-bottom: 1px solid #e0e0e0;">
            ✓ Funds will be available in your registered account
        </li>
        <li style="padding: 0.5rem 0;">
            ✓ Contact support for urgent withdrawal requests
        </li>
    </ul>
</div>

<?php include 'includes/footer.php'; ?>
