function updateReadingStatus(event, link) {
    // Check if the manhwa is in "Plan to Read" status
    if (link.getAttribute('data-status') === 'Plan to Read') {
        // Get the manhwa ID
        const manhwaId = link.getAttribute('data-manhwa-id');
        
        // Create form data
        const formData = new FormData();
        formData.append('manhwa_id', manhwaId);
        
        // Send AJAX request
        fetch('update_reading_status.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Reading status updated to Currently Reading');
            } else {
                console.error('Failed to update reading status:', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}