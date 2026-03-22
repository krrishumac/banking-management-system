<?php
require_once 'config.php';
$page_title = 'Privacy Policy';
include 'includes/header.php';
?>

<div class="card" style="max-width: 900px; margin: 2rem auto;">
    <h1 style="color: var(--primary-color); margin-bottom: 1.5rem;">🔒 Privacy Policy</h1>
    
    <div style="line-height: 1.8; color: #333;">
        <p style="margin-bottom: 1rem;">
            <strong>Last Updated:</strong> <?php echo date('F d, Y'); ?>
        </p>
        
        <h3 style="color: var(--primary-color); margin-top: 2rem; margin-bottom: 1rem;">1. Information We Collect</h3>
        <p style="margin-bottom: 1rem;">
            We collect personal information that you provide to us when you register for an account, including your name, 
            email address, phone number, and other contact details. We also collect transaction data and account activity 
            information to provide our banking services.
        </p>
        
        <h3 style="color: var(--primary-color); margin-top: 2rem; margin-bottom: 1rem;">2. How We Use Your Information</h3>
        <p style="margin-bottom: 1rem;">
            Your information is used to:
        </p>
        <ul style="margin-left: 2rem; margin-bottom: 1rem;">
            <li>Process your banking transactions</li>
            <li>Maintain and secure your account</li>
            <li>Provide customer support</li>
            <li>Comply with legal and regulatory requirements</li>
            <li>Improve our services</li>
        </ul>
        
        <h3 style="color: var(--primary-color); margin-top: 2rem; margin-bottom: 1rem;">3. Data Security</h3>
        <p style="margin-bottom: 1rem;">
            We implement appropriate security measures to protect your personal information from unauthorized access, 
            alteration, disclosure, or destruction. Your account data is encrypted and stored securely on our servers.
        </p>
        
        <h3 style="color: var(--primary-color); margin-top: 2rem; margin-bottom: 1rem;">4. Information Sharing</h3>
        <p style="margin-bottom: 1rem;">
            We do not sell or rent your personal information to third parties. Information may be shared only when 
            required by law or with your explicit consent.
        </p>
        
        <h3 style="color: var(--primary-color); margin-top: 2rem; margin-bottom: 1rem;">5. Your Rights</h3>
        <p style="margin-bottom: 1rem;">
            You have the right to access, update, or delete your personal information. You can manage your account 
            settings through your profile page or contact us for assistance.
        </p>
        
        <h3 style="color: var(--primary-color); margin-top: 2rem; margin-bottom: 1rem;">6. Contact Us</h3>
        <p style="margin-bottom: 1rem;">
            If you have any questions about this Privacy Policy, please contact us through our Help Center.
        </p>
    </div>
    
    <div style="margin-top: 2rem; text-align: center;">
        <a href="javascript:history.back()" class="btn btn-primary">← Go Back</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
