<?php
require_once 'config.php';
require_user_login();

$page_title = 'Dashboard';

$account = get_user_account($_SESSION['user_id']);

$recent_transactions = null;
$total_deposits = 0;
$total_withdrawals = 0;
$today_transactions = 0;

if ($account) {
    $account_id = intval($account['account_id']);

    $transactions_query = "SELECT * FROM transactions WHERE account_id = $account_id ORDER BY transaction_date DESC LIMIT 5";
    $recent_transactions = mysqli_query($conn, $transactions_query);

    $total_deposits_query = "SELECT SUM(amount) as total FROM transactions WHERE account_id = $account_id AND transaction_type IN ('deposit', 'transfer_in')";
    $total_deposits = mysqli_fetch_assoc(mysqli_query($conn, $total_deposits_query))['total'] ?? 0;

    $total_withdrawals_query = "SELECT SUM(amount) as total FROM transactions WHERE account_id = $account_id AND transaction_type IN ('withdrawal', 'transfer_out')";
    $total_withdrawals = mysqli_fetch_assoc(mysqli_query($conn, $total_withdrawals_query))['total'] ?? 0;

    $today_transactions_query = "SELECT COUNT(*) as count FROM transactions WHERE account_id = $account_id AND DATE(transaction_date) = CURDATE()";
    $today_transactions = mysqli_fetch_assoc(mysqli_query($conn, $today_transactions_query))['count'] ?? 0;
}

include 'includes/header.php';
?>

<h1 style="color: white; margin: 2rem 0; font-size: 2.5rem;">
    Welcome back, <?php echo $_SESSION['user_name']; ?>!
</h1>

<?php if ($account): ?>
<div class="account-card">
    <div class="account-type"><?php echo ucfirst($account['account_type']); ?> Account</div>
    <div class="account-number"><?php echo $account['account_number']; ?></div>
    <div class="account-balance"><?php echo format_currency($account['balance']); ?></div>
    <div style="margin-top: 1rem; opacity: 0.9;">Available Balance</div>
</div>
<?php else: ?>
<div class="alert alert-warning">
    No bank account is linked to your profile yet. Please contact admin.
</div>
<?php endif; ?>

<div class="quick-actions">
    <a href="transfer.php" class="quick-action-btn" style="background: linear-gradient(135deg, #d3fe75, #8a9d2c);">
        🔄 Transfer Money
    </a>
    <a href="withdraw.php" class="quick-action-btn" style="background: linear-gradient(135deg, #f093fb, #f5576c);">
        💸 Withdraw Funds
    </a>
    <a href="transactions.php" class="quick-action-btn" style="background: linear-gradient(135deg, #4facfe, #00f2fe);">
        📊 View Transactions
    </a>
    <a href="profile.php" class="quick-action-btn" style="background: linear-gradient(135deg, #43e97b, #38f9d7);">
        👤 My Profile
    </a>
</div>

<div class="dashboard-grid">
    <div class="stat-card">
        <div class="stat-icon">📈</div>
        <div class="stat-value"><?php echo format_currency($total_deposits); ?></div>
        <div class="stat-label">Total Deposits</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">📉</div>
        <div class="stat-value"><?php echo format_currency($total_withdrawals); ?></div>
        <div class="stat-label">Total Withdrawals</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">📊</div>
        <div class="stat-value"><?php echo $today_transactions; ?></div>
        <div class="stat-label">Today's Transactions</div>
    </div>
</div>

<div class="card">
    <div class="card-header">Recent Transactions</div>
    <?php if ($recent_transactions && mysqli_num_rows($recent_transactions) > 0): ?>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Balance After</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($transaction = mysqli_fetch_assoc($recent_transactions)): ?>
                <tr>
                    <td><?php echo $transaction['reference_number']; ?></td>
                    <td>
                        <span class="transaction-<?php echo $transaction['transaction_type']; ?>">
                            <?php echo ucfirst(str_replace('_', ' ', $transaction['transaction_type'])); ?>
                        </span>
                    </td>
                    <td style="font-weight: 700;">
                        <?php echo format_currency($transaction['amount']); ?>
                    </td>
                    <td><?php echo format_currency($transaction['balance_after']); ?></td>
                    <td><?php echo htmlspecialchars($transaction['description']); ?></td>
                    <td><?php echo format_date($transaction['transaction_date']); ?></td>
                    <td>
                        <span class="badge badge-<?php 
                            echo $transaction['status'] == 'completed' ? 'success' : 
                                ($transaction['status'] == 'pending' ? 'warning' : 'danger'); 
                        ?>">
                            <?php echo ucfirst($transaction['status']); ?>
                        </span>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <p style="padding: 2rem; text-align: center; color: var(--light-text);">
        <?php echo $account ? 'No transactions yet' : 'No account available'; ?>
    </p>
    <?php endif; ?>
    
    <div style="margin-top: 1rem; text-align: center;">
        <a href="transactions.php" class="btn btn-primary">View All Transactions</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
