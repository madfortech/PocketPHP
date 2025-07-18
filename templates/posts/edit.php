<?php include __DIR__ . '/../layouts/header.php'; ?>

<style>
    .container {
        display: flex;
        gap: 20px;
    }

    .item {
        width: 100%;
        padding: 20px 10px;
        margin: 20px 20px;
    }

    input, textarea {
        width: 100%;
        margin-top: 10px;
        padding: 10px 0px;
    }

    button {
        padding: 10px;
        background-color: #000;
        color: #fff;
        border: none;
        cursor: pointer;
    }
    
    @media screen and (max-width: 600px) {
        .container {
            flex-direction: column;
        }
        .item {
            width: 100%;
            margin: 20px 0px;
            padding: 20px 10px;
        }
    }
</style>

<div class="container">
    <div class="item">
        <h1>Edit Post</h1>
    
        <form action="/posts/<?php echo $post->id; ?>" method="POST">
            <input type="hidden" name="_method" value="PUT">
            <div>
                <label for="title">Title</label>
                <br>
                <input type="text" id="title" value="<?php echo $post->title; ?>" name="title">
            </div>
        
            <div>
                <label for="body">Content</label>
                <br>
                <textarea id="body" name="body" rows="5"><?php echo htmlspecialchars($post->body); ?></textarea>
            </div>

            <button type="submit" class="btn">Update Post</button>
        </form>
    </div>
</div>
 
<?php include __DIR__ . '/../layouts/footer.php'; ?>
