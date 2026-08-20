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
    overflow: visible;
}

#logo {
    padding: 0 20px;
    display: flex;
    align-items: center;
    height: 100%;
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
    max-width: 100%;
    object-fit: contain;
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

/* Mobile Bottom Navigation */
.mobile-nav {
    display: none;
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    background-color: white;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
    z-index: 100;
}

.mobile-nav ul {
    display: flex;
    justify-content: space-around;
    list-style: none;
    padding: 0;
    margin: 0;
}

.mobile-nav ul li {
    flex: 1;
    text-align: center;
}

.mobile-nav ul li a {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 10px 0;
    color: #6c5ce7;
    text-decoration: none;
    font-size: 0.7rem;
}

.mobile-nav ul li a i {
    font-size: 1.2rem;
    margin-bottom: 5px;
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
        height: 60px;
        padding: 0;
    }
    
    #logo {
        padding: 5px 20px;
    }
    
    #logo img {
        height: 70px;
        margin-top: -5px;
        margin-bottom: -5px;
    }
    
    #navbar {
        display: none;
    }
    
    .mobile-nav {
        display: block;
    }
    
    body {
        padding-bottom: 60px;
    }
}

@media (max-width: 480px) {
    header {
        height: 50px;
    }
    
    #logo img {
        height: 60px;
    }
}
</style>
<header>
    <div id="logo">
        <a href="../index.php">
            <img src="../images/logo.png" alt="The Obscured Index Logo">
        </a>
    </div>
    
    <nav id="navbar">
        <ul>
            <li><a href="home.php">Home</a></li>
            <li><a href="library.php">Library</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>
</header>

<!-- Mobile Bottom Navigation -->
<nav class="mobile-nav">
    <ul>
        <li><a href="home.php"><i class="fas fa-home"></i>Home</a></li>
        <li><a href="library.php"><i class="fas fa-book"></i>Library</a></li>
        <li><a href="add_manhwa.php"><i class="fas fa-plus-circle"></i>Add</a></li>
        <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a></li>
    </ul>
</nav>