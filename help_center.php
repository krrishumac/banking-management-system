<?php
require_once 'config.php';
$page_title = 'Help Center';
include 'includes/header.php';
?>

<div class="card" style="max-width: 900px; margin: 2rem auto;">
    <h1 style="color: var(--primary-color); margin-bottom: 1.5rem;">❓ Help Center</h1>
    
    <div style="line-height: 1.8; color: #333;">
        <p style="margin-bottom: 2rem; font-size: 1.1rem;">
            Find answers to frequently asked questions and learn how to use our banking services.
        </p>
        
        <h2 style="color: var(--primary-color); margin-top: 2rem; margin-bottom: 1rem;">📋 Frequently Asked Questions</h2>
        
        <div style="display: grid; gap: 1rem;">
            <details style="padding: 1rem; background: #f5f5f5; border-radius: 8px; cursor: pointer;">
                <summary style="font-weight: bold; color: var(--primary-color); cursor: pointer;">
                    How do I create an account?
                </summary>
                <p style="margin-top: 1rem; padding-left: 1rem;">
                    Click on the "Register" button on the homepage, fill in your personal information, and submit the form. 
                    Your account will be created with a unique account number automatically.
                </p>
            </details>
            
            <details style="padding: 1rem; background: #f5f5f5; border-radius: 8px; cursor: pointer;">
                <summary style="font-weight: bold; color: var(--primary-color); cursor: pointer;">
                    How do I make a withdrawal?
                </summary>
                <p style="margin-top: 1rem; padding-left: 1rem;">
                    Log in to your account, go to the "Withdraw" page, enter the amount you wish to withdraw, and submit. 
                    Your withdrawal request will be reviewed by an administrator.
                </p>
            </details>
            
            <details style="padding: 1rem; background: #f5f5f5; border-radius: 8px; cursor: pointer;">
                <summary style="font-weight: bold; color: var(--primary-color); cursor: pointer;">
                    How long does it take to process a transfer?
                </summary>
                <p style="margin-top: 1rem; padding-left: 1rem;">
                    Transfer requests typically require administrative approval and are processed within 24-48 hours. 
                    You can check the status of your transfer in the "Transfer" section.
                </p>
            </details>
            
            <details style="padding: 1rem; background: #f5f5f5; border-radius: 8px; cursor: pointer;">
                <summary style="font-weight: bold; color: var(--primary-color); cursor: pointer;">
                    How can I check my transaction history?
                </summary>
                <p style="margin-top: 1rem; padding-left: 1rem;">
                    Navigate to the "Transactions" page from your dashboard. You can filter transactions by type, 
                    date range, and view detailed information about each transaction.
                </p>
            </details>
            
            <details style="padding: 1rem; background: #f5f5f5; border-radius: 8px; cursor: pointer;">
                <summary style="font-weight: bold; color: var(--primary-color); cursor: pointer;">
                    What should I do if I forget my password?
                </summary>
                <p style="margin-top: 1rem; padding-left: 1rem;">
                    Click on "Forgot Password" on the login page. You'll need to contact an administrator to reset 
                    your password for security purposes.
                </p>
            </details>
            
            <details style="padding: 1rem; background: #f5f5f5; border-radius: 8px; cursor: pointer;">
                <summary style="font-weight: bold; color: var(--primary-color); cursor: pointer;">
                    Is my account information secure?
                </summary>
                <p style="margin-top: 1rem; padding-left: 1rem;">
                    Yes! We use industry-standard security measures including password encryption and secure session 
                    management to protect your account information.
                </p>
            </details>
            
            <details style="padding: 1rem; background: #f5f5f5; border-radius: 8px; cursor: pointer;">
                <summary style="font-weight: bold; color: var(--primary-color); cursor: pointer;">
                    How do I update my profile information?
                </summary>
                <p style="margin-top: 1rem; padding-left: 1rem;">
                    Go to your "Profile" page, click on "Edit Profile", make your changes, and save. You can also 
                    change your password from the same page.
                </p>
            </details>
        </div>
        
        <div style="margin-top: 2rem; padding: 1.5rem; background: #e8f5e9; border-radius: 10px; border-left: 4px solid #4caf50;">
            <h3 style="color: #2e7d32; margin-bottom: 0.5rem;">💡 Need More Help?</h3>
            <p style="margin: 0; color: #666;">
                If you can't find the answer you're looking for, please visit our 
                <a href="contact_us.php" style="color: var(--primary-color); text-decoration: underline;">Contact Us</a> 
                page or reach out to our customer support team.
            </p>
        </div>
        
        <div style="margin-top: 2rem; padding: 1.5rem; background: #fff3e0; border-radius: 10px;">
            <h3 style="color: #e65100; margin-bottom: 1rem;">🔐 Security Tips</h3>
            <ul style="margin-left: 1.5rem; color: #666;">
                <li>Never share your password with anyone</li>
                <li>Log out after completing your banking session</li>
                <li>Use a strong password with letters, numbers, and symbols</li>
                <li>Regularly check your transaction history for suspicious activity</li>
                <li>Contact us immediately if you notice any unauthorized transactions</li>
            </ul>
        </div>
    </div>
    
    <div style="margin-top: 2rem; text-align: center;">
        <a href="javascript:history.back()" class="btn btn-primary">← Go Back</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
