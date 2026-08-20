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
        
        .crystal-ball {
            width: 300px;
            height: 300px;
            margin: 0 auto 30px;
            position: relative;
        }
        
        .crystal-stand {
            width: 120px;
            height: 40px;
            background: linear-gradient(135deg, #4a3b5c, #2c2236);
            border-radius: 50%;
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            z-index: 1;
        }
        
        .crystal-globe {
            width: 250px;
            height: 250px;
            background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.8), rgba(155, 89, 182, 0.5) 60%, rgba(155, 89, 182, 0.3) 80%);
            border-radius: 50%;
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            box-shadow: 
                0 0 30px rgba(155, 89, 182, 0.6),
                inset 0 0 50px rgba(255, 255, 255, 0.2),
                inset 0 0 20px rgba(155, 89, 182, 0.5);
            overflow: hidden;
            animation: pulse 3s infinite alternate;
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 30px rgba(155, 89, 182, 0.6), inset 0 0 50px rgba(255, 255, 255, 0.2), inset 0 0 20px rgba(155, 89, 182, 0.5); }
            100% { box-shadow: 0 0 50px rgba(155, 89, 182, 0.8), inset 0 0 70px rgba(255, 255, 255, 0.3), inset 0 0 30px rgba(155, 89, 182, 0.7); }
        }
        
        .crystal-reflection {
            width: 80px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            position: absolute;
            top: 50px;
            left: 50px;
            transform: rotate(-45deg);
            filter: blur(5px);
        }
        
        .crystal-message {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80%;
            text-align: center;
            color: white;
            font-size: 1.2rem;
            font-weight: 500;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.8);
            opacity: 0;
            animation: fadeInOut 10s infinite;
        }
        
        @keyframes fadeInOut {
            0%, 100% { opacity: 0; transform: translate(-50%, -50%) scale(0.8); }
            20%, 80% { opacity: 1; transform: translate(-50%, -50%) scale(1); }
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
        
        .mist {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 50%),
                radial-gradient(circle at 70% 70%, rgba(155, 89, 182, 0.1) 0%, rgba(155, 89, 182, 0) 50%);
            border-radius: 50%;
            animation: rotate 20s linear infinite;
        }
        
        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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
        
        .fortune-teller {
            position: absolute;
            top: 0px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 18rem;
            opacity: 0.1;
            text-shadow: 0 0 20px rgba(155, 89, 182, 0.8);
            line-height: 1;
            z-index: 0;
        }
        
        .crystal-inner {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: radial-gradient(circle at 40% 40%, 
                rgba(255, 255, 255, 0.1) 0%, 
                rgba(155, 89, 182, 0.05) 30%, 
                rgba(155, 89, 182, 0.1) 60%);
            transform: rotate(0deg);
            animation: inner-rotate 15s linear infinite;
        }
        
        @keyframes inner-rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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
        <div class="crystal-ball">
            <div class="crystal-globe">
                <div class="mist"></div>
                <div class="crystal-inner"></div>
                <div class="crystal-reflection"></div>
                <div id="crystal-message" class="crystal-message"></div>
            </div>
            <div class="crystal-stand"></div>
        </div>
        
        <h1 class="fortune-title">The Obscured Index</h1>
        <p class="narrator-text">"Once upon a time… in the depths of obsession…"</p>
        
        <div class="loading-progress">
            <div id="progress-bar" class="progress-bar"></div>
        </div>
        
        <div class="fortune-teller">🔮</div>
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
            const crystalMessage = document.getElementById('crystal-message');
            let messageIndex = 0;
            
            function showNextMessage() {
                crystalMessage.textContent = messages[messageIndex];
                messageIndex = (messageIndex + 1) % messages.length;
            }
            
            showNextMessage();
            setInterval(showNextMessage, 3000);
            
            // Track if narration is complete
            let narrationComplete = false;
            let skipRequested = false;
            
            // Listen for audio end
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