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
            --primary-color: #6c5ce7;
            --secondary-color: #a29bfe;
            --text-color: #2d3436;
            --light-color: #f5f6fa;
            --accent-color: #fd79a8;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
        
        .container {
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 20px;
            flex: 1;
        }
        
        /* Registration Form */
        .form-container {
            max-width: 500px;
            margin: 40px auto;
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
                margin: 20px auto;
                transform: none;
                animation: none;
            }
            
            .form-title {
                font-size: 1.8rem;
                margin-bottom: 20px;
            }
        }
        
        @media (max-width: 480px) {
            .container {
                padding: 10px;
            }
            
            .form-container {
                max-width: 75%;
                padding: 15px;
                margin: 10px auto;
                transform: none;
                animation: none;
                box-shadow: 0 5px 15px rgba(108, 92, 231, 0.2);
            }
            
            .form-title {
                font-size: 1.4rem;
                margin-bottom: 15px;
            }
            
            .form-title::after {
                width: 40px;
                height: 2px;
                bottom: -8px;
            }
            
            .form-group {
                margin-bottom: 12px;
            }
            
            .form-group label {
                margin-bottom: 4px;
                font-size: 0.8rem;
            }
            
            .form-group input {
                padding: 8px 10px;
                font-size: 0.85rem;
                border-width: 1.4px;
            }
            
            .btn {
                padding: 10px 20px;
                font-size: 0.9rem;
                width: 90%;
            }
            
            .login-link {
                margin-top: 15px;
                padding-top: 10px;
                font-size: 0.8rem;
            }
            
            .error-message, .success-message {
                padding: 8px;
                font-size: 0.8rem;
                margin-bottom: 12px;
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
        
        .form-title {
            text-align: center;
            color: var(--primary-color);
            margin-bottom: 25px;
            font-size: 2.2rem;
            font-weight: 700;
            text-shadow: 0 2px 5px rgba(108, 92, 231, 0.2);
            position: relative;
        }
        
        .form-title::after {
            content: '';
            position: absolute;
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 10px;
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
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid rgba(108, 92, 231, 0.2);
            position: relative;
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
                <h2 class="form-title">Create an Account</h2>
                
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
                    
                    <button type="submit" class="btn">Register</button>
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