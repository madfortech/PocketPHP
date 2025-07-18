<?php include __DIR__ . '/../layouts/header.php'; ?>

<style>
    .container {
        display: flex;
        gap: 20px;
    }

    .item:nth-child(2) {
        flex-grow: 2;
    }

    .item{
        max-width: 100%;
        background-color: #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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
        border: 1px solid #ccc;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
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
        <a href="/">Home</a>
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
                    <div class="post-actions" style="margin-top: 10px;">
                        <a href="/posts/<?php echo $post->id; ?>/edit" class="edit-btn" style="background-color: #4CAF50; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; margin-right: 5px;">Edit</a>
                        <form action="/posts/<?php echo $post->id; ?>" method="POST" style="display: inline-block;">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="delete-btn" style="background-color: #f44336; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer;" 
                                    onclick="return confirm('Are you sure you want to delete this post?')">
                                Delete
                            </button>
                        </form>
                    </div>
            </article>
        <?php endforeach; ?>
    </div>
</div>
