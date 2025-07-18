<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="verify-email-container">
    <div class="verify-email-box">
        <h1>Verify Your Email Address</h1>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-info">
                <?php 
                  
                    unset($_SESSION['message']);
                ?>
            </div>
        <?php endif; ?>
        
        <p>Before proceeding, please check your email for a verification link.</p>
        <p>If you did not receive the email,</p>
        
        <form action="/email/resend" method="POST" class="resend-form">
            <button type="submit" class="btn btn-primary">
                Click here to request another
            </button>
        </form>
        
        <!-- Verification URL is stored in session for testing if needed -->
    </div>
</div>

<style>
    .verify-email-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 80vh;
        padding: 20px;
    }
    
    .verify-email-box {
        max-width: 500px;
        width: 100%;
        padding: 30px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        text-align: center;
    }
    
    .verify-email-box h1 {
        margin-top: 0;
        color: #4a6baf;
        margin-bottom: 20px;
    }
    
    .verify-email-box p {
        margin: 15px 0;
        color: #6c757d;
    }
    
    .resend-form {
        margin: 20px 0;
    }
    
    .btn {
        display: inline-block;
        padding: 10px 20px;
        background-color: #4a6baf;
        color: white;
        border: none;
        border-radius: 4px;
        text-decoration: none;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    
    .btn:hover {
        background-color: #3a5a9a;
    }
    
    .alert {
        padding: 12px 15px;
        border-radius: 4px;
        margin-bottom: 20px;
    }
    
    .alert-info {
        background-color: #d1ecf1;
        color: #0c5460;
        border: 1px solid #bee5eb;
    }
</style>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
