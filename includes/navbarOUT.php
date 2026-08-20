<style>
@import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700&display=swap');
/* Header Styling */
header {
    background-color: rgba(13, 13, 13, 0.2);
    backdrop-filter: blur(8px);
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
    color: var(--nav-text-color, #6c5ce7);
    font-weight: 600;
    font-size: 1rem;
    transition: color 0.3s;
    position: relative;
    font-family: 'Cinzel', serif;
}

#navbar ul li a:hover {
    color: var(--nav-hover-color, #a29bfe);
}

#navbar ul li a::after {
    content: '';
    position: absolute;
    width: 0;
    height: 2px;
    background-color: var(--nav-text-color, #6c5ce7);
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
    background-color: var(--nav-text-color, #6c5ce7);
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
        background-color: rgba(13, 13, 13, 0.2);
        backdrop-filter: blur(8px);
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
            <img src="images/logo3.png" alt="The Obscured Index Logo">
        </a>
    </div>
    
    <input type="checkbox" id="menu-toggle" class="menu-toggle">
    <label for="menu-toggle" class="menu-icon"><span></span></label>
    
    <nav id="navbar">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="login.php">Login</a></li>
        </ul>
    </nav>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function isBackgroundDark(element) {
        const bgColor = window.getComputedStyle(element).backgroundColor;
        const rgb = bgColor.match(/\d+/g);
        
        if (!rgb || rgb.length < 3) return false;
        
        const brightness = (parseInt(rgb[0]) * 299 + parseInt(rgb[1]) * 587 + parseInt(rgb[2]) * 114) / 1000;
        return brightness < 128; 
    }
    
    function setNavColors() {
        const header = document.querySelector('header');
        const isDark = isBackgroundDark(header);
        
        if (isDark) {
            document.documentElement.style.setProperty('--nav-text-color', '#f5f6fa');
            document.documentElement.style.setProperty('--nav-hover-color', '#ffffff');
        } else {
            document.documentElement.style.setProperty('--nav-text-color', '#6c5ce7');
            document.documentElement.style.setProperty('--nav-hover-color', '#a29bfe');
        }
    }
    
    setNavColors();
    
    window.addEventListener('scroll', setNavColors);
});
</script>