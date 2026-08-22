<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: api/home.php");
} else {
    header("Location: api/login.php");
}
exit();
