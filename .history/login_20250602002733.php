<?php
session_start();

require_once 'includes/db_connect.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = "Username and password are required";
    } else {
        $query = "SELECT user_id, username, password_hash FROM Current_Users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            
            if (password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                
                header("Location: IN/home.php");
                exit();
            } else {
                $error = "Invalid username or password";
            }
        } else {
            $error = "Invalid username or password";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - The Obscured Index</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #8e44ad;
            --secondary-color: #9b59b6;
            --text-color: #2d3436;
            --light-color: #f5f6fa;
            --accent-color: #e84393;
            --gold-color: #f1c40f;
            --silver-color: #bdc3c7;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        html, body {
            height: 100%;
            width: 100%;
        }
        
        body {
            background: url('images/index/1.jpg') no-repeat center center fixed;
            background-size: cover;
            color: var(--text-color);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }
        
        main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: calc(100vh - 120px); 
        }
        
        .container {
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        @media (max-width: 480px) {
            .container {
                padding: 10px;
            }
        }
                
        /* Login Form */
        .form-container {
            max-width: 400px;
            width: 100%;
            margin: 0 auto;
            background-color: rgba(255, 255, 255, 0.3);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(108, 92, 231, 0.2), 
                        0 0 20px rgba(108, 92, 231, 0.1),
                        0 0 40px rgba(108, 92, 231, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transform: perspective(1000px) rotateX(2deg);
            transition: all 0.5s ease;
            animation: float 6s ease-in-out infinite;
        }
        
        @media (max-width: 768px) {
            .form-container {
                max-width: 90%;
                padding: 25px;
                transform: none;
                animation: none;
            }
            
            .form-title {
                font-size: 1.8rem;
                margin-bottom: 20px;
            }
        }
        
        @media (max-width: 480px) {
            .form-container {
                max-width: 80%;
                padding: 10px;
                transform: none;
                animation: none;
                box-shadow: 0 5px 15px rgba(108, 92, 231, 0.2);
            }
            
            .form-title {
                font-size: 1.2rem;
                margin-bottom: 10px;
            }
            
            .form-title::after {
                width: 30px;
                height: 2px;
                bottom: -5px;
            }
            
            .form-group {
                margin-bottom: 8px;
            }
            
            .form-group label {
                margin-bottom: 2px;
                font-size: 0.7rem;
            }
            
            .form-group input {
                padding: 5px 8px;
                font-size: 0.7rem;
                border-width: 1px;
            }
            
            .btn {
                padding: 6px;
                font-size: 0.75rem;
            }
            
            .register-link {
                margin-top: 10px;
                padding-top: 6px;
                font-size: 0.7rem;
            }
            
            .register-link::before {
                font-size: 0.7rem;
                top: -6px;
            }
            
            .error-message {
                padding: 6px;
                font-size: 0.7rem;
                margin-bottom: 10px;
            }
        }
        
        .form-container:hover {
            transform: perspective(1000px) rotateX(0deg) scale(1.02);
            box-shadow: 0 15px 35px rgba(108, 92, 231, 0.4), 
                        0 0 25px rgba(108, 92, 231, 0.3),
                        0 0 50px rgba(108, 92, 231, 0.2);
        }
        
        @media (hover: none) {
            .form-container:hover {
                transform: none;
            }
        }
        
        @keyframes float {
            0% {
                transform: perspective(1000px) rotateX(2deg) translateY(0px);
            }
            50% {
                transform: perspective(1000px) rotateX(1deg) translateY(-10px);
            }
            100% {
                transform: perspective(1000px) rotateX(2deg) translateY(0px);
            }
        }
        
        .fairytale-title {
            text-align: center;
            color: #8e44ad;
            margin-bottom: 15px;
            font-size: 1.8rem;
            font-weight: 700;
            text-shadow: 0 2px 10px rgba(142, 68, 173, 0.4), 0 0 20px rgba(255, 255, 255, 0.3);
            position: relative;
            font-family: 'Palatino Linotype', 'Book Antiqua', Palatino, serif;
            letter-spacing: 1px;
            line-height: 1.4;
            padding: 0 10px;
        }
        
        .fairytale-title::before,
        .fairytale-title::after {
            content: '';
            position: absolute;
            height: 2px;
            width: 30%;
            background: linear-gradient(90deg, transparent, #e84393, transparent);
            top: 50%;
            transform: translateY(-50%);
        }
        
        .fairytale-title::before {
            left: 0;
        }
        
        .fairytale-title::after {
            right: 0;
        }
        
        .fairytale-subtitle {
            text-align: center;
            color: #9b59b6;
            margin-bottom: 25px;
            font-size: 0.95rem;
            font-style: italic;
            font-family: 'Georgia', serif;
            text-shadow: 0 1px 3px rgba(155, 89, 182, 0.3);
            position: relative;
            padding-bottom: 20px;
        }
        
        .fairytale-subtitle::after {
            content: '❈';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            color: #e84393;
            font-size: 1rem;
            text-shadow: 0 0 5px rgba(232, 67, 147, 0.5);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: translateX(-50%) scale(1); opacity: 0.7; }
            50% { transform: translateX(-50%) scale(1.2); opacity: 1; }
        }
        
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-color);
            transition: all 0.3s;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
            background-color: rgba(255, 255, 255, 0.8);
        }
        
        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 10px rgba(108, 92, 231, 0.2);
            background-color: white;
        }
        
        .btn {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            padding: 14px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
            box-shadow: 0 4px 10px rgba(108, 92, 231, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: all 0.6s;
        }
        
        .btn:hover {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(108, 92, 231, 0.4);
        }
        
        .btn:hover::before {
            left: 100%;
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .error-message {
            color: #e74c3c;
            background-color: rgba(250, 219, 216, 0.8);
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border-left: 4px solid #e74c3c;
            box-shadow: 0 2px 10px rgba(231, 76, 60, 0.2);
            animation: shake 0.5s ease-in-out;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        
        .register-link {
            text-align: center;
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid rgba(108, 92, 231, 0.2);
            position: relative;
        }
        
        .register-link::before {
            content: '✨';
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            background-color: rgba(255, 255, 255, 0.9);
            padding: 0 10px;
            color: var(--accent-color);
        }
        
        .register-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            position: relative;
            padding: 3px 0;
            transition: all 0.3s;
        }
        
        .register-link a::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 2px;
            bottom: 0;
            left: 0;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
            transform: scaleX(0);
            transform-origin: bottom right;
            transition: transform 0.3s;
        }
        
        .register-link a:hover {
            text-decoration: none;
            color: var(--accent-color);
        }
        
        .register-link a:hover::after {
            transform: scaleX(1);
            transform-origin: bottom left;
        }
        
        /* Footer */
        footer {
            background-color: rgba(255, 255, 255, 0.2);
            padding: 10px 0;
            text-align: center;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
            position: relative;
            z-index: 10;
            margin-top: auto;
            width: 100%;
            backdrop-filter: blur(8px);
        }
        
        footer p {
            font-size: 0.9rem;
            color: var(--text-color);
        }
        
    </style>
</head>
<body>
    <?php include 'includes/navbarOUT.php'; ?>

    <main>
        <div class="container">
            <div class="form-container">
                <h2 class="fairytale-title">✧ Not all heroes wear capes ✧</h2>
                <p class="fairytale-subtitle">Once upon a time, they just needed their login details to read until 3 A.M.</p>
                
                <?php if (!empty($error)): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <button type="submit" class="btn">Login</button>
                </form>
                
                <div class="register-link">
                    Don't have an account? <a href="registration.php">Register here</a>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> - My Manhwa Collection. All rights reserved.</p>
    </footer>
</body>
</html>