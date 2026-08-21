<?php
session_start();
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
        // Check if username exists
        $check = mysqli_prepare($conn, "SELECT user_id FROM Current_Users WHERE username = ?");
        mysqli_stmt_bind_param($check, "s", $username);
        mysqli_stmt_execute($check);
        if (mysqli_stmt_get_result($check)->num_rows > 0) {
            $error = "Username already taken";
        } else {
            // Check if email exists
            $check = mysqli_prepare($conn, "SELECT user_id FROM Current_Users WHERE email = ?");
            mysqli_stmt_bind_param($check, "s", $email);
            mysqli_stmt_execute($check);
            if (mysqli_stmt_get_result($check)->num_rows > 0) {
                $error = "Email already registered";
            } else {
                // Create user
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = mysqli_prepare($conn, "INSERT INTO Current_Users (username, email, password_hash) VALUES (?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "sss", $username, $email, $password_hash);
                
                if (mysqli_stmt_execute($stmt)) {
                    $success = "Account created! You can now login.";
                } else {
                    $error = "Registration failed. Please try again.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" type="image/png" href="images/logo.png">
<script src="IN/js/support.js"></script>
</head>
<body>
<x-dc>
<helmet>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&family=Playfair+Display:ital,wght@0,500;1,500&display=swap" rel="stylesheet">
<style>
  body { margin: 0; }
  @keyframes twinkle { 0%,100% { opacity: 0; transform: scale(0.4); } 50% { opacity: 1; transform: scale(1); } }
  @keyframes floaty { 0%,100% { transform: translateY(0) rotate(-0.4deg); } 50% { transform: translateY(-8px) rotate(0.4deg); } }
  @keyframes sparklePop { 0% { transform: scale(0) rotate(0deg); opacity: 0; } 40% { opacity: 1; } 100% { transform: scale(1) rotate(90deg) translateY(-24px); opacity: 0; } }
  .om-sparkle { position: fixed; pointer-events: none; z-index: 9999; font-size: 14px; animation: sparklePop 0.9s ease-out forwards; }
  .error-message { background: rgba(231, 76, 60, 0.2); border: 1px solid rgba(231, 76, 60, 0.5); color: #ff6b6b; padding: 12px; border-radius: 8px; margin-bottom: 18px; text-align: center; font-size: 0.85rem; }
  .success-message { background: rgba(46, 204, 113, 0.2); border: 1px solid rgba(46, 204, 113, 0.5); color: #2ecc71; padding: 12px; border-radius: 8px; margin-bottom: 18px; text-align: center; font-size: 0.85rem; }
</style>
</helmet>

<div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; min-height: 100vh; display: flex; flex-direction: column; background: #0e0a17;">

  <header style="position: fixed; top: 0; left: 0; right: 0; z-index: 100; display: flex; justify-content: space-between; align-items: center; height: 76px; padding: 0 32px; background: rgba(14, 10, 23, 0.45); backdrop-filter: blur(14px); border-bottom: 1px solid rgba(255,255,255,0.08);">
    <a href="index.php" style="display: flex; align-items: center; gap: 12px; text-decoration: none;">
      <img src="images/logo3.png" alt="The Obscured Index logo" style="height: 44px; width: 44px; object-fit: contain; filter: drop-shadow(0 0 6px rgba(162,155,254,0.35));">
      <span style="font-family: 'Cinzel', serif; font-weight: 600; font-size: 1.15rem; color: #f5f3fb; letter-spacing: 0.02em;">The Obscured Index</span>
    </a>
    <nav style="display: flex; align-items: center; gap: 28px;">
      <a href="index.php" style="font-family: 'Cinzel', serif; font-size: 0.85rem; letter-spacing: 0.04em; color: rgba(245,243,251,0.8); text-decoration: none;" style-hover="color: #ffffff;">HOME</a>
      <a href="login.php" style="font-family: 'Cinzel', serif; font-size: 0.85rem; letter-spacing: 0.04em; color: rgba(245,243,251,0.8); text-decoration: none;" style-hover="color: #ffffff;">LOGIN</a>
    </nav>
  </header>

  <main style="flex: 1; position: relative; display: flex; align-items: center; justify-content: center; overflow: hidden;">
    <img src="images/stats-card.jpg" alt="" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; z-index: 0;">
    <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(14,10,23,0.6) 0%, rgba(14,10,23,0.45) 45%, rgba(14,10,23,0.85) 100%); z-index: 1;"></div>

    <div style="position: absolute; top: 22%; left: 82%; width: 5px; height: 5px; border-radius: 50%; background: #fff; box-shadow: 0 0 8px 2px rgba(255,255,255,0.8); animation: twinkle 3.4s ease-in-out infinite; z-index: 2;"></div>
    <div style="position: absolute; top: 68%; left: 12%; width: 4px; height: 4px; border-radius: 50%; background: #fff; box-shadow: 0 0 6px 2px rgba(255,255,255,0.7); animation: twinkle 2.6s ease-in-out infinite 0.5s; z-index: 2;"></div>

    <div style="position: relative; z-index: 3; width: 100%; max-width: 400px; margin: 96px 24px 48px; padding: 40px 36px; background: rgba(12, 9, 18, 0.6); backdrop-filter: blur(14px); border: 1px dashed rgba(255,255,255,0.2); border-radius: 18px; box-shadow: 0 20px 60px rgba(0,0,0,0.5); animation: floaty 7s ease-in-out infinite;">
      <div style="display: block; width: fit-content; margin: 0 auto 14px; background: rgba(255,255,255,0.06); color: #cfc9d8; font-family: 'Cinzel', serif; font-size: 0.62rem; font-weight: 700; letter-spacing: 0.1em; padding: 5px 14px; border-radius: 999px; border: 1px solid rgba(255,255,255,0.18); transform: rotate(2deg);">&#10024; STOP LURKING &#10024;</div>
      <h1 style="font-family: 'Playfair Display', serif; font-weight: 500; font-size: 1.8rem; color: #ffffff; text-align: center; margin: 0 0 12px;">Create your account</h1>
      <p style="font-family: 'Playfair Display', serif; font-style: italic; font-size: 0.9rem; color: rgba(255,255,255,0.7); text-align: center; margin: 0 0 28px; line-height: 1.5;">So you can pretend you're productive while binge-reading.</p>

      <?php if (!empty($error)): ?>
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>
      <?php if (!empty($success)): ?>
        <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
      <?php endif; ?>

      <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" style="display: flex; flex-direction: column; gap: 16px;">
        <div>
          <label style="display: block; font-family: 'Cinzel', serif; font-size: 0.7rem; letter-spacing: 0.06em; color: rgba(255,255,255,0.8); margin-bottom: 6px;">USERNAME</label>
          <input type="text" name="username" placeholder="pick a username" required style="width: 100%; box-sizing: border-box; padding: 12px 14px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.08); color: #fff; font-size: 0.9rem;" style-focus="outline: none; border-color: #a29bfe; background: rgba(255,255,255,0.14);">
        </div>
        <div>
          <label style="display: block; font-family: 'Cinzel', serif; font-size: 0.7rem; letter-spacing: 0.06em; color: rgba(255,255,255,0.8); margin-bottom: 6px;">EMAIL</label>
          <input type="email" name="email" placeholder="you@example.com" required style="width: 100%; box-sizing: border-box; padding: 12px 14px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.08); color: #fff; font-size: 0.9rem;" style-focus="outline: none; border-color: #a29bfe; background: rgba(255,255,255,0.14);">
        </div>
        <div>
          <label style="display: block; font-family: 'Cinzel', serif; font-size: 0.7rem; letter-spacing: 0.06em; color: rgba(255,255,255,0.8); margin-bottom: 6px;">PASSWORD</label>
          <input type="password" name="password" placeholder="8+ characters" required style="width: 100%; box-sizing: border-box; padding: 12px 14px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.08); color: #fff; font-size: 0.9rem;" style-focus="outline: none; border-color: #a29bfe; background: rgba(255,255,255,0.14);">
        </div>
        <div>
          <label style="display: block; font-family: 'Cinzel', serif; font-size: 0.7rem; letter-spacing: 0.06em; color: rgba(255,255,255,0.8); margin-bottom: 6px;">CONFIRM PASSWORD</label>
          <input type="password" name="confirm_password" placeholder="••••••••" required style="width: 100%; box-sizing: border-box; padding: 12px 14px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.08); color: #fff; font-size: 0.9rem;" style-focus="outline: none; border-color: #a29bfe; background: rgba(255,255,255,0.14);">
        </div>
        <button type="submit" style="margin-top: 6px; text-align: center; text-decoration: none; font-family: 'Cinzel', serif; font-size: 0.85rem; letter-spacing: 0.04em; color: #17111f; background: linear-gradient(135deg, #a29bfe, #8a2be2); border: none; padding: 14px; border-radius: 999px; font-weight: 600; cursor: pointer; box-shadow: 0 8px 24px rgba(138,43,226,0.4);" style-hover="box-shadow: 0 10px 30px rgba(138,43,226,0.55);">CREATE ACCOUNT</button>
      </form>

      <p style="text-align: center; margin: 22px 0 0; padding-top: 18px; border-top: 1px solid rgba(255,255,255,0.14); font-size: 0.82rem; color: rgba(255,255,255,0.65);">Already have an account? <a href="login.php" style="color: #c9bffc; font-weight: 600; text-decoration: none;" style-hover="color: #fff;">Login here</a></p>
    </div>
  </main>

  <footer style="position: relative; z-index: 3; background: rgba(14,10,23,0.9); border-top: 1px solid rgba(255,255,255,0.08); padding: 22px 32px; text-align: center;">
    <p style="font-family: 'Cinzel', serif; font-size: 0.75rem; letter-spacing: 0.05em; color: rgba(245,243,251,0.55); margin: 0;">&copy; <?php echo date('Y'); ?> &mdash; The Obscured Index. All rights reserved.</p>
  </footer>
</div>

</x-dc>
</body>
</html>
