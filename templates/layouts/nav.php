<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<style>
    .container{
        display: flex;
        gap: 20px;
        align-items: center;
        padding: 10px 20px;
        background-color: #343a40;
        color: white;
    }

    nav{
        width: 100%;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    a {
        color: white;
        text-decoration: none;
        padding: 8px 12px;
        border-radius: 4px;
        transition: background-color 0.2s;
    }
    
    a:hover {
        background-color: #495057;
    }
    
    .user-info {
        margin-left: auto;
        display: flex;
        gap: 15px;
        align-items: center;
    }
    
    .welcome {
        color: #e9ecef;
        font-size: 0.9em;
    }
    
    .btn-outline-light {
        border: 1px solid #f8f9fa;
        padding: 6px 12px;
    }
    
    .btn-outline-light:hover {
        background-color: #f8f9fa;
        color: #212529;
    }
</style>

<nav>
    <div class="container">
        <a href="/">Home</a>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="/posts/create">Create Post</a>
            <div class="user-info">
                <?php if (isset($_SESSION['user_name'])): ?>
                    <span class="welcome">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <?php endif; ?>
                <form action="/auth/logout" method="POST" style="display: inline;">
                    <button type="submit" style="background: none; border: none; color: white; cursor: pointer; padding: 8px 12px; font-size: 1em; border-radius: 4px; transition: background-color 0.2s;">
                        Logout
                    </button>
                </form>
            </div>
        <?php else: ?>
            <div class="user-info">
                <a href="/auth/login">Login</a>
                <a href="/auth/signup" class="btn-outline-light">Sign Up</a>
            </div>
        <?php endif; ?>
    </div>
</nav>