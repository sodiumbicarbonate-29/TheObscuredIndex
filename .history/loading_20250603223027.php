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
            --crystal-color: #9b59b6;
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
            background-image: 
                radial-gradient(circle at 20% 20%, rgba(108, 92, 231, 0.1) 0%, rgba(108, 92, 231, 0) 25%),
                radial-gradient(circle at 80% 80%, rgba(253, 121, 168, 0.1) 0%, rgba(253, 121, 168, 0) 25%);
        }
        
        .fortune-container {
            text-align: center;
            position: relative;
            z-index: 1;
        }
        
        .mirror-container {
            width: 300px;
            height: 350px;
            margin: 0 auto 50px;
            position: relative;
            transform-style: preserve-3d;
        }
        
        .mirror-frame {
            width: 100%;
            height: 100%;
            position: relative;
            border-radius: 50%;
            box-shadow: 0 0 40px rgba(108, 92, 231, 0.6);
            transform: perspective(800px) rotateX(10deg);
            animation: mirror-float 3s ease-in-out infinite alternate;
            overflow: hidden;
        }
        
        @keyframes mirror-float {
            0% { transform: perspective(800px) rotateX(10deg) translateY(0); }
            100% { transform: perspective(800px) rotateX(10deg) translateY(-5px); }
        }
        
        .mirror-surface {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, 
                rgba(255, 255, 255, 0.1), 
                rgba(108, 92, 231, 0.2), 
                rgba(255, 255, 255, 0.1));
            backdrop-filter: blur(2px);
        }
        
        .mirror-reflection {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, 
                transparent 30%, 
                rgba(255, 255, 255, 0.1) 50%, 
                transparent 70%);
            animation: reflection-move 5s linear infinite;
        }
        
        @keyframes reflection-move {
            0% { background-position: -100% 0; }
            100% { background-position: 200% 0; }
        }
        
        @keyframes mirror-glow {
            0% { box-shadow: inset 0 0 30px rgba(255, 255, 255, 0.3), 0 0 40px rgba(108, 92, 231, 0.6); }
            100% { box-shadow: inset 0 0 50px rgba(255, 255, 255, 0.4), 0 0 60px rgba(108, 92, 231, 0.8); }
        }
        
        .mirror-message {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80%;
            text-align: center;
            font-size: 1.2rem;
            font-weight: 500;
            color: white;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.9), 0 0 20px rgba(108, 92, 231, 0.9);
            opacity: 0;
            z-index: 5;
            animation: mirrorMessageFloat 3s infinite;
            font-style: italic;
            letter-spacing: 1px;
        }
        
        @keyframes mirrorMessageFloat {
            0%, 100% { opacity: 0; transform: translate(-50%, -50%) scale(0.9); }
            20%, 80% { opacity: 1; transform: translate(-50%, -50%) scale(1); }
        }
        
        @keyframes fadeInOut {
            0%, 100% { opacity: 0; }
            20%, 80% { opacity: 1; }
        }
        
        .fortune-title {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: white;
            text-shadow: 0 0 10px rgba(108, 92, 231, 0.8);
            font-family: 'Times New Roman', serif;
        }
        
        .narrator-text {
            font-size: 1.5rem;
            color: #fd79a8;
            margin-bottom: 2rem;
            font-style: italic;
            text-shadow: 0 0 10px rgba(253, 121, 168, 0.5);
        }
        
        .loading-progress {
            width: 300px;
            height: 6px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            margin: 0 auto;
            overflow: hidden;
        }
        
        .progress-bar {
            height: 100%;
            width: 0;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
            border-radius: 10px;
            transition: width 10s ease;
        }
        
        .stars {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }
        
        .star {
            position: absolute;
            width: 3px;
            height: 3px;
            background-color: white;
            border-radius: 50%;
            opacity: 0;
            animation: twinkle 2s infinite;
        }
        
        @keyframes twinkle {
            0%, 100% { opacity: 0; transform: scale(0.5); }
            50% { opacity: 1; transform: scale(1.2); }
        }
        
        .skip-button {
            position: absolute;
            bottom: 20px;
            right: 20px;
            background: rgba(0, 0, 0, 0.5);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            padding: 8px 15px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 100;
            backdrop-filter: blur(5px);
        }
        
        .skip-button:hover {
            background: rgba(108, 92, 231, 0.7);
            transform: translateY(-2px);
        }
        
        .skip-button i {
            margin-left: 5px;
        }
        
        .book-icon {
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 19rem;
            opacity: 0.1;
            line-height: 1;
            z-index: 0;
        }
        
        .magic-particles {
            position: absolute;
            width: 5px;
            height: 5px;
            background-color: rgba(108, 92, 231, 0.8);
            border-radius: 50%;
            pointer-events: none;
            opacity: 0;
            animation: float-up 3s ease-out infinite;
        }
        
        @keyframes float-up {
            0% { transform: translateY(0) scale(0); opacity: 0; }
            20% { opacity: 1; }
            100% { transform: translateY(-100px) scale(0.5) rotate(360deg); opacity: 0; }
        }
    </style>
</head>
<body>
    <button id="skip-button" class="skip-button">Skip <i class="fas fa-forward"></i></button>
    
    <!-- Hidden audio element for narration -->
    <audio id="narrator-voice" preload="auto">
        <source src="audio/narrator.mp3" type="audio/mpeg">
    </audio>
    
    <div class="fortune-container">
        <div class="mirror-container">
            <div class="mirror-frame">
                <div class="mirror-surface"></div>
                <div id="floating-message" class="mirror-message"></div>
            </div>
            <div class="mirror-glow"></div>
        </div>
        
        <h1 class="fortune-title">The Obscured Index</h1>
        <p class="narrator-text">"Once upon a time… in the depths of obsession…"</p>
        
        <div class="loading-progress">
            <div id="progress-bar" class="progress-bar"></div>
        </div>
        
        <div class="book-icon">🪞</div>
    </div>
    
    <div class="stars" id="stars"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Create stars
            const starsContainer = document.getElementById('stars');
            for (let i = 0; i < 50; i++) {
                const star = document.createElement('div');
                star.className = 'star';
                
                const x = Math.random() * 100;
                const y = Math.random() * 100;
                star.style.left = `${x}%`;
                star.style.top = `${y}%`;
                
                const delay = Math.random() * 2;
                star.style.animationDelay = `${delay}s`;
                
                starsContainer.appendChild(star);
            }
            
            const narratorAudio = document.getElementById('narrator-voice');
            narratorAudio.volume = 0.7;
            narratorAudio.play().catch(e => console.log('Audio play failed:', e));
            
            setTimeout(() => {
                document.getElementById('progress-bar').style.width = '100%';
            }, 500);
            
            const messages = <?php echo json_encode($loadingMessages); ?>;
            const floatingMessage = document.getElementById('floating-message');
            let messageIndex = 0;
            
            function showNextMessage() {
                floatingMessage.textContent = messages[messageIndex];
                floatingMessage.style.opacity = "1";
                messageIndex = (messageIndex + 1) % messages.length;
                
                // Create ripple effect in the mirror
                const ripple = document.createElement('div');
                ripple.style.position = 'absolute';
                ripple.style.width = '10px';
                ripple.style.height = '10px';
                ripple.style.borderRadius = '50%';
                ripple.style.backgroundColor = 'rgba(255, 255, 255, 0.3)';
                ripple.style.boxShadow = '0 0 10px rgba(255, 255, 255, 0.5)';
                ripple.style.top = '50%';
                ripple.style.left = '50%';
                ripple.style.transform = 'translate(-50%, -50%)';
                ripple.style.opacity = '0';
                ripple.style.zIndex = '4';
                
                document.querySelector('.mirror-frame').appendChild(ripple);
                
                setTimeout(() => {
                    ripple.style.transition = 'all 2s ease-out';
                    ripple.style.width = '200px';
                    ripple.style.height = '200px';
                    ripple.style.opacity = '0.5';
                }, 10);
                
                setTimeout(() => {
                    ripple.style.opacity = '0';
                    setTimeout(() => ripple.remove(), 500);
                }, 1500);
            }
            
            showNextMessage();
            setInterval(showNextMessage, 3000);
            
            let narrationComplete = false;
            let skipRequested = false;
            
            narratorAudio.addEventListener('ended', function() {
                narrationComplete = true;
                if (skipRequested) {
                    window.location.href = '<?php echo $redirect; ?>';
                }
            });
            
            document.getElementById('skip-button').addEventListener('click', function() {
                skipRequested = true;
                if (narrationComplete) {
                    window.location.href = '<?php echo $redirect; ?>';
                } else {
                    this.textContent = "Skipping...";
                    this.disabled = true;
                    narratorAudio.pause();
                    narrationComplete = true;
                    setTimeout(() => {
                        window.location.href = '<?php echo $redirect; ?>';
                    }, 1000);
                }
            });
            
            setTimeout(() => {
                window.location.href = '<?php echo $redirect; ?>';
            }, 15000);
        });
    </script>
</body>
</html>