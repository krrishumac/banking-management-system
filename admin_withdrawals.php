<?php
require_once 'config.php';
require_admin_login();

$page_title = 'Withdrawal Requests';
$error = '';
$success = '';

if (isset($_GET['action']) && isset($_GET['id'])) {
    $withdrawal_id = intval($_GET['id']);
    $action = $_GET['action'];
    
    $withdrawal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM withdrawal_requests WHERE withdrawal_id = $withdrawal_id"));
    
    if ($withdrawal && $withdrawal['status'] == 'pending') {
        if ($action == 'approve') {
            $account = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM accounts WHERE account_id = " . $withdrawal['account_id']));
            
            if ($account['balance'] >= $withdrawal['amount']) {
                $old_balance = $account['balance'];
                $new_balance = $old_balance - $withdrawal['amount'];
                
                update_account_balance($withdrawal['account_id'], $new_balance);
                record_transaction($withdrawal['account_id'], 'withdrawal', $withdrawal['amount'], $old_balance, $new_balance, $withdrawal['description']);
                
                mysqli_query($conn, "UPDATE withdrawal_requests SET status = 'approved', processed_at = NOW(), processed_by = {$_SESSION['admin_id']} WHERE withdrawal_id = $withdrawal_id");
                
                $success = 'Withdrawal approved successfully';
            } else {
                $error = 'Insufficient balance in account';
            }
        } elseif ($action == 'reject') {
            mysqli_query($conn, "UPDATE withdrawal_requests SET status = 'rejected', processed_at = NOW(), processed_by = {$_SESSION['admin_id']} WHERE withdrawal_id = $withdrawal_id");
            $success = 'Withdrawal rejected';
        }
    }
}

$withdrawals_query = "SELECT w.*, a.account_number, u.full_name, u.email, a.balance 
                      FROM withdrawal_requests w 
                      JOIN accounts a ON w.account_id = a.account_id 
                      JOIN users u ON a.user_id = u.user_id 
                      ORDER BY w.created_at DESC";
$withdrawals = mysqli_query($conn, $withdrawals_query);

include 'includes/admin_header.php';
?>

<h1 style="color: white; margin: 2rem 0; font-size: 2.5rem;">Withdrawal Requests</h1>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo $error; ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">All Withdrawal Requests</div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Account Number</th>
                    <th>Current Balance</th>
                    <th>Amount</th>
                    <th>Description</th>
                    <th>Requested</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($withdrawal = mysqli_fetch_assoc($withdrawals)): ?>
                <tr>
                    <td>#<?php echo $withdrawal['withdrawal_id']; ?></td>
                    <td><?php echo htmlspecialchars($withdrawal['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($withdrawal['account_number']); ?></td>
                    <td><?php echo format_currency($withdrawal['balance']); ?></td>
                    <td style="font-weight: 700; color: var(--danger-color);">
                        <?php echo format_currency($withdrawal['amount']); ?>
                    </td>
                    <td><?php echo htmlspecialchars($withdrawal['description']); ?></td>
                    <td><?php echo format_date($withdrawal['created_at']); ?></td>
                    <td>
                        <span class="badge badge-<?php 
                            echo $withdrawal['status'] == 'approved' ? 'success' : 
                                ($withdrawal['status'] == 'pending' ? 'warning' : 'danger'); 
                        ?>">
                            <?php echo ucfirst($withdrawal['status']); ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($withdrawal['status'] == 'pending'): ?>
                        <a href="?action=approve&id=<?php echo $withdrawal['withdrawal_id']; ?>" class="btn btn-success" style="padding: 0.35rem 0.75rem; font-size: 0.875rem; margin: 2px;" onclick="return confirm('Approve this withdrawal?')">
                            Approve
                        </a>
                        <a href="?action=reject&id=<?php echo $withdrawal['withdrawal_id']; ?>" class="btn btn-danger" style="padding: 0.35rem 0.75rem; font-size: 0.875rem; margin: 2px;" onclick="return confirm('Reject this withdrawal?')">
                            Reject
                        </a>
                        <?php else: ?>
                        <span style="color: var(--light-text);">Processed</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
