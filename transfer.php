<?php
require_once 'config.php';
require_user_login();

$page_title = 'Transfer Money';
$error = '';
$success = '';

$account = get_user_account($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $recipient_account = sanitize_input($_POST['recipient_account']);
    $amount = floatval($_POST['amount']);
    $description = sanitize_input($_POST['description']);
    
    if (empty($recipient_account) || $amount <= 0) {
        $error = 'Please enter valid recipient account and amount';
    } elseif ($recipient_account == $account['account_number']) {
        $error = 'Cannot transfer to your own account';
    } elseif ($amount > $account['balance']) {
        $error = 'Insufficient balance for this transfer';
    } else {
        $recipient_query = "SELECT * FROM accounts WHERE account_number = '$recipient_account' AND status = 'active'";
        $recipient_result = mysqli_query($conn, $recipient_query);
        
        if (mysqli_num_rows($recipient_result) == 0) {
            $error = 'Recipient account not found or inactive';
        } else {
            $insert_query = "INSERT INTO transfer_requests (from_account_id, to_account_number, amount, description) 
                           VALUES (" . $account['account_id'] . ", '$recipient_account', $amount, '$description')";
            
            if (mysqli_query($conn, $insert_query)) {
                $success = 'Transfer request submitted successfully! Awaiting admin approval.';
                $_POST = array();
            } else {
                $error = 'Failed to submit transfer request. Please try again.';
            }
        }
    }
}

$pending_transfers_query = "SELECT tr.*, a.account_number as to_account 
                           FROM transfer_requests tr
                           LEFT JOIN accounts a ON tr.to_account_number = a.account_number
                           WHERE tr.from_account_id = " . $account['account_id'] . " 
                           ORDER BY tr.created_at DESC LIMIT 5";
$pending_transfers = mysqli_query($conn, $pending_transfers_query);

include 'includes/header.php';
?>

<h1 style="color: white; margin: 2rem 0; font-size: 2.5rem;">💸 Transfer Money</h1>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<div class="dashboard-grid" style="grid-template-columns: 1fr 1fr;">
    <div class="card">
        <h2 style="margin-bottom: 1.5rem; color: var(--primary-color);">💳 New Transfer</h2>
        
        <div style="background: linear-gradient(135deg, #667eea, #764ba2); padding: 1.5rem; border-radius: 10px; margin-bottom: 1.5rem; color: white;">
            <div style="font-size: 0.9rem; opacity: 0.9;">Available Balance</div>
            <div style="font-size: 2rem; font-weight: bold; margin-top: 0.5rem;">
                <?php echo format_currency($account['balance']); ?>
            </div>
        </div>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="recipient_account">Recipient Account Number *</label>
                <input type="text" id="recipient_account" name="recipient_account" 
                       placeholder="Enter account number" required 
                       value="<?php echo isset($_POST['recipient_account']) ? htmlspecialchars($_POST['recipient_account']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="amount">Amount (₹) *</label>
                <input type="number" id="amount" name="amount" step="0.01" min="1" 
                       placeholder="0.00" required
                       value="<?php echo isset($_POST['amount']) ? htmlspecialchars($_POST['amount']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3" 
                          placeholder="Optional message or note"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                🚀 Submit Transfer Request
            </button>
        </form>
    </div>
    
    <div class="card">
        <h2 style="margin-bottom: 1.5rem; color: var(--primary-color);">⏳ Recent Transfer Requests</h2>
        
        <?php if (mysqli_num_rows($pending_transfers) > 0): ?>
            <div style="max-height: 500px; overflow-y: auto;">
                <?php while ($transfer = mysqli_fetch_assoc($pending_transfers)): ?>
                    <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; border-left: 4px solid 
                        <?php 
                        echo $transfer['status'] == 'pending' ? '#ff9800' : 
                            ($transfer['status'] == 'approved' ? '#4caf50' : '#f44336'); 
                        ?>;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                            <div>
                                <strong>To: <?php echo htmlspecialchars($transfer['to_account_number']); ?></strong>
                                <div style="font-size: 1.2rem; font-weight: bold; color: #d32f2f; margin-top: 0.25rem;">
                                    <?php echo format_currency($transfer['amount']); ?>
                                </div>
                            </div>
                            <span style="padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600;
                                background: <?php 
                                echo $transfer['status'] == 'pending' ? '#fff3e0' : 
                                    ($transfer['status'] == 'approved' ? '#e8f5e9' : '#ffebee'); 
                                ?>;
                                color: <?php 
                                echo $transfer['status'] == 'pending' ? '#e65100' : 
                                    ($transfer['status'] == 'approved' ? '#2e7d32' : '#c62828'); 
                                ?>;">
                                <?php echo ucfirst($transfer['status']); ?>
                            </span>
                        </div>
                        <?php if ($transfer['description']): ?>
                            <div style="font-size: 0.9rem; color: #666; margin-top: 0.5rem;">
                                📝 <?php echo htmlspecialchars($transfer['description']); ?>
                            </div>
                        <?php endif; ?>
                        <div style="font-size: 0.85rem; color: #999; margin-top: 0.5rem;">
                            🕐 <?php echo format_date($transfer['created_at']); ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 3rem; color: #999;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">📭</div>
                <p>No transfer requests yet</p>
            </div>
        <?php endif; ?>
        
        <a href="transactions.php" class="btn btn-info" style="width: 100%; margin-top: 1rem;">
            📊 View All Transactions
        </a>
    </div>
</div>

<div class="card" style="margin-top: 2rem; background: linear-gradient(135deg, #e3f2fd, #fff);">
    <h3 style="color: var(--info-color); margin-bottom: 1rem;">ℹ️ Transfer Guidelines</h3>
    <ul style="list-style: none; padding: 0;">
        <li style="padding: 0.5rem 0; border-bottom: 1px solid #e0e0e0;">
            ✓ All transfers require admin approval for security
        </li>
        <li style="padding: 0.5rem 0; border-bottom: 1px solid #e0e0e0;">
            ✓ Ensure recipient account number is correct before submitting
        </li>
        <li style="padding: 0.5rem 0; border-bottom: 1px solid #e0e0e0;">
            ✓ Transfer processing typically takes 1-2 business hours
        </li>
        <li style="padding: 0.5rem 0; border-bottom: 1px solid #e0e0e0;">
            ✓ You will be notified once your transfer is approved
        </li>
        <li style="padding: 0.5rem 0;">
            ✓ Contact support if you have any questions
        </li>
    </ul>
</div>

<?php include 'includes/footer.php'; ?>
