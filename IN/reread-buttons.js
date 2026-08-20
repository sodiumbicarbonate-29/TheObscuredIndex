// Function to add reread buttons to manhwas with "Done" status
function addRereadButtons() {
    // Grid view - add reread buttons
    document.querySelectorAll('.manhwa-grid .manhwa-card').forEach(card => {
        const select = card.querySelector('.reading-status-select');
        if (select && select.value === 'Done') {
            const manhwaId = select.getAttribute('data-manhwa-id');
            const actions = card.querySelector('.manhwa-actions');
            
            // Only add button if it doesn't already exist
            if (!actions.querySelector('.reread-btn')) {
                const rereadBtn = document.createElement('a');
                rereadBtn.href = `library.php?action=reread&id=${manhwaId}`;
                rereadBtn.className = 'reread-btn';
                rereadBtn.innerHTML = '<i class="fas fa-redo"></i> Reread';
                actions.appendChild(rereadBtn);
            }
        }
    });
    
    // List view - add reread buttons
    document.querySelectorAll('.manhwa-list .manhwa-list-item').forEach(item => {
        const select = item.querySelector('.reading-status-select');
        if (select && select.value === 'Done') {
            const manhwaId = select.getAttribute('data-manhwa-id');
            const actions = item.querySelector('.manhwa-actions');
            
            // Only add button if it doesn't already exist
            if (!actions.querySelector('.reread-btn')) {
                const rereadBtn = document.createElement('a');
                rereadBtn.href = `library.php?action=reread&id=${manhwaId}`;
                rereadBtn.className = 'reread-btn';
                rereadBtn.innerHTML = '<i class="fas fa-redo"></i> Reread';
                actions.appendChild(rereadBtn);
            }
        }
    });
}

// Initialize reread buttons when the page loads
document.addEventListener('DOMContentLoaded', function() {
    addRereadButtons();
    
    // Update buttons when reading status changes
    document.querySelectorAll('.reading-status-select').forEach(select => {
        select.addEventListener('change', function() {
            setTimeout(addRereadButtons, 100); // Small delay to ensure status is updated
        });
    });
});