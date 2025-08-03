<?php 
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../vendor/autoload.php';
use PocketSecurity\Security;

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = Security::csrfGenerate();
}

include __DIR__ . '/../layouts/header.php'; 
?>

<div class="container">
    <div class="message">
        <h1>Sign Up</h1>
        <p>Create a new account</p>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
        
        <form action="/auth/signup" method="POST">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <div>
                <label for="name">Full Name</label>
                <input type="text" name="name" id="name" required 
                       value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
            </div>
            
            <div>
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            
            <div>
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
                <small>Password must be at least 8 characters long</small>
            </div>
            
            <div>
                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
            </div>
            
            <button type="submit">Sign Up</button>
        </form>
        
        <p>Already have an account? <a href="/auth/login">Login here</a></p>
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
    
    .alert-message {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
        padding: 10px;
        margin: 10px 0;
        border-radius: 4px;
    }
    
    small {
        color: #6c757d;
        display: block;
        margin-top: 5px;
    }
</style>

<?php include __DIR__ . '/../layouts/footer.php'; ?>