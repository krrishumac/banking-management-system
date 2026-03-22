<?php
require_once 'config.php';
require_admin_login();

$page_title = 'All Transactions';

$transactions_query = "SELECT t.*, a.account_number, u.full_name 
                       FROM transactions t 
                       JOIN accounts a ON t.account_id = a.account_id 
                       JOIN users u ON a.user_id = u.user_id 
                       ORDER BY t.transaction_date DESC 
                       LIMIT 100";
$transactions = mysqli_query($conn, $transactions_query);

$total_deposits = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as total FROM transactions WHERE transaction_type = 'deposit'"))['total'] ?? 0;
$total_withdrawals = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as total FROM transactions WHERE transaction_type = 'withdrawal'"))['total'] ?? 0;
$total_transfers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as total FROM transactions WHERE transaction_type IN ('transfer_in', 'transfer_out')"))['total'] ?? 0;

include 'includes/admin_header.php';
?>

<h1 style="color: white; margin: 2rem 0; font-size: 2.5rem;">Transaction Management</h1>

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
        <div class="stat-icon">🔄</div>
        <div class="stat-value"><?php echo format_currency($total_transfers); ?></div>
        <div class="stat-label">Total Transfers</div>
    </div>
</div>

<div class="card">
    <div class="card-header">All Transactions (Last 100)</div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Reference</th>
                    <th>User</th>
                    <th>Account</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Balance Before</th>
                    <th>Balance After</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($transaction = mysqli_fetch_assoc($transactions)): ?>
                <tr>
                    <td>#<?php echo $transaction['transaction_id']; ?></td>
                    <td><?php echo htmlspecialchars($transaction['reference_number']); ?></td>
                    <td><?php echo htmlspecialchars($transaction['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($transaction['account_number']); ?></td>
                    <td>
                        <span class="transaction-<?php echo $transaction['transaction_type']; ?>">
                            <?php echo ucfirst(str_replace('_', ' ', $transaction['transaction_type'])); ?>
                        </span>
                    </td>
                    <td style="font-weight: 700;">
                        <?php echo format_currency($transaction['amount']); ?>
                    </td>
                    <td><?php echo format_currency($transaction['balance_before']); ?></td>
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
</div>

<?php include 'includes/footer.php'; ?>
