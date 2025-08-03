<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$token = $_GET['token'] ?? '';
if (empty($token) && isset($_SESSION['reset_token'])) {
    $token = $_SESSION['reset_token'];
}
include __DIR__ . '/../layouts/header.php'; 

use PocketSecurity\Security;

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = Security::csrfGenerate();
}

?>

<div class="reset-password-container">
    <div class="reset-password-box">
        <h1>Reset Your Password</h1>
        <p>Please enter your new password below.</p>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error-message">
                <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success-message">
                <?php 
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>
        
        <form action="/auth/reset-password" method="POST" class="reset-password-form">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" name="password" id="password" required minlength="8">
                <small>Password must be at least 8 characters long</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" name="confirm_password" id="confirm_password" required minlength="8">
            </div>
            
            <button type="submit" class="submit-btn">Reset Password</button>
            
            <div class="back-to-login">
                <a href="/auth/login">Back to Login</a>
            </div>
        </form>
    </div>
</div>

<style>
    .reset-password-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: calc(100vh - 200px);
        padding: 20px;
    }
    
    .reset-password-box {
        background: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 450px;
    }
    
    .reset-password-box h1 {
        margin-top: 0;
        color: #333;
        font-size: 24px;
        margin-bottom: 10px;
    }
    
    .reset-password-box p {
        color: #666;
        margin-bottom: 25px;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    label {
        display: block;
        margin-bottom: 8px;
        color: #333;
        font-weight: 500;
    }
    
    input[type="password"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
        margin-bottom: 5px;
    }
    
    small {
        color: #666;
        font-size: 12px;
        display: block;
        margin-top: 2px;
    }
    
    .submit-btn {
        width: 100%;
        padding: 12px;
        background: #4a6baf;
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 16px;
        cursor: pointer;
        margin-top: 10px;
    }
    
    .submit-btn:hover {
        background: #3a5a9a;
    }
    
    .back-to-login {
        text-align: center;
        margin-top: 20px;
    }
    
    .back-to-login a {
        color: #4a6baf;
        text-decoration: none;
    }
    
    .back-to-login a:hover {
        text-decoration: underline;
    }
    
    .alert {
        padding: 10px 15px;
        border-radius: 4px;
        margin-bottom: 20px;
    }
    
    .error-message {
        background-color: #ffebee;
        color: #c62828;
        border: 1px solid #ffcdd2;
    }
    
    .success-message {
        background-color: #e8f5e9;
        color: #2e7d32;
        border: 1px solid #c8e6c9;
    }
</style>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
