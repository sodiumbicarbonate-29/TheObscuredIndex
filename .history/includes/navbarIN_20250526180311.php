<header>
    <div id="logo">
        <a href="index.php">
            <img src="images/logo.png" alt="The Obscured Index Logo">
        </a>
    </div>
    
    <input type="checkbox" id="menu-toggle" class="menu-toggle">
    <label for="menu-toggle" class="menu-icon"><span></span></label>
    
    <nav id="navbar">
        <ul>
            <li><a href="index.php">Home</a></li>
            <?php if(isset($_SESSION['user_id'])): ?>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>