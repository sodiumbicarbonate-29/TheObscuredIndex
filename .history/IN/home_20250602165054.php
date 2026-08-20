<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db_connect.php';

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$genres_query = "SELECT DISTINCT genre FROM Manhwas WHERE genre IS NOT NULL AND genre != '' ORDER BY genre";
$genres_result = mysqli_query($conn, $genres_query);
$genres = [];
while ($genre_row = mysqli_fetch_assoc($genres_result)) {
    $genre_list = explode(',', $genre_row['genre']);
    foreach ($genre_list as $g) {
        $g = trim($g);
        if (!empty($g) && !in_array($g, $genres)) {
            $genres[] = $g;
        }
    }
}

$romance_types = ['BL', 'Straight', 'No Romance'];
foreach ($romance_types as $type) {
    $key = array_search($type, $genres);
    if ($key !== false) {
        unset($genres[$key]);
    }
}
sort($genres); 

$romance_types = ['BL', 'Straight', 'No Romance'];
foreach ($romance_types as $type) {
    $key = array_search($type, $genres);
    if ($key !== false) {
        unset($genres[$key]);
    }
}

$reading_query = "SELECT m.*, urs.reading_status, urs.start_reading_date 
                 FROM Manhwas m 
                 JOIN User_Reading_Status urs ON m.manhwa_id = urs.manhwa_id 
                 WHERE urs.user_id = ? AND (urs.reading_status = 'Currently Reading' OR urs.reading_status = 'Reread') 
                 ORDER BY urs.last_updated DESC 
                 LIMIT 5";
$reading_stmt = mysqli_prepare($conn, $reading_query);
mysqli_stmt_bind_param($reading_stmt, "i", $user_id);
mysqli_stmt_execute($reading_stmt);
$reading_result = mysqli_stmt_get_result($reading_stmt);

$completed_query = "SELECT m.*, urs.finish_reading_date 
                   FROM Manhwas m 
                   JOIN User_Reading_Status urs ON m.manhwa_id = urs.manhwa_id 
                   WHERE urs.user_id = ? AND urs.reading_status = 'Done' 
                   ORDER BY urs.finish_reading_date DESC 
                   LIMIT 5";
$completed_stmt = mysqli_prepare($conn, $completed_query);
mysqli_stmt_bind_param($completed_stmt, "i", $user_id);
mysqli_stmt_execute($completed_stmt);
$completed_result = mysqli_stmt_get_result($completed_stmt);

$new_uploads_query = "SELECT * FROM Manhwas 
                     WHERE upload_date >= DATE_SUB(NOW(), INTERVAL 30 DAY) 
                     ORDER BY upload_date DESC 
                     LIMIT 5";
$new_uploads_result = mysqli_query($conn, $new_uploads_query);

$stats_query = "SELECT 
                (SELECT COUNT(*) FROM User_Reading_Status WHERE user_id = ? AND (reading_status = 'Currently Reading' OR reading_status = 'Reread')) as reading_count,
                (SELECT COUNT(*) FROM User_Reading_Status WHERE user_id = ? AND reading_status = 'Done') as completed_count,
                (SELECT COUNT(*) FROM User_Reading_Status WHERE user_id = ? AND reading_status = 'Plan to Read') as plan_count,
                (SELECT COUNT(*) FROM User_Reading_Status WHERE user_id = ?) as total_count";
$stats_stmt = mysqli_prepare($conn, $stats_query);
mysqli_stmt_bind_param($stats_stmt, "iiii", $user_id, $user_id, $user_id, $user_id);
mysqli_stmt_execute($stats_stmt);
$stats_result = mysqli_stmt_get_result($stats_stmt);
$stats = mysqli_fetch_assoc($stats_result);

$plan_query = "SELECT m.*, urs.reading_status 
               FROM Manhwas m 
               JOIN User_Reading_Status urs ON m.manhwa_id = urs.manhwa_id 
               WHERE urs.user_id = ? AND urs.reading_status = 'Plan to Read' 
               ORDER BY urs.last_updated DESC 
               LIMIT 5";
$plan_stmt = mysqli_prepare($conn, $plan_query);
mysqli_stmt_bind_param($plan_stmt, "i", $user_id);
mysqli_stmt_execute($plan_stmt);
$plan_result = mysqli_stmt_get_result($plan_stmt);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'start_reading' && isset($_POST['manhwa_id'])) {
        $manhwa_id = (int)$_POST['manhwa_id'];
        $current_date = date('Y-m-d');
        
        $update_query = "UPDATE User_Reading_Status SET 
                        reading_status = 'Currently Reading', 
                        start_reading_date = ?, 
                        last_updated = NOW() 
                        WHERE user_id = ? AND manhwa_id = ? AND reading_status = 'Plan to Read'";
        $update_stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($update_stmt, "sii", $current_date, $user_id, $manhwa_id);
        $result = mysqli_stmt_execute($update_stmt);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => $result]);
        exit();
    }
    else if ($_POST['action'] == 'mark_done' && isset($_POST['manhwa_id'])) {
        $manhwa_id = (int)$_POST['manhwa_id'];
        $current_date = date('Y-m-d');
        
        $update_query = "UPDATE User_Reading_Status SET 
                        reading_status = 'Done', 
                        finish_reading_date = ?, 
                        last_updated = NOW() 
                        WHERE user_id = ? AND manhwa_id = ? AND (reading_status = 'Currently Reading' OR reading_status = 'Reread')";
        $update_stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($update_stmt, "sii", $current_date, $user_id, $manhwa_id);
        $result = mysqli_stmt_execute($update_stmt);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => $result]);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - My Manhwa Collection</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #6c5ce7;
            --secondary-color: #a29bfe;
            --text-color: #2d3436;
            --light-color: #f5f6fa;
            --accent-color: #fd79a8;
            --success-color: #00b894;
            --warning-color: #fdcb6e;
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
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        <?php include 'includes/header_styles.php'; ?>
        
        main {
            flex: 1;
            padding: 0;
            width: 100%;
        }
        
        .main-content {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }
        
        /* Slideshow styles */
        .cover-slideshow {
            width: 100%;
            height: 75vh;
            overflow: hidden;
            position: relative;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .cover-slide {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }
        
        .cover-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center center;
        }
        
        .cover-slide.active {
            opacity: 1;
        }
        
        .slide-quote {
            position: absolute;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%);
            width: 80%;
            max-width: 800px;
            background-color: rgba(108, 92, 231, 0.9);
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 1.3rem;
            font-weight: 500;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6);
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3), 0 0 30px rgba(162, 155, 254, 0.5);
            font-family: 'Segoe UI', Georgia, serif;
            letter-spacing: 0.5px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(5px);
            animation: glow 2s infinite alternate;
            z-index: 5;
        }
        
        @keyframes glow {
            from {
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3), 0 0 20px rgba(162, 155, 254, 0.5);
            }
            to {
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3), 0 0 30px rgba(162, 155, 254, 0.8);
            }
        }
        
        .welcome-section {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .welcome-section h1 {
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .fairy-link {
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .fairy-link:hover {
            transform: translateY(-3px) rotate(10deg);
            text-shadow: 0 0 10px var(--accent-color);
            animation: fairy-glow 1.5s infinite alternate;
        }
        
        @keyframes fairy-glow {
            from { text-shadow: 0 0 5px var(--accent-color); }
            to { text-shadow: 0 0 15px var(--accent-color), 0 0 20px var(--primary-color); }
        }
        
        .stats-container {
            display: flex;
            gap: 15px;
            margin-top: 15px;
            flex-wrap: nowrap;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #f8f9fa, #e2e6ea);
            border-radius: 15px;
            padding: 15px 10px;
            flex: 1;
            min-width: 0;
            box-shadow: 0 5px 15px rgba(108, 92, 231, 0.2), inset 0 -3px 0 rgba(108, 92, 231, 0.3);
            text-align: center;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
        }
        
        .stat-card:before {
            content: '';
            position: absolute;
            top: -10px;
            left: -10px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,255,255,0.8) 0%, rgba(255,255,255,0) 70%);
            opacity: 0.6;
        }
        
        .stat-card:after {
            content: '';
            position: absolute;
            bottom: -5px;
            right: -5px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(162,155,254,0.8) 0%, rgba(162,155,254,0) 70%);
            opacity: 0.4;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(108, 92, 231, 0.3), inset 0 -3px 0 rgba(108, 92, 231, 0.4);
        }
        
        .stat-card h3 {
            font-size: 1.8rem;
            color: var(--primary-color);
            margin-bottom: 5px;
            text-shadow: 0 1px 2px rgba(255, 255, 255, 0.8);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
        }
        
        .stat-card p {
            color: var(--text-color);
            font-size: 0.9rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .filter-container {
            margin-top: 20px;
        }
        
        .filter-container h3 {
            margin-bottom: 10px;
            color: var(--primary-color);
            font-size: 1.1rem;
        }
        
        .filter-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            overflow-x: auto;
            padding-bottom: 5px;
        }
        
        .filter-tab {
            background-color: white;
            border: none;
            border-radius: 20px;
            padding: 8px 16px;
            cursor: pointer;
            font-weight: 600;
            color: var(--text-color);
            transition: all 0.3s;
            white-space: nowrap;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        
        .filter-tab.active {
            background-color: var(--primary-color);
            color: white;
        }
        
        .filter-tab:hover:not(.active) {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .section-title {
            margin: 25px 0 15px;
            color: var(--primary-color);
            font-size: 1.5rem;
            display: flex;
            align-items: center;
        }
        
        .section-title i {
            margin-right: 10px;
        }
        
        .manhwa-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .manhwa-card {
            background: white;
            border-radius: 10px;
            overflow: visible;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        
        .manhwa-card:hover {
            transform: translateY(-5px);
        }
        
        .manhwa-cover {
            height: 200px;
            overflow: hidden;
            position: relative;
        }
        
        .manhwa-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .manhwa-status {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            color: white;
        }
        
        .reading-status {
            position: relative;
            z-index: 5;
        }
        
        .reading-status:hover .status-tooltip {
            visibility: visible;
            opacity: 1;
            transform: translateX(-50%) translateY(-8px);
            animation: tooltipAppear 0.3s forwards;
        }
        
        @keyframes tooltipAppear {
            0% { transform: translateX(-50%) translateY(0); opacity: 0; }
            70% { transform: translateX(-50%) translateY(-12px); }
            100% { transform: translateX(-50%) translateY(-8px); opacity: 1; }
        }
        
        .status-tooltip {
            visibility: hidden;
            opacity: 0;
            position: absolute;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            text-align: center;
            padding: 12px 18px;
            border-radius: 12px;
            width: max-content;
            max-width: 220px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
            z-index: 1000;
            pointer-events: none;
            box-shadow: 0 4px 20px rgba(108, 92, 231, 0.4), 0 0 15px rgba(253, 121, 168, 0.4);
            border: 2px solid rgba(255, 255, 255, 0.3);
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
            letter-spacing: 0.5px;
            backdrop-filter: blur(5px);
            animation: tooltipGlow 2s infinite alternate;
        }
        
        @keyframes tooltipGlow {
            from {
                box-shadow: 0 4px 20px rgba(108, 92, 231, 0.4), 0 0 15px rgba(253, 121, 168, 0.4);
            }
            to {
                box-shadow: 0 4px 20px rgba(108, 92, 231, 0.7), 0 0 25px rgba(253, 121, 168, 0.7);
            }
        }
        
        .status-tooltip::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -8px;
            border-width: 8px;
            border-style: solid;
            border-color: var(--accent-color) transparent transparent transparent;
        }
        
        .status-tooltip::before {
            content: "✨";
            position: absolute;
            top: -10px;
            left: 15px;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.9);
            text-shadow: 0 0 5px var(--accent-color);
            animation: sparkleFloat 3s infinite ease-in-out;
        }
        
        @keyframes sparkleFloat {
            0% { transform: translateY(0) rotate(0deg); opacity: 0.5; }
            50% { transform: translateY(-10px) rotate(180deg); opacity: 1; }
            100% { transform: translateY(0) rotate(360deg); opacity: 0.5; }
        }
        
        .status-ongoing {
            background-color: var(--primary-color);
        }
        
        .status-completed {
            background-color: var(--success-color);
        }
        
        .status-dropped {
            background-color: var(--accent-color);
        }
        
        .status-hiatus {
            background-color: var(--warning-color);
        }
        
        .manhwa-info {
            padding: 15px;
        }
        
        .manhwa-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--text-color);
            overflow: visible;
            min-height: 2.4em;
            word-wrap: break-word;
        }
        
        .manhwa-author {
            font-size: 0.8rem;
            color: #666;
            margin-bottom: 5px;
        }
        
        .manhwa-genre {
            font-size: 0.8rem;
            color: var(--accent-color);
            margin-bottom: 0;
            font-style: italic;
            display: inline-block;
            position: relative;
            top: 3.px;
        }
        
        .genre-date-container {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 10px;
            flex-wrap: nowrap;
        }
        
        .start-date {
            font-size: 0.7rem;
            color: #666;
            position: relative;
            padding-left: 18px;
            display: inline-flex;
            align-items: center;
        }
        
        .start-date:before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: var(--primary-color);
            opacity: 0.7;
        }
        
        .reading-progress {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.8rem;
            color: #666;
            gap: 8px;
        }
        
        .reread-badge {
            background-color: var(--accent-color);
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .read-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--primary-color);
            color: white;
            padding: 5px 10px;
            height: 32px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.3s;
            text-align: center;
            white-space: nowrap;
            flex: 1;
        }
        
        .read-btn i {
            margin-right: 5px;
        }
        
        .read-btn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .done-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--success-color);
            color: white;
            padding: 5px 10px;
            height: 32px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            text-align: center;
            white-space: nowrap;
            flex: 1;
        }
        
        .done-btn i {
            margin-right: 5px;
        }
        
        .done-btn:hover {
            background-color: #00a382;
            transform: translateY(-2px);
        }
        
        .done-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--success-color);
            color: white;
            padding: 5px 10px;
            height: 32px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
            flex: 1;
        }
        
        .done-btn:hover {
            background-color: #00a382;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        
        .empty-state {
            text-align: center;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .empty-state i {
            font-size: 3rem;
            color: var(--secondary-color);
            margin-bottom: 15px;
        }
        
        .empty-state h3 {
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .empty-state p {
            color: #666;
        }
        
        .view-all {
            display: inline-block;
            margin-left: auto;
            font-size: 0.9rem;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }
        
        .view-all:hover {
            text-decoration: underline;
        }
        
        
        @media (max-width: 768px) {
            .main-content {
                padding: 15px;
            }
            
            .manhwa-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }
            
            .manhwa-cover {
                height: 200px;
            }
            
            .stat-card {
                min-width: 0;
                flex: 1;
            }
            
            .stat-card h3 {
                font-size: 1.4rem;
            }
            
            .stat-card p {
                font-size: 0.8rem;
            }
            
            .section-title {
                font-size: 1.3rem;
                flex-wrap: wrap;
            }
            
            .view-all {
                margin-left: 10px;
            }
            
            .cover-slideshow {
                height: 50vh;
            }
        }
        
        @media (max-width: 480px) {
            .main-content {
                padding: 10px;
            }
            
            .manhwa-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 8px;
            }
            
            .manhwa-cover {
                height: 130px;
            }
            
            .manhwa-info {
                padding: 6px;
            }
            
            .manhwa-title {
                font-size: 0.8rem;
                min-height: 1.8em;
                margin-bottom: 2px;
            }
            
            .manhwa-genre {
                font-size: 0.7rem;
            }
            
            .reading-progress {
                flex-direction: row;
                flex-wrap: nowrap;
            }
            
            .read-btn, .done-btn {
                padding: 4px 8px;
                font-size: 0.7rem;
                flex: 1;
                min-width: 0;
            }
            
            .read-btn i, .done-btn i {
                margin-right: 2px;
            }
            
            .welcome-section h1 {
                font-size: 1.5rem;
            }
            
            .stats-container {
                gap: 8px;
            }
            
            .stat-card {
                padding: 10px 5px;
            }
            
            .stat-card h3 {
                font-size: 1.2rem;
            }
            
            .stat-card p {
                font-size: 0.7rem;
            }
            
            .section-title {
                font-size: 1.2rem;
                margin: 20px 0 10px;
                justify-content: flex-start;
                width: 100%;
            }
            
            .section-title .view-all {
                margin-left: auto;
            }
            
            .section-title i {
                margin-right: 5px;
            }
            
            .view-all {
                font-size: 0.8rem;
            }
            
            .cover-slideshow {
                height: 60vh;
                border-radius: 5px;
            }
            
            .genre-date-container {
                display: flex;
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
                gap: 5px;
                flex-wrap: wrap;
            }
            
            .manhwa-genre {
                font-size: 0.65rem;
                max-width: 50%;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            
            .start-date {
                font-size: 0.65rem;
                padding-left: 12px;
            }
            
            .start-date:before {
                width: 8px;
                height: 8px;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/navbarIN.php'; ?>

    <main>
        <?php
        $morningQuotes = [
            "Awake already? Brave soul… or just another all-nighter?",
            "The prophecy said you'd touch grass today. You ignored it.",
            "You woke up and chose... emotional instability? Respect.",
            "Morning magic is real. So is reading in your pajamas.",
            "Early to rise, early to cry over fictional characters.",
            "Let's pretend this is a productive start."
        ];
        
        $eveningQuotes = [
            "Just one chapter before bed, right? Right?",
            "This is when the real reading begins.",
            "Sun's down. Sanity's gone. Time for angst.",
            "Dinner? Social life? No… this is reading hour.",
            "You could stop here. But let's not lie to each other."
        ];
        
        $midnightQuotes = [
            "Oh no. You again. At this hour?",
            "Midnight? Peak reading. Peak regret.",
            "The ghost of sleep haunts you.",
            "Darkness falls. So does your productivity.",
            "You said one chapter. 3 hours ago. We both knew you lied.",
            "Still convincing yourself you'll stop after this one, huh?",
            "Daywalkers sleep. Night gremlins binge. You chose wisely.",
            "Your brain: 'Sleep.' You: 'Trauma arc.'"
        ];
        
        $hour = date('G');
        $quotes = [];
        $welcomeMessage = "";
        
        // Debug output
        error_log("Current hour: $hour");
        
        if ($hour >= 5 && $hour < 12) {
            $quotes = $morningQuotes;
            $welcomeMessage = $morningQuotes[array_rand($morningQuotes)];
            error_log("Using morning quotes: " . count($quotes) . " quotes available");
        } else if ($hour >= 18 && $hour <= 21) {
            $quotes = $eveningQuotes;
            $welcomeMessage = $eveningQuotes[array_rand($eveningQuotes)];
            error_log("Using evening quotes: " . count($quotes) . " quotes available");
        } else if ($hour >= 22 || $hour < 5) {
            $quotes = $midnightQuotes;
            $welcomeMessage = $midnightQuotes[array_rand($midnightQuotes)];
            error_log("Using midnight quotes: " . count($quotes) . " quotes available");
        } else {
            $quotes = $morningQuotes; 
            $welcomeMessage = "Track your manhwa collection";
            error_log("Using default afternoon message, but setting quotes array");
        }
        
        $slideshow_query = "SELECT cover_image FROM Manhwas ORDER BY RAND() LIMIT 10";
        $slideshow_result = mysqli_query($conn, $slideshow_query);
        
        if (mysqli_num_rows($slideshow_result) > 0):
        ?>
        <div class="cover-slideshow">
            <?php 
            $active = true;
            $i = 0;
            while ($slide = mysqli_fetch_assoc($slideshow_result)): 
                $cover = !empty($slide['cover_image']) ? '../' . $slide['cover_image'] : '../images/default-cover.jpg';
                $quote = !empty($quotes) && count($quotes) > 0 ? $quotes[$i % count($quotes)] : "";
                error_log("Slide $i - Quote: " . ($quote ? $quote : "No quote"));
            ?>
                <div class="cover-slide <?php echo $active ? 'active' : ''; ?>">
                    <img src="<?php echo htmlspecialchars($cover); ?>" alt="Manhwa Cover">
                    <?php if (!empty($quote)): ?>
                    <div class="slide-quote"><?php echo htmlspecialchars($quote); ?></div>
                    <?php endif; ?>
                </div>
            <?php 
                $active = false;
                $i++;
            endwhile; 
            ?>
        </div>
        <?php endif; ?>
        
        <div class="main-content">
            <div class="welcome-section">
            <h1>Welcome back, <?php echo htmlspecialchars($username); ?>! <a href="secret_shelf.php" class="fairy-link">🧚‍♀️</a></h1>
            <p>
            <?php echo htmlspecialchars($welcomeMessage); ?>
            </p>
            
            <div class="stats-container">
                <div class="stat-card">
                    <h3><?php echo $stats['reading_count']; ?></h3>
                    <p>Reading</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $stats['completed_count']; ?></h3>
                    <p>Completed</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $stats['plan_count']; ?></h3>
                    <p>Plan to Read</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $stats['total_count']; ?></h3>
                    <p>Total</p>
                </div>
            </div>
            
            <div class="filter-container">
                <h3>Filter by Genre:</h3>
                <div class="filter-tabs">
                    <button class="filter-tab active" data-genre="all">All</button>
                    <button class="filter-tab" data-genre="BL">BL</button>
                    <button class="filter-tab" data-genre="Straight">Straight</button>
                    <button class="filter-tab" data-genre="No Romance">No Romance</button>
                    <?php foreach ($genres as $genre): ?>
                        <button class="filter-tab" data-genre="<?php echo htmlspecialchars($genre); ?>"><?php echo htmlspecialchars($genre); ?></button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <h2 class="section-title">
            <i class="fas fa-book-open"></i> Continue Reading
            <a href="#" class="view-all">View All</a>
        </h2>
        
        <?php if (mysqli_num_rows($reading_result) > 0): ?>
            <div class="manhwa-grid">
                <?php while ($manhwa = mysqli_fetch_assoc($reading_result)): ?>
                    <div class="manhwa-card">
                        <div class="manhwa-cover">
                            <img src="<?php echo !empty($manhwa['cover_image']) ? '../' . htmlspecialchars($manhwa['cover_image']) : '../images/default-cover.jpg'; ?>" alt="<?php echo htmlspecialchars($manhwa['title']); ?>">
                            <span class="manhwa-status status-<?php echo strtolower($manhwa['status']); ?>"><?php echo $manhwa['status']; ?></span>
                        </div>
                        <div class="manhwa-info">
                            <h3 class="manhwa-title"><?php echo htmlspecialchars($manhwa['title']); ?></h3>
                            <div class="genre-date-container">
                                <p class="manhwa-genre"><?php echo !empty($manhwa['genre']) ? htmlspecialchars($manhwa['genre']) : 'No genre'; ?></p>
                                <?php if (!empty($manhwa['start_reading_date'])): ?>
                                <span class="start-date">Started: <?php echo date('M d', strtotime($manhwa['start_reading_date'])); ?></span>
                                <?php endif; ?>
                                <?php if ($manhwa['reading_status'] === 'Reread'): ?>
                                <span class="reread-badge">Rereading</span>
                                <?php endif; ?>
                            </div>
                            <div class="reading-progress">
                                <div class="reading-status">
                                    <?php if (!empty($manhwa['reading_link'])): ?>
                                        <a href="<?php echo htmlspecialchars($manhwa['reading_link']); ?>" target="_blank" class="read-btn"><i class="fas fa-book-open"></i> Read</a>
                                        <div class="status-tooltip">Still here? You're either REALLY into this… or just forgot it open.</div>
                                    <?php else: ?>
                                        <a href="view_manhwa.php?id=<?php echo $manhwa['manhwa_id']; ?>" class="read-btn"><i class="fas fa-eye"></i> View</a>
                                        <div class="status-tooltip">Still here? You're either REALLY into this… or just forgot it open.</div>
                                    <?php endif; ?>
                                </div>
                                <div class="reading-status">
                                    <button class="done-btn" onclick="updateReadingStatus('mark_done', <?php echo $manhwa['manhwa_id']; ?>)"><i class="fas fa-check"></i> Done</button>
                                    <div class="status-tooltip">Congrats, now go touch grass.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-book"></i>
                <h3>No manhwas in progress</h3>
                <p>Start reading to see your collection here!</p>
            </div>
        <?php endif; ?>
        
        <h2 class="section-title">
            <i class="fas fa-list"></i> Plan to Read
            <a href="library.php?reading_status=Plan+to+Read" class="view-all">View All</a>
        </h2>
        
        <?php if (mysqli_num_rows($plan_result) > 0): ?>
            <div class="manhwa-grid">
                <?php while ($manhwa = mysqli_fetch_assoc($plan_result)): ?>
                    <div class="manhwa-card">
                        <div class="manhwa-cover">
                            <img src="<?php echo !empty($manhwa['cover_image']) ? '../' . htmlspecialchars($manhwa['cover_image']) : '../images/default-cover.jpg'; ?>" alt="<?php echo htmlspecialchars($manhwa['title']); ?>">
                            <span class="manhwa-status status-<?php echo strtolower($manhwa['status']); ?>"><?php echo $manhwa['status']; ?></span>
                        </div>
                        <div class="manhwa-info">
                            <h3 class="manhwa-title"><?php echo htmlspecialchars($manhwa['title']); ?></h3>
                            <p class="manhwa-genre"><?php echo !empty($manhwa['genre']) ? htmlspecialchars($manhwa['genre']) : 'No genre'; ?></p>
                            <div class="reading-progress">
                                <div class="reading-status">
                                    <?php if (!empty($manhwa['reading_link'])): ?>
                                        <a href="<?php echo htmlspecialchars($manhwa['reading_link']); ?>" target="_blank" class="read-btn" onclick="updateReadingStatus('start_reading', <?php echo $manhwa['manhwa_id']; ?>)"><i class="fas fa-book-open"></i> Read</a>
                                        <div class="status-tooltip">You've made a promise. Let's see if you're a person of honor.</div>
                                    <?php else: ?>
                                        <a href="view_manhwa.php?id=<?php echo $manhwa['manhwa_id']; ?>" class="read-btn"><i class="fas fa-eye"></i> View</a>
                                        <div class="status-tooltip">You've made a promise. Let's see if you're a person of honor.</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-clipboard-list"></i>
                <h3>No manhwas planned</h3>
                <p>Add manhwas to your reading plan!</p>
            </div>
        <?php endif; ?>
            
        </div>
    </main>

    <?php 
    $root_path = '../';
    include '../includes/footer.php'; 
    ?>

    <script>
        let slideIndex = 0;
        const slides = document.querySelectorAll('.cover-slide');
        
        // Debug logging
        console.log('Slides found:', slides.length);
        slides.forEach((slide, i) => {
            const quote = slide.querySelector('.slide-quote');
            console.log(`Slide ${i+1} has quote:`, quote ? quote.textContent : 'No quote found');
        });
        
        function showSlides() {
            if (slides.length === 0) {
                console.log('No slides found, exiting showSlides');
                return;
            }
            
            for (let i = 0; i < slides.length; i++) {
                slides[i].classList.remove('active');
            }
            
            slideIndex++;
            if (slideIndex > slides.length) {
                slideIndex = 1;
            }
            
            console.log('Activating slide', slideIndex);
            slides[slideIndex - 1].classList.add('active');
            setTimeout(showSlides, 5000); 
        }
        
        if (slides.length > 1) {
            console.log('Starting slideshow timer');
            setTimeout(showSlides, 5000);
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const filterTabs = document.querySelectorAll('.filter-tab');
            const manhwaCards = document.querySelectorAll('.manhwa-card');
            
            filterTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    filterTabs.forEach(t => t.classList.remove('active'));
                    
                    this.classList.add('active');
                    
                    const selectedGenre = this.getAttribute('data-genre');
                    
                    manhwaCards.forEach(card => {
                        const cardGenre = card.querySelector('.manhwa-genre').textContent;
                        
                        if (selectedGenre === 'all' || cardGenre.includes(selectedGenre)) {
                            card.style.display = 'block';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            });
        });
        function updateReadingStatus(action, manhwaId) {
            const formData = new FormData();
            formData.append('action', action);
            formData.append('manhwa_id', manhwaId);
            
            fetch('home.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (action === 'mark_done') {
                        const card = event.target.closest('.manhwa-card');
                        if (card) {
                            card.style.opacity = '0';
                            setTimeout(() => {
                                card.style.display = 'none';
                                
                                const readingCount = document.querySelector('.stat-card:nth-child(1) h3');
                                const completedCount = document.querySelector('.stat-card:nth-child(2) h3');
                                
                                if (readingCount && completedCount) {
                                    readingCount.textContent = parseInt(readingCount.textContent) - 1;
                                    completedCount.textContent = parseInt(completedCount.textContent) + 1;
                                }
                                
                                const remainingCards = document.querySelectorAll('.manhwa-grid .manhwa-card[style="display: block;"], .manhwa-grid .manhwa-card:not([style])');
                                if (remainingCards.length === 0) {
                                    const grid = document.querySelector('.manhwa-grid');
                                    const emptyState = document.createElement('div');
                                    emptyState.className = 'empty-state';
                                    emptyState.innerHTML = `
                                        <i class="fas fa-book"></i>
                                        <h3>No manhwas in progress</h3>
                                        <p>Start reading to see your collection here!</p>
                                    `;
                                    grid.parentNode.insertBefore(emptyState, grid.nextSibling);
                                    grid.style.display = 'none';
                                }
                            }, 500);
                        }
                    } else if (action === 'start_reading') {
                        const card = event.target.closest('.manhwa-card');
                        if (card) {
                            const planCount = document.querySelector('.stat-card:nth-child(3) h3');
                            const readingCount = document.querySelector('.stat-card:nth-child(1) h3');
                            
                            if (planCount && readingCount) {
                                planCount.textContent = parseInt(planCount.textContent) - 1;
                                readingCount.textContent = parseInt(readingCount.textContent) + 1;
                            }
                        }
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        }

    </script>
</body>
</html>

    
