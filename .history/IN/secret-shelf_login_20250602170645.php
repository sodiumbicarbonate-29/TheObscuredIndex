<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../includes/db_connect.php';

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$access_granted = false;
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['password']) && $_POST['password'] === 'noshamezone') {
        $access_granted = true;
        $_SESSION['secret_shelf_access'] = true;
    } else {
        $error_message = "Incorrect password. Try again.";
    }
}

if (isset($_SESSION['secret_shelf_access']) && $_SESSION['secret_shelf_access'] === true) {
    $access_granted = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secret Shelf - The Obscured Index</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #6c5ce7;
            --secondary-color: #a29bfe;
            --text-color: #2d3436;
            --light-color: #f5f6fa;
            --accent-color: #fd79a8;
            --success-color: #00b894;
            --warning-color: #fdcb6e;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #0f0f1a;
            color: #f5f6fa;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-image: url('../images/secret-bg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        
        main {
            flex: 1;
            padding: 40px 20px;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }
        
        .secret-header {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }
        
        .secret-header h1 {
            font-size: 3rem;
            color: #fff;
            text-shadow: 0 0 10px rgba(108, 92, 231, 0.8), 0 0 20px rgba(108, 92, 231, 0.4);
            margin-bottom: 15px;
            font-family: 'Times New Roman', serif;
            letter-spacing: 2px;
        }
        
        .secret-header p {
            font-size: 1.2rem;
            color: #a29bfe;
            max-width: 700px;
            margin: 0 auto;
            font-style: italic;
        }
        
        .secret-shelf {
            background: rgba(15, 15, 26, 0.8);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5), 0 0 30px rgba(108, 92, 231, 0.3);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(108, 92, 231, 0.3);
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }
        
        .secret-shelf::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(to right, transparent, rgba(108, 92, 231, 0.8), transparent);
            animation: shimmer 2s infinite;
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        .secret-shelf h2 {
            color: #a29bfe;
            margin-bottom: 20px;
            font-size: 1.8rem;
            border-bottom: 1px solid rgba(108, 92, 231, 0.3);
            padding-bottom: 10px;
        }
        
        .secret-message {
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 20px;
        }
        
        .secret-note {
            background: rgba(253, 121, 168, 0.1);
            border-left: 4px solid var(--accent-color);
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }
        
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .back-link:hover {
            color: #fff;
            text-shadow: 0 0 8px var(--accent-color);
        }
        
        .sparkle {
            position: absolute;
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background-color: white;
            pointer-events: none;
            opacity: 0;
            animation: sparkleFloat 2s ease-in-out infinite;
        }
        
        @keyframes sparkleFloat {
            0% { transform: translateY(0) scale(0); opacity: 0; }
            50% { opacity: 0.8; }
            100% { transform: translateY(-20px) scale(1); opacity: 0; }
        }
        
        .password-form {
            max-width: 400px;
            margin: 0 auto;
            text-align: center;
        }
        
        .password-input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(108, 92, 231, 0.5);
            border-radius: 8px;
            color: white;
            font-size: 1rem;
            text-align: center;
            transition: all 0.3s;
        }
        
        .password-input:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 15px rgba(253, 121, 168, 0.3);
        }
        
        .password-submit {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        
        .password-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
        }
        
        .error-message {
            color: var(--accent-color);
            margin-bottom: 15px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <?php include '../includes/navbarIN.php'; ?>

    <main>
        <div class="secret-header">
            <h1>The Secret Shelf</h1>
            <p>You've discovered the hidden collection, known only to the most curious readers...</p>
        </div>
        
        <?php if (!$access_granted): ?>
        <h2>Welcome to the Inner Circle</h2>
        <div class="secret-message">
            <p>Dear <?php echo htmlspecialchars($username); ?>,</p>
            <p>✨ You nosy little gremlin, you actually found the secret shelf. Bravo. 👏</p>
            <p>Welcome to the forbidden archives — where we stash our guilty pleasures, unhinged plotlines, and manhwa so rare, even the algorithm blushed.</p>
            <p>You're now part of the elite chaos circle. Expect exclusive content, spicy surprises, and features we're too embarrassed to list publicly... coming soon.</p>
            <p>Now go forth, and read like no one's judging.</p>
        </div>
        
        <div class="secret-note">
            <p>— The Shelf That Shall Not Be Named</p>
        </div>
        
        <form method="post" class="password-form">
            <?php if (!empty($error_message)): ?>
                <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
            <input type="password" name="password" class="password-input" placeholder="Enter the secret password" autofocus>
            <button type="submit" class="password-submit">Enter</button>
        </form>
    </main>

    <?php 
    $root_path = '../';
    include '../includes/footer.php'; 
    ?>

    <script>
        // Create sparkles
        function createSparkles() {
            const secretShelf = document.querySelector('.secret-shelf');
            const shelfRect = secretShelf.getBoundingClientRect();
            
            for (let i = 0; i < 20; i++) {
                setTimeout(() => {
                    const sparkle = document.createElement('div');
                    sparkle.className = 'sparkle';
                    
                    const left = Math.random() * shelfRect.width;
                    sparkle.style.left = `${left + shelfRect.left}px`;
                    sparkle.style.top = `${shelfRect.bottom - 5}px`;
                    
                    const delay = Math.random() * 2;
                    sparkle.style.animationDelay = `${delay}s`;
                    
                    document.body.appendChild(sparkle);
                    
                    setTimeout(() => {
                        sparkle.remove();
                        createSparkle(shelfRect);
                    }, 2000 + delay * 1000);
                }, i * 100);
            }
        }
        
        function createSparkle(shelfRect) {
            const sparkle = document.createElement('div');
            sparkle.className = 'sparkle';
            
            const left = Math.random() * shelfRect.width;
            sparkle.style.left = `${left + shelfRect.left}px`;
            sparkle.style.top = `${shelfRect.bottom - 5}px`;
            
            const delay = Math.random() * 2;
            sparkle.style.animationDelay = `${delay}s`;
            
            document.body.appendChild(sparkle);
            
            setTimeout(() => {
                sparkle.remove();
                createSparkle(shelfRect);
            }, 2000 + delay * 1000);
        }
        
        window.addEventListener('load', () => {
            createSparkles();
        });
    </script>
</body>
</html>