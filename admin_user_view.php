<?php
require_once 'config.php';
require_admin_login();

$page_title = 'User Details';
$error = '';

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($user_id <= 0) {
    $error = 'Invalid user ID';
} else {
    $user_query = "SELECT * FROM users WHERE user_id = $user_id LIMIT 1";
    $user_result = mysqli_query($conn, $user_query);
    $user = mysqli_fetch_assoc($user_result);

    if (!$user) {
        $error = 'User not found';
    }
}

$accounts = null;
$transactions = null;

if (!$error) {
    $accounts_query = "SELECT * FROM accounts WHERE user_id = $user_id ORDER BY opening_date DESC";
    $accounts = mysqli_query($conn, $accounts_query);

    $transactions_query = "SELECT t.*, a.account_number 
                           FROM transactions t
                           JOIN accounts a ON t.account_id = a.account_id
                           WHERE a.user_id = $user_id
                           ORDER BY t.transaction_date DESC
                           LIMIT 20";
    $transactions = mysqli_query($conn, $transactions_query);
}

include 'includes/admin_header.php';
?>

<h1 style="color: white; margin: 2rem 0; font-size: 2.5rem;">User Account Details</h1>

<div style="margin-bottom: 1rem;">
    <a href="admin_users.php" class="btn btn-info" style="padding: 0.5rem 1rem;">← Back to Users</a>
</div>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo $error; ?></div>
<?php else: ?>

<div class="card">
    <div class="card-header">User Profile</div>
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
        <p><strong>User ID:</strong> #<?php echo $user['user_id']; ?></p>
        <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
        <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user['full_name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
        <p><strong>ID Number:</strong> <?php echo htmlspecialchars($user['id_number']); ?></p>
        <p style="grid-column: span 2;"><strong>Address:</strong> <?php echo htmlspecialchars($user['address']); ?></p>
        <p><strong>Date of Birth:</strong> <?php echo date('M d, Y', strtotime($user['date_of_birth'])); ?></p>
        <p>
            <strong>Status:</strong>
            <span class="badge badge-<?php echo $user['status'] == 'active' ? 'success' : ($user['status'] == 'suspended' ? 'warning' : 'danger'); ?>">
                <?php echo ucfirst($user['status']); ?>
            </span>
        </p>
        <p><strong>Registered:</strong> <?php echo format_date($user['created_at']); ?></p>
        <p><strong>Last Login:</strong> <?php echo $user['last_login'] ? format_date($user['last_login']) : 'Never'; ?></p>
    </div>
</div>

<div class="card">
    <div class="card-header">User Accounts</div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Account Number</th>
                    <th>Type</th>
                    <th>Balance</th>
                    <th>Currency</th>
                    <th>Status</th>
                    <th>Opened</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($accounts) > 0): ?>
                    <?php while ($account = mysqli_fetch_assoc($accounts)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($account['account_number']); ?></td>
                        <td><?php echo ucfirst(str_replace('_', ' ', $account['account_type'])); ?></td>
                        <td style="font-weight: 700; color: var(--primary-color);"><?php echo format_currency($account['balance']); ?></td>
                        <td><?php echo htmlspecialchars($account['currency']); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $account['status'] == 'active' ? 'success' : ($account['status'] == 'frozen' ? 'warning' : 'danger'); ?>">
                                <?php echo ucfirst($account['status']); ?>
                            </span>
                        </td>
                        <td><?php echo format_date($account['opening_date']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--light-text);">No accounts found for this user.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header">Recent Transactions (Last 20)</div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Account</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Balance Before</th>
                    <th>Balance After</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($transactions) > 0): ?>
                    <?php while ($transaction = mysqli_fetch_assoc($transactions)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($transaction['reference_number']); ?></td>
                        <td><?php echo htmlspecialchars($transaction['account_number']); ?></td>
                        <td><?php echo ucfirst(str_replace('_', ' ', $transaction['transaction_type'])); ?></td>
                        <td><?php echo format_currency($transaction['amount']); ?></td>
                        <td><?php echo format_currency($transaction['balance_before']); ?></td>
                        <td><?php echo format_currency($transaction['balance_after']); ?></td>
                        <td><?php echo format_date($transaction['transaction_date']); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $transaction['status'] == 'completed' ? 'success' : ($transaction['status'] == 'pending' ? 'warning' : 'danger'); ?>">
                                <?php echo ucfirst($transaction['status']); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center; color: var(--light-text);">No transactions found for this user.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php endif; ?>

<?php include 'includes/footer.php'; ?>
