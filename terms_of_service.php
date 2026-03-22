<?php
require_once 'config.php';
$page_title = 'Terms of Service';
include 'includes/header.php';
?>

<div class="card" style="max-width: 900px; margin: 2rem auto;">
    <h1 style="color: var(--primary-color); margin-bottom: 1.5rem;">📜 Terms of Service</h1>
    
    <div style="line-height: 1.8; color: #333;">
        <p style="margin-bottom: 1rem;">
            <strong>Effective Date:</strong> <?php echo date('F d, Y'); ?>
        </p>
        
        <h3 style="color: var(--primary-color); margin-top: 2rem; margin-bottom: 1rem;">1. Acceptance of Terms</h3>
        <p style="margin-bottom: 1rem;">
            By accessing and using KM BANK PVT LTD services, you accept and agree to be bound by these Terms of Service. 
            If you do not agree to these terms, please do not use our services.
        </p>
        
        <h3 style="color: var(--primary-color); margin-top: 2rem; margin-bottom: 1rem;">2. Account Registration</h3>
        <p style="margin-bottom: 1rem;">
            To use our banking services, you must:
        </p>
        <ul style="margin-left: 2rem; margin-bottom: 1rem;">
            <li>Be at least 18 years of age</li>
            <li>Provide accurate and complete information</li>
            <li>Maintain the security of your account credentials</li>
            <li>Notify us immediately of any unauthorized access</li>
        </ul>
        
        <h3 style="color: var(--primary-color); margin-top: 2rem; margin-bottom: 1rem;">3. Account Usage</h3>
        <p style="margin-bottom: 1rem;">
            You agree to use your account only for lawful purposes. You must not use our services for any fraudulent, 
            illegal, or unauthorized activities. We reserve the right to suspend or terminate accounts that violate 
            these terms.
        </p>
        
        <h3 style="color: var(--primary-color); margin-top: 2rem; margin-bottom: 1rem;">4. Transaction Policies</h3>
        <p style="margin-bottom: 1rem;">
            All transactions are subject to verification and approval by our system. Withdrawals and transfers may 
            require administrative approval. We reserve the right to decline any transaction that appears suspicious 
            or violates our policies.
        </p>
        
        <h3 style="color: var(--primary-color); margin-top: 2rem; margin-bottom: 1rem;">5. Fees and Charges</h3>
        <p style="margin-bottom: 1rem;">
            Account maintenance and transaction fees may apply. You will be notified of any applicable fees before 
            they are charged to your account.
        </p>
        
        <h3 style="color: var(--primary-color); margin-top: 2rem; margin-bottom: 1rem;">6. Limitation of Liability</h3>
        <p style="margin-bottom: 1rem;">
            KM BANK PVT LTD is not liable for any indirect, incidental, or consequential damages arising from your 
            use of our services. Our liability is limited to the maximum extent permitted by law.
        </p>
        
        <h3 style="color: var(--primary-color); margin-top: 2rem; margin-bottom: 1rem;">7. Changes to Terms</h3>
        <p style="margin-bottom: 1rem;">
            We reserve the right to modify these terms at any time. Continued use of our services after changes 
            constitutes acceptance of the modified terms.
        </p>
    </div>
    
    <div style="margin-top: 2rem; text-align: center;">
        <a href="javascript:history.back()" class="btn btn-primary">← Go Back</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
