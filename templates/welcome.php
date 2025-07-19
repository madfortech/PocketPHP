<?php include 'layouts/header.php'; ?>

<div class="welcome-container">
    <div class="welcome-content">
        <h1>Welcome to PocketPHP</h1>
        <p class="lead">A simple and lightweight PHP boilerplate</p>
        
        <?php if (!isset($_SESSION['user_id'])): ?>
            <div class="auth-buttons">
                <a href="/auth/login" class="btn btn-primary">Login</a>
                <a href="/auth/signup" class="btn btn-outline">Sign Up</a>
            </div>
        <?php else: ?>
            <div class="dashboard-links">
                <a href="/posts" class="btn btn-primary">View Posts</a>
                <a href="/posts/create" class="btn btn-outline">Create New Post</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .welcome-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: calc(100vh - 200px);
        padding: 20px;
        text-align: center;
    }
    
    .welcome-content {
        max-width: 800px;
        margin: 0 auto;
        padding: 40px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .welcome-content h1 {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        color: #343a40;
    }
    
    .lead {
        font-size: 1.25rem;
        color: #6c757d;
        margin-bottom: 2rem;
    }
    
    .auth-buttons,
    .dashboard-links {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 2rem;
    }
    
    .btn {
        display: inline-block;
        padding: 10px 24px;
        border-radius: 4px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .btn-primary {
        background-color: #007bff;
        color: white;
        border: 1px solid #007bff;
    }
    
    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }
    
    .btn-outline {
        background-color: transparent;
        color: #007bff;
        border: 1px solid #007bff;
    }
    
    .btn-outline:hover {
        background-color: #f8f9fa;
    }

    

        @media screen and (max-width: 600px) {
            .container {
                flex-direction: column;
            }
            .item:nth-child(2) {
                flex-grow: 1;
                min-width: 100%;
            }

            .item:nth-child(1) {
                flex-grow: 0;
                min-width: 100%;
            }
        }

        ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        li {
            margin-bottom: 10px;
        }

        .article{
            width: 100%;
            border: 1px solid #ccc;
            padding: 20px 0px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
    
        .container {
            display: flex;
            gap: 20px;
        }

        .item:nth-child(2) {
            flex-grow: 2;
        }

        .item{
            max-width: 100%;
            padding: 5px 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        
    </style>

 
<div class="container">
    <div class="item">
        <?php foreach ($posts as $post): ?>
            <ul>
                <li>
                    <a href="/posts/<?php echo $post->id; ?>"><?php echo $post->title; ?></a>
                </li>
            </ul>
           
        <?php endforeach; ?>
 
    </div>

    <div class="item">
        <?php foreach ($posts as $post): ?>
            <article class="article">
            
                <a href="/posts/<?php echo $post->id; ?>">
                    <h1><?php echo htmlspecialchars($post->title); ?></h1>
                </a>
                    <div class="post-meta">
                        <small>Posted on: <?php echo date('M j, Y', strtotime($post->created_at)); ?></small>
                    </div>
                    <div class="post-content">
                        <?php echo nl2br(htmlspecialchars($post->body)); ?>
                    </div>
            </article>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'layouts/footer.php'; ?>