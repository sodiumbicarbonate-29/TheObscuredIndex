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
    height: 60px;
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
    height: 180px;
    margin-right: 15px;
    filter: drop-shadow(0 0 5px rgba(108, 92, 231, 0.4));
    transition: all 0.3s ease;
    margin-top: -60px;
    margin-bottom: -60px;
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
    color: var(--text-color);
    font-weight: 600;
    font-size: 1rem;
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
    bottom: -3px;
    left: 0;
    transition: width 0.3s;
}

#navbar ul li a:hover::after {
    width: 100%;
}
</style>
<header>
    <div id="logo">
        <a href="index.php">
            <img src="images/logo.png" alt="The Obscured Index Logo">
        </a>
    </div>
    
    <nav id="navbar">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="login.php">Login</a></li>
        </ul>
    </nav>
</header>