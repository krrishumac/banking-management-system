<?php
require_once 'config.php';
$page_title = 'Contact Us';
include 'includes/header.php';
?>

<div class="card" style="max-width: 800px; margin: 2rem auto;">
    <h1 style="color: var(--primary-color); margin-bottom: 1.5rem;">📞 Contact Us</h1>
    
    <div style="line-height: 1.8; color: #333;">
        <p style="margin-bottom: 2rem; font-size: 1.1rem;">
            We're here to help! Get in touch with us through any of the following channels.
        </p>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
            <div style="padding: 1.5rem; background: #f5f5f5; border-radius: 10px; text-align: center;">
                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">📧</div>
                <h3 style="color: var(--primary-color); margin-bottom: 0.5rem;">Email</h3>
                <p style="margin: 0; color: #666;">support@kmbank.com</p>
                <p style="margin: 0; color: #666;">info@kmbank.com</p>
            </div>
            
            <div style="padding: 1.5rem; background: #f5f5f5; border-radius: 10px; text-align: center;">
                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">📱</div>
                <h3 style="color: var(--primary-color); margin-bottom: 0.5rem;">Phone</h3>
                <p style="margin: 0; color: #666;">+1 (800) 123-4567</p>
                <p style="margin: 0; color: #666;">Mon-Fri: 9AM - 6PM</p>
            </div>
            
            <div style="padding: 1.5rem; background: #f5f5f5; border-radius: 10px; text-align: center;">
                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">📍</div>
                <h3 style="color: var(--primary-color); margin-bottom: 0.5rem;">Address</h3>
                <p style="margin: 0; color: #666;">123 Banking Street</p>
                <p style="margin: 0; color: #666;">New York, NY 10001</p>
            </div>
        </div>
        
        <div style="background: #e3f2fd; padding: 1.5rem; border-radius: 10px; border-left: 4px solid var(--primary-color);">
            <h3 style="color: var(--primary-color); margin-bottom: 1rem;">💬 Send Us a Message</h3>
            <form style="display: grid; gap: 1rem;">
                <div class="form-group">
                    <label for="name">Your Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" required>
                </div>
                
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="5" required style="resize: vertical;"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Send Message</button>
            </form>
        </div>
        
        <div style="margin-top: 2rem; padding: 1rem; background: #fff3e0; border-radius: 10px;">
            <h4 style="color: #e65100; margin-bottom: 0.5rem;">⚡ Quick Response</h4>
            <p style="margin: 0; color: #666;">
                For urgent issues, please call our 24/7 customer service hotline. 
                Email responses typically take 24-48 hours.
            </p>
        </div>
    </div>
    
    <div style="margin-top: 2rem; text-align: center;">
        <a href="javascript:history.back()" class="btn btn-primary">← Go Back</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
