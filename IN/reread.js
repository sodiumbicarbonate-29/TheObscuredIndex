document.addEventListener('DOMContentLoaded', function() {
    // Add reread buttons to all manhwas with reading links
    const cards = document.querySelectorAll('.manhwa-card');
    cards.forEach(card => {
        const actions = card.querySelector('.manhwa-actions');
        const manhwaId = card.querySelector('.reading-status-select').getAttribute('data-manhwa-id');
        
        // Check if this manhwa has a reading link
        fetch(`get_reading_link.php?id=${manhwaId}`)
            .then(response => response.json())
            .then(data => {
                if (data.has_link) {
                    // Create reread button
                    const rereadBtn = document.createElement('a');
                    rereadBtn.href = `library.php?action=reread&id=${manhwaId}`;
                    rereadBtn.className = 'reread-btn';
                    rereadBtn.textContent = 'Reread';
                    actions.appendChild(rereadBtn);
                }
            });
    });
});
