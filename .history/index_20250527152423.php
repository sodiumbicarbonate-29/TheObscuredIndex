\<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="manhwa, manga, webtoon, collection, tracker, reading list">
    <title>The Obscured Index - Manhwa Collection Tracker</title>
    <link rel="canonical" href="https://theobscuredindex.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #6c5ce7;
            --secondary-color: #a29bfe;
            --text-color: #2d3436;
            --light-color: #f5f6fa;
            --accent-color:rgb(186, 185, 185);
            --magic-color-1: #8a2be2; /* Purple */
            --magic-color-2: #ff69b4; /* Pink */
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
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            overflow-y: auto;
        }
        
        /* Magic sparkles */
        .magic-sparkle {
            position: absolute;
            pointer-events: none;
            z-index: 1000;
            opacity: 0;
            animation: sparkle 2s ease-in-out forwards;
            background: transparent;
            transform-origin: center;
        }
        
        .magic-sparkle::before, .magic-sparkle::after {
            content: '';
            position: absolute;
            background: linear-gradient(135deg, #ff69b4, #8a2be2);
            border-radius: 2px;
        }
        
        .magic-sparkle::before {
            width: 100%;
            height: 20%;
            left: 0;
            top: 40%;
        }
        
        .magic-sparkle::after {
            width: 20%;
            height: 100%;
            left: 40%;
            top: 0;
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
            height: calc(100vh - 130px - 50px);
            position: relative;
            overflow: hidden;
            z-index: 1;
            min-height: 400px;
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
        
        /* Magic animations */
        @keyframes sparkle {
            0% { transform: scale(0); opacity: 0; }
            50% { opacity: 0.8; }
            100% { transform: scale(1); opacity: 0; }
        }
        
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
        
        @keyframes glow {
            0% { filter: drop-shadow(0 0 5px rgba(108, 92, 231, 0.4)); }
            50% { filter: drop-shadow(0 0 15px rgba(138, 43, 226, 0.8)); }
            100% { filter: drop-shadow(0 0 5px rgba(108, 92, 231, 0.4)); }
        }
        
        .mySlides img {
            transition: transform 0.5s ease;
        }
        
        .mySlides:hover img {
            transform: scale(1.05);
        }
        
        #logo img {
            animation: glow 3s infinite;
        }
        
        /* Magical Footer */
        footer {
            background: linear-gradient(45deg, #6c5ce7, #8a2be2);
            padding: 15px 0;
            text-align: center;
            box-shadow: 0 -5px 15px rgba(138, 43, 226, 0.3);
            position: sticky;
            bottom: 0;
            overflow: hidden;
            z-index: 10;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(to right, transparent, white, transparent);
            animation: shimmer 2s infinite;
        }
        
        .footer-sparkle {
            position: absolute;
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background-color: white;
            pointer-events: none;
            opacity: 0;
            animation: footerSparkle 2s ease-in-out infinite;
        }
        
        @keyframes footerSparkle {
            0% { transform: translateY(0) scale(0); opacity: 0; }
            50% { opacity: 0.8; }
            100% { transform: translateY(-20px) scale(1); opacity: 0; }
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        footer p {
            font-size: 0.9rem;
            color: white;
            text-shadow: 0 0 5px rgba(0,0,0,0.3);
            position: relative;
            z-index: 2;
        }
        #logo img {
            animation: glow 3s infinite;
        }
        
        /* Magical Footer */
        footer {
            background: linear-gradient(45deg, #6c5ce7, #8a2be2);
            padding: 15px 0;
            text-align: center;
            box-shadow: 0 -5px 15px rgba(138, 43, 226, 0.3);
            position: relative;
            overflow: hidden;
            z-index: 10;
            margin-top: auto;
            width: 100%;
        }
        
        footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(to right, transparent, white, transparent);
            animation: shimmer 2s infinite;
        }
        
        .footer-sparkle {
            position: absolute;
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background-color: white;
            pointer-events: none;
            opacity: 0;
            animation: footerSparkle 2s ease-in-out infinite;
        }
        
        @keyframes footerSparkle {
            0% { transform: translateY(0) scale(0); opacity: 0; }
            50% { opacity: 0.8; }
            100% { transform: translateY(-20px) scale(1); opacity: 0; }
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        footer p {
            font-size: 0.9rem;
            color: white;
            text-shadow: 0 0 5px rgba(0,0,0,0.3);
            position: relative;
            z-index: 2;
        }
        
        /* Magical Footer */
        footer {
            background: linear-gradient(45deg, #6c5ce7, #8a2be2);
            padding: 15px 0;
            text-align: center;
            box-shadow: 0 -5px 15px rgba(138, 43, 226, 0.3);
            position: relative;
            overflow: hidden;
            margin-top: auto;
            z-index: 10;
        }
        
        footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(to right, transparent, white, transparent);
            animation: shimmer 2s infinite;
        }
        
        .footer-sparkle {
            position: absolute;
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background-color: white;
            pointer-events: none;
            opacity: 0;
            animation: footerSparkle 2s ease-in-out infinite;
        }
        
        @keyframes footerSparkle {
            0% { transform: translateY(0) scale(0); opacity: 0; }
            50% { opacity: 0.8; }
            100% { transform: translateY(-20px) scale(1); opacity: 0; }
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        footer p {
            font-size: 0.9rem;
            color: white;
            text-shadow: 0 0 5px rgba(0,0,0,0.3);
            position: relative;
            z-index: 2;
        }
        
        /* Magical Footer */
        footer {
            background: linear-gradient(135deg, #6c5ce7, #8a2be2, #ff69b4);
            padding: 10px 0;
            text-align: center;
            box-shadow: 0 -5px 15px rgba(138, 43, 226, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(to right, transparent, white, transparent);
            animation: shimmer 2s infinite;
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        footer p {
            font-size: 0.9rem;
            color: white;
            text-shadow: 0 0 5px rgba(0,0,0,0.3);
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
                <img src="images/index/1.jpg" alt="Featured Manhwa Collection">
            </div>

            <div class="mySlides fade">
                <img src="images/index/6.jpg" alt="Featured Manhwa Collection">
            </div>
            
            <div class="mySlides fade">
                <img src="images/index/7.jpg" alt="Featured Manhwa Collection">
            </div>

            <div class="mySlides fade">
                <img src="images/index/8.jpg" alt="Featured Manhwa Collection">
            </div>

            <div class="mySlides fade">
                <img src="images/index/9.jpg" alt="Featured Manhwa Collection">
            </div>

            <div class="mySlides fade">
                <img src="images/index/9.jpg" alt="Featured Manhwa Collection">
            </div>
    
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> - My Manhwa Collection. All rights reserved.</p>
        </div>
        <div id="footer-sparkles"></div>
    </footer>

    <script>
        document.addEventListener('mousemove', function(e) {
            if (Math.random() > 0.9) { 
                createSparkle(e.pageX, e.pageY);
            }
        });
        
        function createSparkle(x, y) {
            const sparkle = document.createElement('div');
            sparkle.className = 'magic-sparkle';
            
            const size = Math.random() * 12 + 8;
            sparkle.style.width = `${size}px`;
            sparkle.style.height = `${size}px`;
            
            const rotation = Math.random() * 360;
            sparkle.style.transform = `rotate(${rotation}deg)`;
            
            sparkle.style.left = `${x - size/2}px`;
            sparkle.style.top = `${y - size/2}px`;
            
            document.body.appendChild(sparkle);
            
            setTimeout(() => {
                sparkle.remove();
            }, 2000);
        }
        
        const slides = document.querySelectorAll('.mySlides');
        slides.forEach((slide, index) => {
            slide.style.animation = `float ${3 + index}s infinite ease-in-out`;
        });
        
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
        
        function createFooterSparkles() {
            const footer = document.getElementById('footer-sparkles');
            const footerWidth = footer.parentElement.offsetWidth;
            
            for (let i = 0; i < 20; i++) {
                setTimeout(() => {
                    const sparkle = document.createElement('div');
                    sparkle.className = 'footer-sparkle';
                    
                    const left = Math.random() * footerWidth;
                    sparkle.style.left = `${left}px`;
                    sparkle.style.bottom = '5px';
                    
                    const delay = Math.random() * 2;
                    sparkle.style.animationDelay = `${delay}s`;
                    
                    footer.appendChild(sparkle);
                    
                    setTimeout(() => {
                        sparkle.remove();
                        createFooterSparkle(footer, footerWidth);
                    }, 2000 + delay * 1000);
                }, i * 100);
            }
        }
        
        function createFooterSparkle(footer, footerWidth) {
            const sparkle = document.createElement('div');
            sparkle.className = 'footer-sparkle';
            
            const left = Math.random() * footerWidth;
            sparkle.style.left = `${left}px`;
            sparkle.style.bottom = '5px';
            
            const delay = Math.random() * 2;
            sparkle.style.animationDelay = `${delay}s`;
            
            footer.appendChild(sparkle);
            
            setTimeout(() => {
                sparkle.remove();
                createFooterSparkle(footer, footerWidth);
            }, 2000 + delay * 1000);
        }
        
        window.addEventListener('load', createFooterSparkles);
    </script>
</body>
</html>
