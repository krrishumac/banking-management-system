<?php
require_once 'config.php';
require_admin_login();

$page_title = 'Transfer Requests';
$error = '';
$success = '';

if (isset($_GET['action']) && isset($_GET['id'])) {
    $request_id = intval($_GET['id']);
    $action = $_GET['action'];
    
    $transfer = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM transfer_requests WHERE request_id = $request_id"));
    
    if ($transfer && $transfer['status'] == 'pending') {
        if ($action == 'approve') {
            $from_account = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM accounts WHERE account_id = " . $transfer['from_account_id']));
            $to_account = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM accounts WHERE account_number = '" . $transfer['to_account_number'] . "'"));
            
            if (!$to_account) {
                $error = 'Recipient account not found';
            } elseif ($from_account['balance'] < $transfer['amount']) {
                $error = 'Insufficient balance in sender account';
            } else {
                $from_old_balance = $from_account['balance'];
                $from_new_balance = $from_old_balance - $transfer['amount'];
                update_account_balance($transfer['from_account_id'], $from_new_balance);
                record_transaction($transfer['from_account_id'], 'transfer_out', $transfer['amount'], $from_old_balance, $from_new_balance, 'Transfer to ' . $transfer['to_account_number'] . ': ' . $transfer['description'], $transfer['to_account_number']);
                
                $to_old_balance = $to_account['balance'];
                $to_new_balance = $to_old_balance + $transfer['amount'];
                update_account_balance($to_account['account_id'], $to_new_balance);
                record_transaction($to_account['account_id'], 'transfer_in', $transfer['amount'], $to_old_balance, $to_new_balance, 'Transfer from ' . $from_account['account_number'] . ': ' . $transfer['description'], $from_account['account_number']);
                
                mysqli_query($conn, "UPDATE transfer_requests SET status = 'approved', processed_at = NOW(), processed_by = {$_SESSION['admin_id']} WHERE request_id = $request_id");
                
                $success = 'Transfer approved successfully';
            }
        } elseif ($action == 'reject') {
            mysqli_query($conn, "UPDATE transfer_requests SET status = 'rejected', processed_at = NOW(), processed_by = {$_SESSION['admin_id']} WHERE request_id = $request_id");
            $success = 'Transfer rejected';
        }
    }
}

$transfers_query = "SELECT tr.*, a.account_number as from_account_number, u.full_name, a.balance 
                    FROM transfer_requests tr 
                    JOIN accounts a ON tr.from_account_id = a.account_id 
                    JOIN users u ON a.user_id = u.user_id 
                    ORDER BY tr.created_at DESC";
$transfers = mysqli_query($conn, $transfers_query);

include 'includes/admin_header.php';
?>

<h1 style="color: white; margin: 2rem 0; font-size: 2.5rem;">Transfer Requests</h1>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo $error; ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">All Transfer Requests</div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Sender</th>
                    <th>From Account</th>
                    <th>To Account</th>
                    <th>Amount</th>
                    <th>Current Balance</th>
                    <th>Description</th>
                    <th>Requested</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($transfer = mysqli_fetch_assoc($transfers)): ?>
                <tr>
                    <td>#<?php echo $transfer['request_id']; ?></td>
                    <td><?php echo htmlspecialchars($transfer['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($transfer['from_account_number']); ?></td>
                    <td><?php echo htmlspecialchars($transfer['to_account_number']); ?></td>
                    <td style="font-weight: 700; color: var(--info-color);">
                        <?php echo format_currency($transfer['amount']); ?>
                    </td>
                    <td><?php echo format_currency($transfer['balance']); ?></td>
                    <td><?php echo htmlspecialchars($transfer['description']); ?></td>
                    <td><?php echo format_date($transfer['created_at']); ?></td>
                    <td>
                        <span class="badge badge-<?php 
                            echo $transfer['status'] == 'approved' ? 'success' : 
                                ($transfer['status'] == 'pending' ? 'warning' : 'danger'); 
                        ?>">
                            <?php echo ucfirst($transfer['status']); ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($transfer['status'] == 'pending'): ?>
                        <a href="?action=approve&id=<?php echo $transfer['request_id']; ?>" class="btn btn-success" style="padding: 0.35rem 0.75rem; font-size: 0.875rem; margin: 2px;" onclick="return confirm('Approve this transfer?')">
                            Approve
                        </a>
                        <a href="?action=reject&id=<?php echo $transfer['request_id']; ?>" class="btn btn-danger" style="padding: 0.35rem 0.75rem; font-size: 0.875rem; margin: 2px;" onclick="return confirm('Reject this transfer?')">
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
