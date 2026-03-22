<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>KM BANK Admin</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="animated-bg"></div>
    <header>
        <div class="header-content">
            <a href="admin_dashboard.php" class="logo">🏦 KM BANK - Admin Panel</a>
            <nav>
                <ul>
                    <li><a href="admin_dashboard.php">Dashboard</a></li>
                    <li><a href="admin_users.php">Users</a></li>
                    <li><a href="admin_accounts.php">Accounts</a></li>
                    <li><a href="admin_transactions.php">Transactions</a></li>
                    <li><a href="admin_withdrawals.php">Withdrawals</a></li>
                    <li><a href="admin_transfers.php">Transfers</a></li>
                    <li><a href="admin_logout.php" class="btn btn-danger">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <div class="container">
