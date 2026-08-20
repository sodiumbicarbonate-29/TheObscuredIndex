<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'IN/home.php';

$loadingMessages = [
    "Polishing villain smirks…",
    "Unpacking 999 unread chapters…",
    "Calming down overdramatic FL…",
    "Untangling toxic plotlines… again.",
    "Convincing ML to stop kidnapping people…",
    "Sacrificing sleep for your entertainment…",
    "Unclenching cliffhanger fists…",
    "Blurring the line between cringe and masterpiece…"
];

$randomMessage = $loadingMessages[array_rand($loadingMessages)];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loading - The Obscured Index</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #6c5ce7;
            --secondary-color: #a29bfe;
            --accent-color: #fd79a8;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #0f0f1a;
            color: white;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }
        
        .curtain-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 10;
        }
        
        .curtain-left, .curtain-right {
            position: absolute;
            top: 0;
            width: 50%;
            height: 100%;
            z-index: 10;
            transition: transform 2s cubic-bezier(0.86, 0, 0.07, 1);
            background-color: #8e44ad;
            background-image: 
                linear-gradient(175deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.6) 100%),
                repeating-linear-gradient(0deg, rgba(0,0,0,0.4) 0px, rgba(0,0,0,0.4) 1px, rgba(0,0,0,0) 1px, rgba(0,0,0,0) 30px),
                repeating-linear-gradient(90deg, rgba(0,0,0,0) 0px, rgba(0,0,0,0) 10px, rgba(0,0,0,0.1) 10px, rgba(0,0,0,0.1) 11px);
            box-shadow: inset 0 0 30px rgba(0,0,0,0.5);
            overflow: hidden;
        }
        
        /* Gold embroidery on curtains */
        .curtain-left::before, .curtain-right::before {
            content: '';
            position: absolute;
            top: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 20%, rgba(241, 196, 15, 0.3) 0%, rgba(241, 196, 15, 0) 25%),
                radial-gradient(circle at 80% 40%, rgba(241, 196, 15, 0.3) 0%, rgba(241, 196, 15, 0) 25%),
                radial-gradient(circle at 40% 60%, rgba(241, 196, 15, 0.3) 0%, rgba(241, 196, 15, 0) 25%),
                radial-gradient(circle at 70% 80%, rgba(241, 196, 15, 0.3) 0%, rgba(241, 196, 15, 0) 25%),
                repeating-linear-gradient(
                    to bottom,
                    rgba(255, 255, 255, 0.1) 0%,
                    rgba(255, 255, 255, 0.1) 1%,
                    transparent 1%,
                    transparent 10%
                );
        }
        
        .curtain-left {
            left: 0;
            transform: translateX(0);
            border-right: 15px solid #6c3483;
        }
        
        .curtain-right {
            right: 0;
            transform: translateX(0);
            border-left: 15px solid #6c3483;
        }
        
        .curtain-left::before, .curtain-right::before {
            content: '';
            position: absolute;
            top: 0;
            width: 100%;
            height: 100%;
            background: repeating-linear-gradient(
                to bottom,
                rgba(255, 255, 255, 0.1) 0%,
                rgba(255, 255, 255, 0.1) 1%,
                transparent 1%,
                transparent 10%
            );
        }
        
        .curtain-left::after, .curtain-right::after {
            content: '';
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 20px;
            background: #6c3483;
            box-shadow: 0 -5px 10px rgba(0,0,0,0.3);
        }
        
        .curtain-left.open {
            transform: translateX(-100%);
        }
        
        .curtain-right.open {
            transform: translateX(100%);
        }
        
        .spotlight {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,255,255,0.8) 0%, rgba(255,255,255,0) 70%);
            opacity: 0;
            transition: opacity 2s ease;
            pointer-events: none;
            z-index: 5;
        }
        
        .spotlight.visible {
            opacity: 1;
        }
        
        .loading-container {
            text-align: center;
            position: relative;
            z-index: 1;
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 1s ease, transform 1s ease;
        }
        
        .loading-container.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        .throne {
            width: 200px;
            height: 250px;
            margin: 0 auto 30px;
            position: relative;
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 1s ease 1s, transform 1s ease 1s;
        }
        
        .throne.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        .throne-base {
            width: 180px;
            height: 200px;
            background: linear-gradient(135deg, #8e44ad, #9b59b6);
            border-radius: 10px 10px 0 0;
            position: relative;
            margin: 0 auto;
            box-shadow: 0 0 20px rgba(142, 68, 173, 0.6);
            overflow: hidden;
        }
        
        .throne-base::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: repeating-linear-gradient(
                90deg,
                rgba(255, 255, 255, 0.1),
                rgba(255, 255, 255, 0.1) 10px,
                rgba(255, 255, 255, 0.2) 10px,
                rgba(255, 255, 255, 0.2) 20px
            );
        }
        
        .throne-seat {
            width: 200px;
            height: 40px;
            background: linear-gradient(135deg, #9b59b6, #8e44ad);
            border-radius: 10px;
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        
        .throne-back {
            width: 160px;
            height: 120px;
            background: linear-gradient(135deg, #8e44ad, #9b59b6);
            border-radius: 10px 10px 0 0;
            position: absolute;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%);
            box-shadow: 0 -5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .throne-book {
            width: 30px;
            height: 40px;
            background: #f1c40f;
            position: absolute;
            border-radius: 3px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        
        .throne-book:nth-child(1) { top: 20px; left: 20px; transform: rotate(-15deg); }
        .throne-book:nth-child(2) { top: 30px; left: 40px; transform: rotate(5deg); }
        .throne-book:nth-child(3) { top: 15px; left: 60px; transform: rotate(-10deg); }
        .throne-book:nth-child(4) { top: 25px; right: 50px; transform: rotate(8deg); }
        .throne-book:nth-child(5) { top: 10px; right: 30px; transform: rotate(-5deg); }
        
        .loading-title {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: white;
            text-shadow: 0 0 10px rgba(108, 92, 231, 0.8);
            font-family: 'Times New Roman', serif;
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 1s ease 2s, transform 1s ease 2s;
        }
        
        .loading-title.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        .narrator-text {
            font-size: 1.5rem;
            color: #fd79a8;
            margin-bottom: 2rem;
            font-style: italic;
            text-shadow: 0 0 10px rgba(253, 121, 168, 0.5);
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 1s ease 3s, transform 1s ease 3s;
        }
        
        .narrator-text.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        .loading-message {
            font-size: 1.2rem;
            color: #a29bfe;
            margin-bottom: 1.5rem;
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 1s ease 3.5s, transform 1s ease 3.5s;
        }
        
        .loading-message.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        .loading-progress {
            width: 300px;
            height: 6px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            margin: 0 auto;
            overflow: hidden;
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 1s ease 4s, transform 1s ease 4s;
        }
        
        .loading-progress.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        .progress-bar {
            height: 100%;
            width: 0;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
            border-radius: 10px;
            transition: width 3s ease;
        }
        
        .sparkles {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
            z-index: 20;
        }
        
        .sparkle {
            position: absolute;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: white;
            opacity: 0;
            filter: blur(0.5px);
        }
        
        @keyframes sparkle {
            0% { transform: scale(0) rotate(0deg); opacity: 0; }
            25% { transform: scale(1) rotate(90deg); opacity: 1; }
            50% { transform: scale(1.5) rotate(180deg); opacity: 0.8; }
            75% { transform: scale(1) rotate(270deg); opacity: 0.5; }
            100% { transform: scale(0) rotate(360deg); opacity: 0; }
        }
        
        /* Floating magical particles */
        .magical-particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 5;
        }
        
        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            filter: blur(1px);
            animation: float-up 15s linear infinite;
        }
        
        @keyframes float-up {
            0% {
                transform: translateY(100vh) scale(0);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-50px) scale(1);
                opacity: 0;
            }
        }
        
        /* Glowing runes around the throne */
        .magical-runes {
            position: absolute;
            width: 300px;
            height: 300px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -40%);
            border-radius: 50%;
            z-index: 2;
            opacity: 0;
            transition: opacity 2s ease 3s;
        }
        
        .magical-runes.visible {
            opacity: 1;
        }
        
        .rune {
            position: absolute;
            font-size: 1.2rem;
            color: #f1c40f;
            text-shadow: 0 0 5px #f1c40f, 0 0 10px #f1c40f;
            animation: rune-glow 3s infinite alternate;
        }
        
        @keyframes rune-glow {
            0% { text-shadow: 0 0 5px #f1c40f, 0 0 10px #f1c40f; }
            100% { text-shadow: 0 0 10px #f1c40f, 0 0 20px #f1c40f, 0 0 30px #f1c40f; }
        }
    </style>
</head>
<body>
    <div class="curtain-container">
        <div class="curtain-left"></div>
        <div class="curtain-right"></div>
    </div>
    
    <div class="spotlight"></div>
    
    <div class="magical-particles" id="magical-particles"></div>
    <div class="magical-runes" id="magical-runes"></div>
    
    <div class="loading-container">
        <div class="throne">
            <div class="throne-base">
                <div class="throne-book"></div>
                <div class="throne-book"></div>
                <div class="throne-book"></div>
                <div class="throne-book"></div>
                <div class="throne-book"></div>
                <div class="throne-back"></div>
                <div class="throne-seat"></div>
            </div>
        </div>
        
        <h1 class="loading-title">The Obscured Index</h1>
        <p class="narrator-text">"Once upon a time… in the depths of obsession…"</p>
        <p class="loading-message"><?php echo htmlspecialchars($randomMessage); ?></p>
        
        <div class="loading-progress">
            <div class="progress-bar"></div>
        </div>
        
        <div class="sparkles" id="sparkles"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                document.querySelector('.curtain-left').classList.add('open');
                document.querySelector('.curtain-right').classList.add('open');
            }, 500);
            
            setTimeout(() => {
                document.querySelector('.spotlight').classList.add('visible');
            }, 1500);
            
            setTimeout(() => {
                document.querySelector('.loading-container').classList.add('visible');
                document.querySelector('.throne').classList.add('visible');
                document.querySelector('.loading-title').classList.add('visible');
                document.querySelector('.narrator-text').classList.add('visible');
                document.querySelector('.loading-message').classList.add('visible');
                document.querySelector('.loading-progress').classList.add('visible');
            }, 2000);
            
            setTimeout(() => {
                document.querySelector('.progress-bar').style.width = '100%';
            }, 2500);
        });
        
        function createSparkles() {
            const sparklesContainer = document.getElementById('sparkles');
            const containerRect = document.body.getBoundingClientRect();
            
            for (let i = 0; i < 20; i++) {
                setTimeout(() => {
                    const sparkle = document.createElement('div');
                    sparkle.className = 'sparkle';
                    
                    const x = Math.random() * containerRect.width;
                    const y = Math.random() * containerRect.height;
                    sparkle.style.left = `${x}px`;
                    sparkle.style.top = `${y}px`;
                    
                    const colors = ['#a29bfe', '#fd79a8', '#6c5ce7', '#f1c40f', '#e84393'];
                    sparkle.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                    
                    const size = Math.random() * 4 + 2;
                    sparkle.style.width = `${size}px`;
                    sparkle.style.height = `${size}px`;
                    
                    const duration = Math.random() * 2 + 1;
                    sparkle.style.animation = `sparkle ${duration}s ease-in-out`;
                    
                    sparklesContainer.appendChild(sparkle);
                    
                    setTimeout(() => {
                        sparkle.remove();
                    }, duration * 1000);
                }, i * 100);
            }
        }
        
        setTimeout(() => {
            createSparkles();
            setInterval(createSparkles, 2000);
        }, 2000);
        
        function createMagicalParticles() {
            const particlesContainer = document.getElementById('magical-particles');
            
            for (let i = 0; i < 50; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                
                const x = Math.random() * 100;
                particle.style.left = `${x}%`;
                
                const size = Math.random() * 5 + 2;
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                
                const colors = ['#a29bfe', '#fd79a8', '#6c5ce7', '#f1c40f', '#e84393', '#ffffff'];
                particle.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                
                const duration = Math.random() * 10 + 10;
                const delay = Math.random() * 10;
                particle.style.animation = `float-up ${duration}s linear ${delay}s infinite`;
                
                particlesContainer.appendChild(particle);
            }
        }
        
        function createMagicalRunes() {
            const runesContainer = document.getElementById('magical-runes');
            const runeSymbols = ['✧', '✦', '✵', '✴', '✷', '✸', '✹', '✺', '✻', '✼', '✽', '✾', '✿', '❀', '❁', '❂', '❃', '❄', '❅', '❆', '❇', '❈', '❉', '❊', '❋'];
            
            for (let i = 0; i < 20; i++) {
                const rune = document.createElement('div');
                rune.className = 'rune';
                
                rune.textContent = runeSymbols[Math.floor(Math.random() * runeSymbols.length)];
                
                const angle = (i / 20) * Math.PI * 2; 
                const radius = 120 + Math.random() * 30; 
                const x = Math.cos(angle) * radius;
                const y = Math.sin(angle) * radius;
                
                rune.style.left = `calc(50% + ${x}px)`;
                rune.style.top = `calc(50% + ${y}px)`;
                
                const delay = Math.random() * 2;
                rune.style.animationDelay = `${delay}s`;
                
                runesContainer.appendChild(rune);
            }
        }
        
        createMagicalParticles();
        createMagicalRunes();
        
        setTimeout(() => {
            document.querySelector('.magical-runes').classList.add('visible');
        }, 3000);
        
        setTimeout(() => {
            window.location.href = '<?php echo $redirect; ?>';
        }, 10000);
    </script>
</body>
</html>