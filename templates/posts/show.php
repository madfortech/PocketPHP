<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <?php if (isset($post) && $post): ?>
        <article class="post">
            <h1><?php echo htmlspecialchars($post->title); ?></h1>
            <div class="post-meta">
                <small>Posted on: <?php echo date('M j, Y', strtotime($post->created_at)); ?></small>
            </div>
            <div class="post-content">
                <?php echo nl2br(htmlspecialchars($post->body)); ?>
            </div>
            <div class="post-actions" style="margin-top: 20px;">
                <a href="/posts" class="btn">Back to Posts</a>
                <a href="/posts/<?php echo $post->id; ?>/edit" class="edit-btn">Edit</a>
                <form action="/posts/<?php echo $post->id; ?>" method="POST" style="display: inline-block;">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this post?')">Delete</button>
                </form>
            </div>
        </article>
    <?php else: ?>
        <div class="alert">
            Post not found.
        </div>
        <a href="/posts" class="btn">Back to Posts</a>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>