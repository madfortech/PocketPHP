<?php 
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../vendor/autoload.php';
use PocketSecurity\Security;
use PocketErrorLog\ErrorLog;

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = Security::csrfGenerate();
}

// Debug: Log the generated token and session info
ErrorLog::log('=== LOGIN PAGE LOADED ===');
ErrorLog::log('Session ID: ' . session_id());
ErrorLog::log('CSRF Token in session: ' . ($_SESSION['csrf_token'] ?? 'NOT SET'));
ErrorLog::log('Session data: ' . print_r($_SESSION, true));

// Ensure we have a valid token
if (empty($_SESSION['csrf_token']) || strlen($_SESSION['csrf_token']) !== 64) {
    ErrorLog::log('Generating new CSRF token');
    $_SESSION['csrf_token'] = Security::csrfGenerate();
}

include __DIR__ . '/../layouts/header.php'; 
?>

<div class="container">
    <div class="message">
        <h1>Login</h1>
        <p>Login to your account</p>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php 
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>
        
        <form action="/auth/login" method="POST">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
            
            <div>
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
            </div>
            
            <div>
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>

            <div style="margin-top: 10px;">
                <a href="/auth/forgot-password">Forgot Password?</a>
            </div>

            <div style="margin-top: 10px;">
                <input type="checkbox" name="remember_me" id="remember_me">
                <label for="remember_me">Remember me</label>
            </div>
                
            
            <button type="submit">Login</button>
        </form>
        
        <p>Don't have an account? <a href="/auth/signup">Sign up here</a></p>
    </div>
</div>

<style>
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
    
