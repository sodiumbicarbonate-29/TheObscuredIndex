<?php
session_start();

// Database connection
$conn = mysqli_connect("localhost", "root", "", "TheObscuredIndex");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

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
                
                header("Location: index.php");
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
    <link rel="stylesheet" href="css/style.css">
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
            background-color: var(--light-color);
            color: var(--text-color);
            line-height: 1.6;
        }
        
        .container {
            background: url('images/index/1.jpg') no-repeat center center fixed;
            background-size: cover;
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Header Styling */
        header {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
            padding: 15px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        #logo {
            padding: 0 20px;
        }
        
        #logo a {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 1.8rem;
            font-weight: bold;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }
        
        #logo a:hover {
            text-shadow: 0 0 10px rgba(108, 92, 231, 0.5);
        }
        
        #navbar ul {
            display: flex;
            list-style: none;
            padding-right: 20px;
        }
        
        #navbar ul li {
            margin-left: 25px;
        }
        
        #navbar ul li a {
            text-decoration: none;
            color: var(--text-color);
            font-weight: 600;
            font-size: 1.1rem;
            transition: color 0.3s;
            position: relative;
        }
        
        #navbar ul li a:hover {
            color: var(--primary-color);
        }
        
        #navbar ul li a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            background-color: var(--primary-color);
            bottom: -5px;
            left: 0;
            transition: width 0.3s;
        }
        
        #navbar ul li a:hover::after {
            width: 100%;
        }
        
        /* Login Form */
        .form-container {
            max-width: 400px;
            margin: 80px auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .form-title {
            text-align: center;
            color: var(--primary-color);
            margin-bottom: 25px;
            font-size: 2rem;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        
        .btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            width: 100%;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: var(--secondary-color);
        }
        
        .error-message {
            color: #e74c3c;
            background-color: #fadbd8;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .register-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .register-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }
        
        .register-link a:hover {
            text-decoration: underline;
        }
        
        /* Footer */
        footer {
            background-color: white;
            padding: 20px 0;
            text-align: center;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            margin-top: 40px;
        }
        
        footer p {
            font-size: 0.9rem;
            color: var(--text-color);
        }
        /* Responsive Design */
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                padding: 10px 0;
            }
            
            #navbar ul {
                margin-top: 15px;
                padding-right: 0;
            }
            
            #navbar ul li {
                margin-left: 15px;
                margin-right: 15px;
            }
            
            main h1 {
                font-size: 2rem;
            }
            
            .slideshow-container {
                height: calc(100vh - 180px);
            }
        }
    </style>
</head>
<body>
    <header>
        <div id="logo">
            <a href="index.html">The Obscured Index</a>
        </div>
        
        <nav id="navbar">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="registration.php">Register</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="container">
            <div class="form-container">
                <h2 class="form-title">Login</h2>
                
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
            <p>&copy; <?php echo date('Y'); ?> The Obscured Index - My Manhwa Collection. All rights reserved.</p>
    </footer>
</body>
</html>