<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="manhwa, manga, webtoon, collection, tracker, reading list">
    <title>The Obscured Index - Manhwa Collection Tracker</title>
    <link rel="canonical" href="https://theobscuredindex.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&display=swap">
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
            background-size: cover;
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
            background-color: rgba(255, 255, 255, 0.2);
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
            overflow: visible;
            width: 100%;
            backdrop-filter: blur(10px);
            opacity: 0.8;
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
                background-color: rgba(255, 255, 255, 0.3);
                backdrop-filter: blur(10px);
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
            margin-top: 0;
            position: relative;
            z-index: 1;
            width: 100%;
        }
        
        .banner {
            text-align: center;
            padding: 40px 20px;
            width: 80%;
            max-width: 1200px;
            background-color: rgba(13, 13, 13, 0.2);
            backdrop-filter: blur(10px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin: 200px auto 20px;
            opacity: 0.8;
            position: relative;
            z-index: 5;
            overflow: hidden;
        }
        
        .banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(to right, transparent, white, transparent);
            animation: shimmer 2s infinite;
        }
        
        .banner::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(to right, transparent, white, transparent);
            animation: shimmer 2s infinite;
            animation-delay: 1s;
        }
        
        .banner h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            margin: 0 0 15px 0;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .banner p {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            color: white;
            margin: 0;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
        }
        
        /* Catchphrase carousel */
        .catchphrase-container {
            height: 1.8em;
            overflow: hidden;
            position: relative;
            margin-top: 20px;
        }
        
        .catchphrase {
            position: absolute;
            width: 100%;
            text-align: center;
            opacity: 0;
            font-family: 'Playfair Display', serif;
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.9);
            font-style: italic;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
        }
        
        @keyframes fadeInOut {
            0% { opacity: 0; transform: translateY(10px); }
            10% { opacity: 1; transform: translateY(0); }
            90% { opacity: 1; transform: translateY(0); }
            100% { opacity: 0; transform: translateY(-10px); }
        }
        
        .catchphrase.active {
            animation: fadeInOut 5s ease-in-out forwards;
        }
        
        @media (max-width: 768px) {
            .banner h1 {
                font-size: 2.5rem;
            }
            
            .banner p {
                font-size: 1.2rem;
            }
        }
        
        @media (max-width: 480px) {
            .banner h1 {
                font-size: 2rem;
            }
            
            .banner p {
                font-size: 1rem;
            }
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
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            overflow: hidden;
            z-index: 0;
        }
        
        .mySlides {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1.5s ease-in-out;
            pointer-events: none;
        }
        
        .mySlides.active {
            opacity: 1;
            z-index: 1;
            pointer-events: auto;
        }
        
        .mySlides img {
            width: 100vw;
            height: 100vh;
            object-fit: cover;
            object-position: center;
            display: block;
        }
        
        .active, .dot:hover {
            background-color: var(--primary-color);
        }
        
        /* Magic animations */
        @keyframes sparkle {
            0% { transform: scale(0); opacity: 0; }
            50% { opacity: 0.8; }
            100% { transform: scale(1); opacity: 0; }
        }
        
        /* Removed float animation */
        
        @keyframes glow {
            0% { filter: drop-shadow(0 0 5px rgba(108, 92, 231, 0.4)); }
            50% { filter: drop-shadow(0 0 15px rgba(138, 43, 226, 0.8)); }
            100% { filter: drop-shadow(0 0 5px rgba(108, 92, 231, 0.4)); }
        }
        
        .mySlides img {
            /* Removed transition and transform effects */
        }
        
        #logo img {
            animation: glow 3s infinite;
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
        
        /* Form Styling */
        .form-container {
            max-width: 400px;
            width: 100%;
            margin: 0 auto;
            background-color: rgba(255, 255, 255, 0.85);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(108, 92, 231, 0.3), 
                        0 0 20px rgba(108, 92, 231, 0.2),
                        0 0 40px rgba(108, 92, 231, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transform: perspective(1000px) rotateX(0deg);
            transition: all 0.5s ease;
        }
        
        @media (max-width: 768px) {
            .form-container {
                max-width: 90%;
                padding: 25px;
                transform: none;
            }
            
            .form-title {
                font-size: 1.8rem;
                margin-bottom: 20px;
            }
        }
        
        @media (max-width: 480px) {
            .form-container {
                max-width: 90%;
                padding: 12px;
                transform: none;
            }
            
            .form-title {
                font-size: 1.3rem;
                margin-bottom: 12px;
            }
            
            .form-title::after {
                width: 40px;
                height: 2px;
                bottom: -8px;
            }
            
            .form-group {
                margin-bottom: 10px;
            }
            
            .form-group label {
                margin-bottom: 3px;
                font-size: 0.75rem;
            }
            
            .form-group input {
                padding: 6px 10px;
                font-size: 0.75rem;
                border-width: 1px;
            }
            
            .btn {
                padding: 8px;
                font-size: 0.8rem;
            }
            
            .register-link {
                margin-top: 12px;
                padding-top: 8px;
                font-size: 0.75rem;
            }
            
            .register-link::before {
                font-size: 0.8rem;
                top: -8px;
            }
        }
        
        .form-container:hover {
            box-shadow: 0 15px 35px rgba(108, 92, 231, 0.4), 
                        0 0 25px rgba(108, 92, 231, 0.3),
                        0 0 50px rgba(108, 92, 231, 0.2);
        }
        
        @media (hover: none) {
            .form-container:hover {
                transform: none;
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
            box-shadow: 0 6px 15px rgba(108, 92, 231, 0.4);
        }
        
        .btn:hover::before {
            left: 100%;
        }
        
        .btn:active {
            /* Removed transform effect */
        }
        
        .register-link {
            text-align: center;
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid rgba(108, 92, 231, 0.2);
            position: relative;
        }
        
        .register-link::before {
            content: '✨';
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            background-color: rgba(255, 255, 255, 0.9);
            padding: 0 10px;
            color: var(--accent-color);
        }
        
        .register-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            position: relative;
            padding: 3px 0;
            transition: all 0.3s;
        }
        
        .register-link a::after {
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
        
        .register-link a:hover {
            text-decoration: none;
            color: var(--accent-color);
        }
        
        .register-link a:hover::after {
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
        
        /* Footer Sparkle Animation */
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
        <div class="banner">
            <h1>Welcome to The Obscured Index</h1>
            <p><em>Where lost scrolls of manhwa await… if you’re brave (or bored) enough to enter.</em></p>
        </div>
        <div class="slideshow-container">
            <div class="mySlides">
                <img src="images/index/13.jpg" alt="Featured Manhwa Collection">
            </div>
            <div class="mySlides">
                <img src="images/index/15.jpg" alt="Featured Manhwa Collection">
            </div>
            <div class="mySlides">
                <img src="images/index/17.jpg" alt="Featured Manhwa Collection">
            </div>
            <div class="mySlides">
                <img src="images/index/1.jpg" alt="Featured Manhwa Collection">
            </div>
            <div class="mySlides">
                <img src="images/index/11.jpg" alt="Featured Manhwa Collection">
            </div>
            <div class="mySlides">
                <img src="images/index/12.jpg" alt="Featured Manhwa Collection">
            </div>
            <div class="mySlides">
                <img src="images/index/7.jpg" alt="Featured Manhwa Collection">
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
        
        function createBannerSparkles() {
            const banner = document.querySelector('.banner');
            const bannerWidth = banner.offsetWidth;
            
            for (let i = 0; i < 20; i++) {
                setTimeout(() => {
                    const sparkle = document.createElement('div');
                    sparkle.className = 'footer-sparkle';
                    
                    const left = Math.random() * bannerWidth;
                    sparkle.style.left = `${left}px`;
                    sparkle.style.bottom = '5px';
                    
                    const delay = Math.random() * 2;
                    sparkle.style.animationDelay = `${delay}s`;
                    
                    banner.appendChild(sparkle);
                    
                    setTimeout(() => {
                        sparkle.remove();
                        createBannerSparkle(banner, bannerWidth);
                    }, 2000 + delay * 1000);
                }, i * 100);
            }
        }
        
        function createBannerSparkle(banner, bannerWidth) {
            const sparkle = document.createElement('div');
            sparkle.className = 'footer-sparkle';
            
            const left = Math.random() * bannerWidth;
            sparkle.style.left = `${left}px`;
            sparkle.style.bottom = '5px';
            
            const delay = Math.random() * 2;
            sparkle.style.animationDelay = `${delay}s`;
            
            banner.appendChild(sparkle);
            
            setTimeout(() => {
                sparkle.remove();
                createBannerSparkle(banner, bannerWidth);
            }, 2000 + delay * 1000);
        }
        
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
        
        window.addEventListener('load', function() {
            createFooterSparkles();
            createBannerSparkles();
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
                slides[i].classList.remove("active");
            }
            
            if (dots.length > 0) {
                for (i = 0; i < dots.length; i++) {
                    dots[i].className = dots[i].className.replace(" active", "");
                }
                dots[slideIndex-1].className += " active";
            }
            
            slides[slideIndex-1].classList.add("active");
        }

        setInterval(function() {
            plusSlides(1);
        }, 5000);
    </script>
</body>
</html>
