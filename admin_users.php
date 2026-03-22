<?php
require_once 'config.php';
require_admin_login();

$page_title = 'User Management';
$error = '';
$success = '';

if (isset($_GET['action']) && isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    $action = $_GET['action'];
    
    if ($action == 'activate') {
        mysqli_query($conn, "UPDATE users SET status = 'active' WHERE user_id = $user_id");
        $success = 'User activated successfully';
    } elseif ($action == 'suspend') {
        mysqli_query($conn, "UPDATE users SET status = 'suspended' WHERE user_id = $user_id");
        $success = 'User suspended successfully';
    } elseif ($action == 'delete') {
        mysqli_query($conn, "DELETE FROM users WHERE user_id = $user_id");
        $success = 'User deleted successfully';
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_user'])) {
    $username = sanitize_input($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $full_name = sanitize_input($_POST['full_name']);
    $email = sanitize_input($_POST['email']);
    $phone = sanitize_input($_POST['phone']);
    $address = sanitize_input($_POST['address']);
    $dob = sanitize_input($_POST['date_of_birth']);
    $id_number = sanitize_input($_POST['id_number']);
    
    $check_query = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        $error = 'Username or email already exists';
    } else {
        $insert_query = "INSERT INTO users (username, password, full_name, email, phone, address, date_of_birth, id_number) 
                        VALUES ('$username', '$password', '$full_name', '$email', '$phone', '$address', '$dob', '$id_number')";
        
        if (mysqli_query($conn, $insert_query)) {
            $new_user_id = mysqli_insert_id($conn);
            
            $account_number = generate_account_number();
            $account_query = "INSERT INTO accounts (user_id, account_number, account_type, balance) 
                            VALUES ($new_user_id, '$account_number', 'savings', 0.00)";
            mysqli_query($conn, $account_query);
            
            $success = 'User created successfully with account number: ' . $account_number;
        } else {
            $error = 'Error creating user';
        }
    }
}

$users_query = "SELECT u.*, COUNT(a.account_id) as account_count 
                FROM users u 
                LEFT JOIN accounts a ON u.user_id = a.user_id 
                GROUP BY u.user_id 
                ORDER BY u.created_at DESC";
$users = mysqli_query($conn, $users_query);

include 'includes/admin_header.php';
?>

<h1 style="color: white; margin: 2rem 0; font-size: 2.5rem;">User Management</h1>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo $error; ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">Create New User</div>
    <form method="POST" action="">
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="tel" id="phone" name="phone" placeholder="+91XXXXXXXXXX" pattern="\+91[0-9]{10}" title="Please enter Indian phone number in format: +91XXXXXXXXXX" required>
            </div>
            
            <div class="form-group">
                <label for="date_of_birth">Date of Birth</label>
                <input type="date" id="date_of_birth" name="date_of_birth" required>
            </div>
            
            <div class="form-group">
                <label for="id_number">ID Number</label>
                <input type="text" id="id_number" name="id_number" required>
            </div>
            
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" required>
            </div>
        </div>
        
        <div class="form-group">
            <button type="submit" name="create_user" class="btn btn-success">Create User</button>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header">All Users</div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Accounts</th>
                    <th>Status</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = mysqli_fetch_assoc($users)): ?>
                <tr>
                    <td>#<?php echo $user['user_id']; ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['phone']); ?></td>
                    <td><?php echo $user['account_count']; ?></td>
                    <td>
                        <span class="badge badge-<?php 
                            echo $user['status'] == 'active' ? 'success' : 
                                ($user['status'] == 'suspended' ? 'warning' : 'danger'); 
                        ?>">
                            <?php echo ucfirst($user['status']); ?>
                        </span>
                    </td>
                    <td><?php echo format_date($user['created_at']); ?></td>
                    <td>
                        <a href="admin_user_view.php?id=<?php echo $user['user_id']; ?>" class="btn btn-info" style="padding: 0.35rem 0.75rem; font-size: 0.875rem; margin: 2px;">
                            View
                        </a>
                        <?php if ($user['status'] == 'active'): ?>
                        <a href="?action=suspend&id=<?php echo $user['user_id']; ?>" class="btn btn-warning" style="padding: 0.35rem 0.75rem; font-size: 0.875rem; margin: 2px;" onclick="return confirm('Suspend this user?')">
                            Suspend
                        </a>
                        <?php else: ?>
                        <a href="?action=activate&id=<?php echo $user['user_id']; ?>" class="btn btn-success" style="padding: 0.35rem 0.75rem; font-size: 0.875rem; margin: 2px;">
                            Activate
                        </a>
                        <?php endif; ?>
                        <a href="?action=delete&id=<?php echo $user['user_id']; ?>" class="btn btn-danger" style="padding: 0.35rem 0.75rem; font-size: 0.875rem; margin: 2px;" onclick="return confirm('Delete this user?')">
                            Delete
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
