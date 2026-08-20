<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Obscured Index</title>
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
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .container {
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 0;
        }
        
        /* Header Styling */
        header {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 9999;
            padding: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 80px;
            overflow: hidden;
            width: 100%;
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
        
        /* Ensure navbar is visible on all screen sizes */
        #navbar {
            display: block;
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
            
            /* Only apply max-height: 0 when menu is not checked */
            .menu-toggle:not(:checked) ~ #navbar {
                max-height: 0;
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
                
        /* Main Content */
        main {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 0;
            align-items: center;
            margin-top: 80px;
            position: relative;
            z-index: 1;
            width: 100%;
        }
        
        main h1 {
            font-size: 2.5rem;
            margin: 20px 0;
            color: var(--primary-color);
            text-align: center;
        }
        
        /* Slideshow Container */
        .slideshow-container {
            flex: 1;
            width: 100%;
            max-width: 100%;
            height: calc(100vh - 90px);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        
        .mySlides {
            display: none;
            width: 100%;
            height: 100%;
        }
        
        .mySlides img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        
        .active, .dot:hover {
            background-color: var(--primary-color);
        }
        
        /* Fading animation */
        .fade {
            animation-name: fade;
            animation-duration: 1.5s;
        }
        
        @keyframes fade {
            from {opacity: .4}
            to {opacity: 1}
        }
        
        /* Footer */
        footer {
            background-color: white;
            padding: 10px 0;
            text-align: center;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
        }
        
        footer p {
            font-size: 0.9rem;
            color: var(--text-color);
        }
        
    </style>
</head>
<body>
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
                <li><a href="login.php">Login</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="slideshow-container">
            <div class="mySlides fade">
                <img src="images/index/1.jpg" alt="Manhwa Image 1">
            </div>

            <div class="mySlides fade">
                <img src="images/index/2.jpg" alt="Manhwa Image 2">
            </div>

            <div class="mySlides fade">
                <img src="images/index/3.jpg" alt="Manhwa Image 3">
            </div>
            
            <div class="mySlides fade">
                <img src="images/index/4.jpg" alt="Manhwa Image 4">
            </div>
            
            <div class="mySlides fade">
                <img src="images/index/5.jpg" alt="Manhwa Image 5">
            </div>
  
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> - My Manhwa Collection. All rights reserved.</p>
        </div>
    </footer>

    <script>
        let slideIndex = 1;
        showSlides(slideIndex);

        function plusSlides(n) {
            showSlides(slideIndex += n);
        }

        function currentSlide(n) {
            showSlides(slideIndex = n);
        }

        function showSlides(n) {
            let i;
            let slides = document.getElementsByClassName("mySlides");
            let dots = document.getElementsByClassName("dot");
            
            if (n > slides.length) {slideIndex = 1}
            if (n < 1) {slideIndex = slides.length}
            
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            
            if (dots.length > 0) {
                for (i = 0; i < dots.length; i++) {
                    dots[i].className = dots[i].className.replace(" active", "");
                }
                dots[slideIndex-1].className += " active";
            }
            
            slides[slideIndex-1].style.display = "block";
        }

        setInterval(function() {
            plusSlides(1);
        }, 5000); 
    </script>
</body>
</html>
