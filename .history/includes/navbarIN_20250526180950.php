<style>
/* Header Styling */
header {
    background-color: white;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    position: sticky;
    top: 0;
    z-index: 100;
    padding: 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    height: 80px;
    overflow: hidden;
}

#logo {
    padding: 0 20px;
}

#logo a {
    display: flex;
    align-items: center;
    text-decoration: none;
    transition: all 0.3s ease;
}

#logo img {
    height: 120px;
    margin-right: 15px;
    filter: drop-shadow(0 0 5px rgba(108, 92, 231, 0.4));
    transition: all 0.3s ease;
    margin-top: -20px;
    margin-bottom: -20px;
}

#logo a:hover img {
    filter: drop-shadow(0 0 8px rgba(108, 92, 231, 0.7));
    transform: scale(1.05);
}

#navbar ul {
    display: flex;
    list-style: none;
    padding-right: 20px;
}

#navbar ul li {
    margin-left: 20px;
}

#navbar ul li a {
    text-decoration: none;
    color: #6c5ce7;
    font-weight: 600;
    font-size: 1rem;
    transition: color 0.3s;
    position: relative;
}

#navbar ul li a:hover {
    color: #a29bfe;
}

#navbar ul li a::after {
    content: '';
    position: absolute;
    width: 0;
    height: 2px;
    background-color: #6c5ce7;
    bottom: -3px;
    left: 0;
    transition: width 0.3s;
}

#navbar ul li a:hover::after {
    width: 100%;
}

/* Menu toggle for mobile */
.menu-toggle {
    display: none;
}

.menu-icon {
    display: none;
    cursor: pointer;
    padding: 20px;
    position: relative;
    z-index: 2;
}

.menu-icon span, 
.menu-icon span::before, 
.menu-icon span::after {
    display: block;
    position: absolute;
    width: 25px;
    height: 3px;
    background-color: #6c5ce7;
    transition: all 0.3s;
}

.menu-icon span::before {
    content: '';
    top: -8px;
}

.menu-icon span::after {
    content: '';
    top: 8px;
}

/* Responsive Design */
@media (max-width: 992px) {
    #logo img {
        height: 100px;
        margin-top: -10px;
        margin-bottom: -10px;
    }
}

@media (max-width: 768px) {
    header {
        height: auto;
        min-height: 60px;
        flex-wrap: wrap;
        padding: 0;
    }
    
    #logo {
        padding: 10px 20px;
    }
    
    #logo img {
        height: 80px;
        margin-top: 0;
        margin-bottom: 0;
    }
    
    .menu-icon {
        display: inline-block;
        position: absolute;
        right: 10px;
        top: 15px;
    }
    
    #navbar {
        width: 100%;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
    }
    
    .menu-toggle:checked ~ #navbar {
        max-height: 200px;
    }
    
    #navbar ul {
        flex-direction: column;
        padding: 0;
        width: 100%;
        background-color: white;
    }
    
    #navbar ul li {
        margin: 0;
        text-align: center;
        padding: 10px 0;
        border-top: 1px solid #f0f0f0;
    }
}

@media (max-width: 480px) {
    #logo img {
        height: 80px;
    }
}
</style>
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