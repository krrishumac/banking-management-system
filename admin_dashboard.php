<?php
require_once 'config.php';
require_admin_login();

$page_title = 'Admin Dashboard';
$stats = get_dashboard_stats();

$recent_transactions_query = "SELECT t.*, a.account_number, u.full_name 
                               FROM transactions t 
                               JOIN accounts a ON t.account_id = a.account_id 
                               JOIN users u ON a.user_id = u.user_id 
                               ORDER BY t.transaction_date DESC LIMIT 10";
$recent_transactions = mysqli_query($conn, $recent_transactions_query);

$recent_users_query = "SELECT * FROM users ORDER BY created_at DESC LIMIT 5";
$recent_users = mysqli_query($conn, $recent_users_query);

include 'includes/admin_header.php';
?>

<h1 style="color: white; margin: 2rem 0; font-size: 2.5rem;">
    Welcome, <?php echo $_SESSION['admin_name']; ?>!
</h1>

<div class="dashboard-grid">
    <div class="stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-value"><?php echo $stats['total_users']; ?></div>
        <div class="stat-label">Total Users</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">🏦</div>
        <div class="stat-value"><?php echo $stats['total_accounts']; ?></div>
        <div class="stat-label">Active Accounts</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">💰</div>
        <div class="stat-value"><?php echo format_currency($stats['total_deposits']); ?></div>
        <div class="stat-label">Total Deposits</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">📊</div>
        <div class="stat-value"><?php echo $stats['today_transactions']; ?></div>
        <div class="stat-label">Today's Transactions</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">⏳</div>
        <div class="stat-value"><?php echo $stats['pending_withdrawals']; ?></div>
        <div class="stat-label">Pending Withdrawals</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">🔄</div>
        <div class="stat-value"><?php echo $stats['pending_transfers']; ?></div>
        <div class="stat-label">Pending Transfers</div>
    </div>
</div>

<div class="quick-actions">
    <a href="admin_users.php" class="quick-action-btn" style="background: linear-gradient(135deg, #d3fe75, #8a9d2c);">
        👥 Manage Users
    </a>
    <a href="admin_accounts.php" class="quick-action-btn" style="background: linear-gradient(135deg, #f093fb, #f5576c);">
        🏦 Manage Accounts
    </a>
    <a href="admin_withdrawals.php" class="quick-action-btn" style="background: linear-gradient(135deg, #4facfe, #00f2fe);">
        💸 Process Withdrawals
    </a>
    <a href="admin_transfers.php" class="quick-action-btn" style="background: linear-gradient(135deg, #43e97b, #38f9d7);">
        🔄 Process Transfers
    </a>
</div>

<div class="card">
    <div class="card-header">Recent Transactions</div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Account</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($transaction = mysqli_fetch_assoc($recent_transactions)): ?>
                <tr>
                    <td>#<?php echo $transaction['transaction_id']; ?></td>
                    <td><?php echo htmlspecialchars($transaction['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($transaction['account_number']); ?></td>
                    <td>
                        <span class="transaction-<?php echo $transaction['transaction_type']; ?>">
                            <?php echo ucfirst(str_replace('_', ' ', $transaction['transaction_type'])); ?>
                        </span>
                    </td>
                    <td><?php echo format_currency($transaction['amount']); ?></td>
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

<div class="card">
    <div class="card-header">Recently Registered Users</div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Registered</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = mysqli_fetch_assoc($recent_users)): ?>
                <tr>
                    <td>#<?php echo $user['user_id']; ?></td>
                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['phone']); ?></td>
                    <td><?php echo format_date($user['created_at']); ?></td>
                    <td>
                        <span class="badge badge-<?php 
                            echo $user['status'] == 'active' ? 'success' : 'danger'; 
                        ?>">
                            <?php echo ucfirst($user['status']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="admin_user_view.php?id=<?php echo $user['user_id']; ?>" class="btn btn-info" style="padding: 0.35rem 0.75rem; font-size: 0.875rem;">
                            View
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
