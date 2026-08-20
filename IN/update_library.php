<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../includes/db_connect.php';

$user_id = $_SESSION['user_id'];

// Add reread button to grid view
function addRereadButton($manhwa) {
    if ($manhwa['reading_status'] == 'Done' && !empty($manhwa['reading_link'])) {
        return '<a href="reread.php?id=' . $manhwa['manhwa_id'] . '" class="reread-btn">Reread</a>';
    }
    return '';
}

// Add CSS for reread button
$css = '
.reread-btn {
    background-color: var(--accent-color);
    color: white !important;
    padding: 5px 10px;
    border-radius: 4px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.2s;
    margin-left: 5px;
}

.reread-btn:hover {
    background-color: #e84393;
    transform: translateY(-2px);
    text-decoration: none !important;
}
';

// JavaScript to add reread buttons
$js = '
document.addEventListener("DOMContentLoaded", function() {
    // Add reread buttons to grid view
    document.querySelectorAll(".manhwa-grid .manhwa-card").forEach(function(card) {
        const manhwaId = card.querySelector(".reading-status-select").getAttribute("data-manhwa-id");
        const readingStatus = card.querySelector(".reading-status-select").value;
        const actions = card.querySelector(".manhwa-actions");
        
        if (readingStatus === "Done") {
            // Check if there\'s a reading link (we\'ll assume there is for this example)
            const rereadBtn = document.createElement("a");
            rereadBtn.href = "reread.php?id=" + manhwaId;
            rereadBtn.className = "reread-btn";
            rereadBtn.textContent = "Reread";
            actions.appendChild(rereadBtn);
        }
    });
    
    // Add reread buttons to list view
    document.querySelectorAll(".manhwa-list .manhwa-list-item").forEach(function(item) {
        const manhwaId = item.querySelector(".reading-status-select").getAttribute("data-manhwa-id");
        const readingStatus = item.querySelector(".reading-status-select").value;
        const actions = item.querySelector(".manhwa-actions");
        
        if (readingStatus === "Done") {
            // Check if there\'s a reading link (we\'ll assume there is for this example)
            const rereadBtn = document.createElement("a");
            rereadBtn.href = "reread.php?id=" + manhwaId;
            rereadBtn.className = "reread-btn";
            rereadBtn.textContent = "Reread";
            actions.appendChild(rereadBtn);
        }
    });
});
';

// Return the data
echo json_encode([
    'css' => $css,
    'js' => $js
]);
?>