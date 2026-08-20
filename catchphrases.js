// Catchphrase carousel functionality
document.addEventListener('DOMContentLoaded', function() {
    const catchphrases = [
        "Every chapter ends with pain. We keep coming back anyway.",
        "Welcome to your 3 A.M. personality shift.",
        "The plot? Lost. The vibes? Immaculate.",
        "Once upon a time... you said you'd sleep early.",
        "Here be dragons, cliffhangers, and way too many hot villains.",
        "You promised to read just one. That was 84 years ago.",
        "Who needs therapy when you have fictional trauma?",
        "The library you never meant to build. But here we are.",
        "The scrolls are ancient. The simping is eternal.",
        "Destined to read 'just one more chapter' for eternity."
    ];
    
    // Create container
    const banner = document.querySelector('.banner');
    const container = document.createElement('div');
    container.className = 'catchphrase-container';
    banner.appendChild(container);
    
    // Create initial catchphrase element
    const catchphrase = document.createElement('div');
    catchphrase.className = 'catchphrase active';
    catchphrase.textContent = catchphrases[0];
    container.appendChild(catchphrase);
    
    let currentIndex = 0;
    
    // Function to rotate catchphrases
    function rotateCatchphrases() {
        // Fade out current catchphrase
        catchphrase.classList.remove('active');
        
        // After fade out, change text and fade in
        setTimeout(() => {
            currentIndex = (currentIndex + 1) % catchphrases.length;
            catchphrase.textContent = catchphrases[currentIndex];
            catchphrase.classList.add('active');
        }, 1000);
    }
    
    // Rotate every 5 seconds
    setInterval(rotateCatchphrases, 5000);
});