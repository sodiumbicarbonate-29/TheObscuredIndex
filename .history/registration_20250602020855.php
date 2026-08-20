<?php
session_start();

// Database connection
require_once 'includes/db_connect.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters";
    } else {
        $check_query = "SELECT * FROM Current_Users WHERE username = ? OR email = ?";
        $stmt = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($stmt, "ss", $username, $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $error = "Username or email already exists";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $insert_query = "INSERT INTO Current_Users (username, email, password_hash) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $insert_query);
            mysqli_stmt_bind_param($stmt, "sss", $username, $email, $password_hash);
            
            if (mysqli_stmt_execute($stmt)) {
                $success = "Registration successful! You can now login.";
            } else {
                $error = "Registration failed: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - The Obscured Index</title>
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
        
        /* Registration Form */
        .form-container {
            max-width: 400px;
            width: 100%;
            margin: 0 auto;
            background-color: rgba(255, 255, 255, 0.3);
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(142, 68, 173, 0.2), 
                        0 0 20px rgba(142, 68, 173, 0.1),
                        0 0 40px rgba(142, 68, 173, 0.05),
                        0 0 60px rgba(241, 196, 15, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-top: 1px solid rgba(241, 196, 15, 0.3);
            border-left: 1px solid rgba(241, 196, 15, 0.2);
            transform: perspective(1000px) rotateX(2deg);
            transition: all 0.5s ease;
            animation: float 6s ease-in-out infinite;
            position: relative;
            overflow: visible;
        }
        
        .form-container::before {
            content: '';
            position: absolute;
            top: -15px;
            left: -15px;
            right: -15px;
            bottom: -15px;
            background: radial-gradient(circle at top left, rgba(241, 196, 15, 0.1) 0%, transparent 70%),
                        radial-gradient(circle at bottom right, rgba(142, 68, 173, 0.1) 0%, transparent 70%);
            z-index: -1;
            border-radius: 20px;
            filter: blur(10px);
        }
        
        @media (max-width: 768px) {
            .form-container {
                max-width: 90%;
                padding: 20px;
                margin: 15px auto;
                transform: none;
                animation: none;
            }
            
            .fairytale-title {
                font-size: 1.4rem;
                margin-bottom: 10px;
            }
            
            .fairytale-subtitle {
                font-size: 0.8rem;
                margin-bottom: 15px;
            }
            
            .form-group {
                margin-bottom: 12px;
            }
        }
        
        @media (max-width: 480px) {
            .container {
                padding: 10px;
            }
            
            .form-container {
                max-width: 85%;
                padding: 15px;
                margin: 0 auto;
                transform: none;
                animation: none;
                box-shadow: 0 5px 15px rgba(108, 92, 231, 0.2);
            }
            
            .fairytale-title {
                font-size: 1.2rem;
                margin-bottom: 8px;
                padding: 0;
            }
            
            .fairytale-subtitle {
                font-size: 0.75rem;
                margin-bottom: 12px;
                padding-bottom: 12px;
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
            
            .login-link {
                margin-top: 10px;
                padding-top: 6px;
                font-size: 0.7rem;
            }
            
            .login-link::before {
                font-size: 0.7rem;
                top: -6px;
            }
            
            .error-message, .success-message {
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
            margin-bottom: 10px;
            font-size: 1.5rem;
            font-weight: 700;
            text-shadow: 0 2px 10px rgba(142, 68, 173, 0.4), 0 0 20px rgba(255, 255, 255, 0.3);
            position: relative;
            font-family: 'Palatino Linotype', 'Book Antiqua', Palatino, serif;
            letter-spacing: 1px;
            line-height: 1.3;
            padding: 0 5px;
        }
        
        .fairytale-title::before,
        .fairytale-title::after {
            content: '';
            position: absolute;
            height: 2px;
            width: 25%;
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
            color:rgb(205, 50, 145);
            margin-bottom: 15px;
            font-size: 0.85rem;
            font-style: italic;
            font-family: 'Georgia', serif;
            text-shadow: 0 1px 1px rgba(0, 0, 0, 0.3), 0 0 6px rgba(241, 196, 15, 0.8);
            position: relative;
            padding-bottom: 15px;
            font-weight: 600;
        }
        
        .fairytale-subtitle::after {
            content: '❈';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            color:rgb(205, 50, 145);
            font-size: 1rem;
            text-shadow: 0 1px 1px rgba(0, 0, 0, 0.5), 0 0 8px rgba(241, 196, 15, 0.9);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: translateX(-50%) scale(1); opacity: 0.7; }
            50% { transform: translateX(-50%) scale(1.2); opacity: 1; }
        }
        
        .form-group {
            margin-bottom: 15px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: var(--text-color);
            transition: all 0.3s;
            font-size: 0.9rem;
        }
        
        .form-group input {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 0.9rem;
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
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.95rem;
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
            background-color: rgba(250, 219, 216, 0.5);
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border-left: 4px solid #e74c3c;
            box-shadow: 0 2px 10px rgba(231, 76, 60, 0.2);
            animation: shake 0.5s ease-in-out;
            backdrop-filter: blur(5px);
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        
        .success-message {
            color: #27ae60;
            background-color: rgba(212, 239, 223, 0.5);
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border-left: 4px solid #27ae60;
            box-shadow: 0 2px 10px rgba(39, 174, 96, 0.2);
            animation: pulse 1.5s infinite;
            backdrop-filter: blur(5px);
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(39, 174, 96, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(39, 174, 96, 0); }
            100% { box-shadow: 0 0 0 0 rgba(39, 174, 96, 0); }
        }
        
        .login-link {
            text-align: center;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid rgba(108, 92, 231, 0.2);
            position: relative;
            font-size: 0.9rem;
        }
        
        .login-link::before {
            content: '✨';
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            background-color: rgba(255, 255, 255, 0.3);
            padding: 0 10px;
            color: var(--accent-color);
        }
        
        @media (max-width: 480px) {
            .login-link::before {
                display: none;
            }
        }
        
        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            position: relative;
            padding: 3px 0;
            transition: all 0.3s;
        }
        
        .login-link a::after {
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
        
        .login-link a:hover {
            text-decoration: none;
            color: var(--accent-color);
        }
        
        .login-link a:hover::after {
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
                <h2 class="fairytale-title">✧ Stop Lurking. It’s Time to Commit, Babe ✧</h2>
                <p class="fairytale-subtitle">Make an account so you can pretend you’re productive while binge-reading</p>
                
                <?php if (!empty($error)): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if (!empty($success)): ?>
                    <div class="success-message"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <button type="submit" class="btn">I Said Click.</button>
                </form>
                
                <div class="login-link">
                    Already have an account? <a href="login.php">Login here</a>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> - My Manhwa Collection. All rights reserved.</p>
    </footer>
</body>
</html>