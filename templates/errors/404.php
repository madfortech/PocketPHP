<?php 
http_response_code(404);
?>

<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="error-page">
    <div class="error-content">
        <h1 class="error-code">404</h1>
        <h2 class="error-title">Oops! Page Not Found</h2>
        <p class="error-message">
            The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.
        </p>
        <div class="error-actions">
            <a href="/" class="btn btn-primary">
                <i class="fas fa-home"></i> Back to Home
            </a>
            <a href="javascript:history.back()" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Go Back
            </a>
        </div>
    </div>
</div>

<style>
    .error-page {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 80vh;
        padding: 20px;
        text-align: center;
        background-color: #f8f9fa;
    }

    .error-content {
        max-width: 500px;
        padding: 40px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .error-code {
        font-size: 6rem;
        font-weight: 700;
        color: #4a6baf;
        margin: 0 0 10px;
        line-height: 1;
    }

    .error-title {
        font-size: 2rem;
        color: #343a40;
        margin: 0 0 15px;
    }

    .error-message {
        color: #6c757d;
        margin-bottom: 30px;
        line-height: 1.6;
    }

    .error-actions {
        display: flex;
        gap: 15px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 20px;
        border-radius: 4px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .btn i {
        margin-right: 8px;
    }

    .btn-primary {
        background-color: #4a6baf;
        color: white;
        border: 1px solid #3a5a9a;
    }

    .btn-primary:hover {
        background-color: #3a5a9a;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
        border: 1px solid #5a6268;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
    }

    @media (max-width: 576px) {
        .error-code {
            font-size: 4rem;
        }
        
        .error-title {
            font-size: 1.5rem;
        }
        
        .error-actions {
            flex-direction: column;
            gap: 10px;
        }
        
        .btn {
            width: 100%;
        }
    }
</style>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
