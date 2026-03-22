<?php
require_once 'config.php';
require_admin_login();

$page_title = 'Account Management';
$error = '';
$success = '';

if (isset($_GET['action']) && isset($_GET['id'])) {
    $account_id = intval($_GET['id']);
    $action = $_GET['action'];
    
    if ($action == 'freeze') {
        mysqli_query($conn, "UPDATE accounts SET status = 'frozen' WHERE account_id = $account_id");
        $success = 'Account frozen successfully';
    } elseif ($action == 'activate') {
        mysqli_query($conn, "UPDATE accounts SET status = 'active' WHERE account_id = $account_id");
        $success = 'Account activated successfully';
    } elseif ($action == 'close') {
        mysqli_query($conn, "UPDATE accounts SET status = 'closed' WHERE account_id = $account_id");
        $success = 'Account closed successfully';
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['adjust_balance'])) {
    $account_id = intval($_POST['account_id']);
    $adjustment_type = $_POST['adjustment_type'];
    $amount = floatval($_POST['amount']);
    $description = sanitize_input($_POST['description']);
    
    $account = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM accounts WHERE account_id = $account_id"));
    $old_balance = $account['balance'];
    
    if ($adjustment_type == 'add') {
        $new_balance = $old_balance + $amount;
        $transaction_type = 'deposit';
    } else {
        $new_balance = $old_balance - $amount;
        $transaction_type = 'withdrawal';
    }
    
    update_account_balance($account_id, $new_balance);
    record_transaction($account_id, $transaction_type, $amount, $old_balance, $new_balance, 'Admin adjustment: ' . $description);
    
    $success = 'Balance adjusted successfully';
}

$accounts_query = "SELECT a.*, u.full_name, u.email, u.phone 
                   FROM accounts a 
                   JOIN users u ON a.user_id = u.user_id 
                   ORDER BY a.account_id DESC";
$accounts = mysqli_query($conn, $accounts_query);

include 'includes/admin_header.php';
?>

<h1 style="color: white; margin: 2rem 0; font-size: 2.5rem;">Account Management</h1>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo $error; ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">Adjust Account Balance</div>
    <form method="POST" action="">
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
            <div class="form-group">
                <label for="account_id">Select Account</label>
                <select id="account_id" name="account_id" required>
                    <option value="">-- Select Account --</option>
                    <?php 
                    mysqli_data_seek($accounts, 0);
                    while ($acc = mysqli_fetch_assoc($accounts)): 
                    ?>
                        <option value="<?php echo $acc['account_id']; ?>">
                            <?php echo $acc['account_number']; ?> - <?php echo $acc['full_name']; ?> (<?php echo format_currency($acc['balance']); ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="adjustment_type">Adjustment Type</label>
                <select id="adjustment_type" name="adjustment_type" required>
                    <option value="add">Add Funds</option>
                    <option value="subtract">Subtract Funds</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="amount">Amount</label>
                <input type="number" id="amount" name="amount" step="0.01" min="0.01" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <input type="text" id="description" name="description" required>
            </div>
        </div>
        
        <div class="form-group">
            <button type="submit" name="adjust_balance" class="btn btn-warning">Adjust Balance</button>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header">All Accounts</div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Account Number</th>
                    <th>Account Holder</th>
                    <th>Email</th>
                    <th>Type</th>
                    <th>Balance</th>
                    <th>Status</th>
                    <th>Opened</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php mysqli_data_seek($accounts, 0); ?>
                <?php while ($account = mysqli_fetch_assoc($accounts)): ?>
                <tr>
                    <td>#<?php echo $account['account_id']; ?></td>
                    <td><?php echo htmlspecialchars($account['account_number']); ?></td>
                    <td><?php echo htmlspecialchars($account['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($account['email']); ?></td>
                    <td><?php echo ucfirst($account['account_type']); ?></td>
                    <td style="font-weight: 700; color: var(--primary-color);">
                        <?php echo format_currency($account['balance']); ?>
                    </td>
                    <td>
                        <span class="badge badge-<?php 
                            echo $account['status'] == 'active' ? 'success' : 
                                ($account['status'] == 'frozen' ? 'warning' : 'danger'); 
                        ?>">
                            <?php echo ucfirst($account['status']); ?>
                        </span>
                    </td>
                    <td><?php echo format_date($account['opening_date']); ?></td>
                    <td>
                        <?php if ($account['status'] == 'active'): ?>
                        <a href="?action=freeze&id=<?php echo $account['account_id']; ?>" class="btn btn-warning" style="padding: 0.35rem 0.75rem; font-size: 0.875rem; margin: 2px;" onclick="return confirm('Freeze this account?')">
                            Freeze
                        </a>
                        <?php elseif ($account['status'] == 'frozen'): ?>
                        <a href="?action=activate&id=<?php echo $account['account_id']; ?>" class="btn btn-success" style="padding: 0.35rem 0.75rem; font-size: 0.875rem; margin: 2px;">
                            Activate
                        </a>
                        <?php endif; ?>
                        
                        <?php if ($account['status'] != 'closed'): ?>
                        <a href="?action=close&id=<?php echo $account['account_id']; ?>" class="btn btn-danger" style="padding: 0.35rem 0.75rem; font-size: 0.875rem; margin: 2px;" onclick="return confirm('Close this account?')">
                            Close
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
