<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include __DIR__ . '/../layouts/header.php'; 
?>

<div class="forgot-password-container">
    <div class="forgot-password-box">
        <h1>Forgot Password</h1>
        <p>Enter your email address and we'll send you a link to reset your password.</p>
        
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
        
        <form action="/auth/forgot-password" method="POST" class="forgot-password-form">
            <div>
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" required 
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
 
            <button type="submit" class="submit-btn">Send Reset Link</button>
            
            <div class="back-to-login">
                <a href="/auth/login">Back to Login</a>
            </div>
        </form>
    </div>
</div>

<style>
    .forgot-password-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: calc(100vh - 200px);
        padding: 20px;
    }
    
    .forgot-password-box {
        background: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 450px;
    }
    
    .forgot-password-box h1 {
        margin-top: 0;
        color: #333;
        font-size: 24px;
        margin-bottom: 10px;
    }
    
    .forgot-password-box p {
        color: #666;
        margin-bottom: 25px;
    }
    
    
    
    label {
        display: block;
        margin-bottom: 8px;
        color: #333;
        font-weight: 500;
    }
    
    input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
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
        transition: background-color 0.3s;
    }
    
    .submit-btn:hover {
        background: #3a5a9a;
    }
    
    .back-to-login {
        text-align: center;
        margin-top: 20px;
    }
    
    .back-to-login a {
        color: #007bff;
        text-decoration: none;
    }
    
    .back-to-login a:hover {
        text-decoration: underline;
    }
    
    .alert {
        padding: 12px 15px;
        border-radius: 4px;
        margin-bottom: 20px;
    }
    
    .error-message {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    
    .success-message {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .container {
        max-width: 600px;
        margin: 50px auto;
        padding: 20px;
    }
    
 
    
    input {
        width: 100%;
        padding: 8px;
        margin-top: 5px;
    }
    
    button {
        padding: 10px 20px;
        background: #007bff;
        color: white;
        border: none;
        cursor: pointer;
        margin-top: 10px;
    }
    
    .alert {
        padding: 10px;
        margin: 10px 0;
        border-radius: 4px;
    }
    
    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    
    .alert-message {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
        padding: 10px;
        margin: 10px 0;
        border-radius: 4px;
    }
</style>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
    
