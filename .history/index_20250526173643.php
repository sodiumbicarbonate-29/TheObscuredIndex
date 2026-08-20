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
        /* Modern Clean Design */
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
                
        /* Main Content */
        main {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 0;
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
            height: calc(100vh - 140px);
            position: relative;
            overflow: hidden;
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
        
        /* Next & previous buttons */
        .prev, .next {
            cursor: pointer;
            position: absolute;
            top: 50%;
            width: auto;
            margin-top: -22px;
            padding: 16px;
            color: white;
            font-weight: bold;
            font-size: 18px;
            transition: 0.6s ease;
            border-radius: 0 3px 3px 0;
            user-select: none;
            background-color: rgba(0, 0, 0, 0.3);
            z-index: 10;
        }
        
        .next {
            right: 0;
            border-radius: 3px 0 0 3px;
        }
        
        .prev:hover, .next:hover {
            background-color: rgba(0, 0, 0, 0.8);
        }
        
        /* Caption text */
        .text {
            color: #f2f2f2;
            font-size: 15px;
            padding: 8px 12px;
            position: absolute;
            bottom: 8px;
            width: 100%;
            text-align: center;
            background-color: rgba(0, 0, 0, 0.5);
        }
        
        .dots-container {
            text-align: center;
            position: absolute;
            bottom: 30px;
            width: 100%;
            z-index: 10;
        }
        
        .dot {
            cursor: pointer;
            height: 15px;
            width: 15px;
            margin: 0 5px;
            background-color: #bbb;
            border-radius: 50%;
            display: inline-block;
            transition: background-color 0.6s ease;
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
    <?php include 'includes/navbar.php'; ?>

    <main>
        <div class="slideshow-container">
            <div class="mySlides fade">
                <img src="images/index/1.jpg" alt="Manhwa Image 1">
                <div class="text">Featured Manhwa 1</div>
            </div>

            <div class="mySlides fade">
                <img src="images/index/2.jpg" alt="Manhwa Image 2">
                <div class="text">Featured Manhwa 2</div>
            </div>

            <div class="mySlides fade">
                <img src="images/index/3.jpg" alt="Manhwa Image 3">
                <div class="text">Featured Manhwa 3</div>
            </div>
            
            <div class="mySlides fade">
                <img src="images/index/4.jpg" alt="Manhwa Image 4">
                <div class="text">Featured Manhwa 4</div>
            </div>
            
            <div class="mySlides fade">
                <img src="images/index/5.jpg" alt="Manhwa Image 5">
                <div class="text">Featured Manhwa 5</div>
            </div>

            <!-- Next and previous buttons -->
            <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
            <a class="next" onclick="plusSlides(1)">&#10095;</a>
            
            <!-- The dots/circles -->
            <div class="dots-container">
                <span class="dot" onclick="currentSlide(1)"></span>
                <span class="dot" onclick="currentSlide(2)"></span>
                <span class="dot" onclick="currentSlide(3)"></span>
                <span class="dot" onclick="currentSlide(4)"></span>
                <span class="dot" onclick="currentSlide(5)"></span>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> - My Manhwa Collection. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Slideshow JavaScript
        let slideIndex = 1;
        showSlides(slideIndex);

        // Next/previous controls
        function plusSlides(n) {
            showSlides(slideIndex += n);
        }

        // Thumbnail image controls
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
            
            for (i = 0; i < dots.length; i++) {
                dots[i].className = dots[i].className.replace(" active", "");
            }
            
            slides[slideIndex-1].style.display = "block";
            dots[slideIndex-1].className += " active";
        }

        // Auto slideshow
        setInterval(function() {
            plusSlides(1);
        }, 5000); // Change image every 5 seconds
    </script>
</body>
</html>
</html>