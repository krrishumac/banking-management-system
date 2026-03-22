<?php
require_once 'config.php';
require_user_login();

$page_title = 'Transaction History';

$account = get_user_account($_SESSION['user_id']);

$records_per_page = 15;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $records_per_page;

$filter_type = isset($_GET['type']) ? sanitize_input($_GET['type']) : 'all';
$filter_from = isset($_GET['from']) ? sanitize_input($_GET['from']) : '';
$filter_to = isset($_GET['to']) ? sanitize_input($_GET['to']) : '';

$where_clauses = ["account_id = " . $account['account_id']];

if ($filter_type != 'all') {
    $where_clauses[] = "transaction_type = '$filter_type'";
}

if ($filter_from) {
    $where_clauses[] = "DATE(transaction_date) >= '$filter_from'";
}

if ($filter_to) {
    $where_clauses[] = "DATE(transaction_date) <= '$filter_to'";
}

$where_sql = implode(' AND ', $where_clauses);

$count_query = "SELECT COUNT(*) as total FROM transactions WHERE $where_sql";
$count_result = mysqli_query($conn, $count_query);
$total_records = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_records / $records_per_page);

$transactions_query = "SELECT * FROM transactions 
                      WHERE $where_sql 
                      ORDER BY transaction_date DESC 
                      LIMIT $records_per_page OFFSET $offset";
$transactions = mysqli_query($conn, $transactions_query);

$stats_query = "SELECT 
                SUM(CASE WHEN transaction_type IN ('deposit', 'transfer_in') THEN amount ELSE 0 END) as total_credit,
                SUM(CASE WHEN transaction_type IN ('withdrawal', 'transfer_out') THEN amount ELSE 0 END) as total_debit,
                COUNT(*) as total_count
                FROM transactions WHERE account_id = " . $account['account_id'];
$stats = mysqli_fetch_assoc(mysqli_query($conn, $stats_query));

include 'includes/header.php';
?>

<style>
@media print {
    header, nav, h1, .dashboard-grid, form, button, .btn, footer, a {
        display: none;
    }
    
    body {
        background: white;
        color: black;
    }
    
    .card {
        box-shadow: none;
        border: none;
        padding: 0;
    }
    
    .card h2 {
        text-align: center;
        font-size: 20px;
        border-bottom: 2px solid black;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
    }
    
    th, td {
        border: 1px solid #333;
        padding: 8px;
        text-align: left;
        font-size: 11px;
    }
    
    th {
        background: #333;
        color: white;
        font-weight: bold;
    }
    
    tbody tr:nth-child(even) {
        background: #f5f5f5;
    }
}
</style>

<h1 style="color: white; margin: 2rem 0; font-size: 2.5rem;">📊 Transaction History</h1>

<div class="dashboard-grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); margin-bottom: 2rem;">
    <div class="stat-card" style="background: linear-gradient(135deg, #4caf50, #66bb6a);">
        <div class="stat-icon">📈</div>
        <div class="stat-value" style="color: white;"><?php echo format_currency($stats['total_credit']); ?></div>
        <div class="stat-label" style="color: rgba(255,255,255,0.9);">Total Credits</div>
    </div>
    
    <div class="stat-card" style="background: linear-gradient(135deg, #f44336, #e57373);">
        <div class="stat-icon">📉</div>
        <div class="stat-value" style="color: white;"><?php echo format_currency($stats['total_debit']); ?></div>
        <div class="stat-label" style="color: rgba(255,255,255,0.9);">Total Debits</div>
    </div>
    
    <div class="stat-card" style="background: linear-gradient(135deg, #2196f3, #42a5f5);">
        <div class="stat-icon">🔢</div>
        <div class="stat-value" style="color: white;"><?php echo number_format($stats['total_count']); ?></div>
        <div class="stat-label" style="color: rgba(255,255,255,0.9);">Total Transactions</div>
    </div>
    
    <div class="stat-card" style="background: linear-gradient(135deg, #9c27b0, #ba68c8);">
        <div class="stat-icon">💰</div>
        <div class="stat-value" style="color: white;"><?php echo format_currency($account['balance']); ?></div>
        <div class="stat-label" style="color: rgba(255,255,255,0.9);">Current Balance</div>
    </div>
</div>

<div class="card" style="margin-bottom: 2rem;">
    <h3 style="margin-bottom: 1rem; color: var(--primary-color);">🔍 Filter Transactions</h3>
    <form method="GET" action="">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div class="form-group">
                <label for="type">Transaction Type</label>
                <select id="type" name="type">
                    <option value="all" <?php echo $filter_type == 'all' ? 'selected' : ''; ?>>All Types</option>
                    <option value="deposit" <?php echo $filter_type == 'deposit' ? 'selected' : ''; ?>>Deposits</option>
                    <option value="withdrawal" <?php echo $filter_type == 'withdrawal' ? 'selected' : ''; ?>>Withdrawals</option>
                    <option value="transfer_in" <?php echo $filter_type == 'transfer_in' ? 'selected' : ''; ?>>Transfer In</option>
                    <option value="transfer_out" <?php echo $filter_type == 'transfer_out' ? 'selected' : ''; ?>>Transfer Out</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="from">From Date</label>
                <input type="date" id="from" name="from" value="<?php echo htmlspecialchars($filter_from); ?>">
            </div>
            
            <div class="form-group">
                <label for="to">To Date</label>
                <input type="date" id="to" name="to" value="<?php echo htmlspecialchars($filter_to); ?>">
            </div>
            
            <div class="form-group" style="display: flex; align-items: flex-end; gap: 0.5rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">Apply Filters</button>
                <a href="transactions.php" class="btn" style="background: #e0e0e0; color: #333;">Clear</a>
            </div>
        </div>
    </form>
</div>

<div class="card">
    <h2 style="margin-bottom: 1.5rem; color: var(--primary-color);">
        📜 Transaction Records 
        <span style="font-size: 0.9rem; color: #666; font-weight: normal;">
            (<?php echo $total_records; ?> total)
        </span>
    </h2>
    
    <?php if (mysqli_num_rows($transactions) > 0): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Date & Time</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Balance After</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($transaction = mysqli_fetch_assoc($transactions)): 
                        $is_credit = in_array($transaction['transaction_type'], ['deposit', 'transfer_in']);
                    switch($transaction['transaction_type']) {
                            case 'deposit':
                                $type_icon = '💰';
                                break;
                            case 'withdrawal':
                                $type_icon = '💸';
                                break;
                            case 'transfer_in':
                                $type_icon = '📥';
                                break;
                            case 'transfer_out':
                                $type_icon = '📤';
                                break;
                            default:
                                $type_icon = '💵';
                                break;
                        }
                    ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($transaction['reference_number']); ?></strong>
                            </td>
                            <td><?php echo format_date($transaction['transaction_date']); ?></td>
                            <td>
                                <span style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.85rem;
                                    background: <?php echo $is_credit ? '#e8f5e9' : '#ffebee'; ?>;
                                    color: <?php echo $is_credit ? '#2e7d32' : '#c62828'; ?>;">
                                    <?php echo $type_icon . ' ' . ucwords(str_replace('_', ' ', $transaction['transaction_type'])); ?>
                                </span>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($transaction['description']); ?>
                                <?php if ($transaction['recipient_account']): ?>
                                    <br><small style="color: #666;">To: <?php echo htmlspecialchars($transaction['recipient_account']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong style="color: <?php echo $is_credit ? '#4caf50' : '#f44336'; ?>;">
                                    <?php echo ($is_credit ? '+' : '-') . format_currency($transaction['amount']); ?>
                                </strong>
                            </td>
                            <td><?php echo format_currency($transaction['balance_after']); ?></td>
                            <td>
                                <span style="padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.85rem;
                                    background: <?php echo $transaction['status'] == 'completed' ? '#e8f5e9' : '#fff3e0'; ?>;
                                    color: <?php echo $transaction['status'] == 'completed' ? '#2e7d32' : '#e65100'; ?>;">
                                    <?php echo ucfirst($transaction['status']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($total_pages > 1): ?>
            <div style="margin-top: 2rem; display: flex; justify-content: center; gap: 0.5rem; flex-wrap: wrap;">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo ($page - 1) . ($filter_type != 'all' ? '&type=' . $filter_type : '') . ($filter_from ? '&from=' . $filter_from : '') . ($filter_to ? '&to=' . $filter_to : ''); ?>" 
                       class="btn" style="background: #e0e0e0; color: #333; padding: 0.5rem 1rem;">← Previous</a>
                <?php endif; ?>
                
                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                    <a href="?page=<?php echo $i . ($filter_type != 'all' ? '&type=' . $filter_type : '') . ($filter_from ? '&from=' . $filter_from : '') . ($filter_to ? '&to=' . $filter_to : ''); ?>" 
                       class="btn <?php echo $i == $page ? 'btn-primary' : ''; ?>" 
                       style="<?php echo $i != $page ? 'background: #e0e0e0; color: #333;' : ''; ?> padding: 0.5rem 1rem;">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo ($page + 1) . ($filter_type != 'all' ? '&type=' . $filter_type : '') . ($filter_from ? '&from=' . $filter_from : '') . ($filter_to ? '&to=' . $filter_to : ''); ?>" 
                       class="btn" style="background: #e0e0e0; color: #333; padding: 0.5rem 1rem;">Next →</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
    <?php else: ?>
        <div style="text-align: center; padding: 3rem; color: #999;">
            <div style="font-size: 4rem; margin-bottom: 1rem;">📭</div>
            <h3>No transactions found</h3>
            <p>Try adjusting your filters or start making transactions</p>
        </div>
    <?php endif; ?>
</div>

<div style="margin-top: 2rem; display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
    <button onclick="window.print()" class="btn btn-info">🖨️ Print Statement</button>
    <a href="dashboard.php" class="btn" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white;">
        🏠 Back to Dashboard
    </a>
</div>

<?php include 'includes/footer.php'; ?>
