<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    die("Unauthorized");
}

require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

if (isset($_GET['id'])) {
    if (!verify_csrf_token($_GET['token'])) {
        die("Invalid CSRF Token");
    }

    $id = $_GET['id'];
    
    // Get path first to delete file
    $stmt = $pdo->prepare("SELECT image_path FROM hero_slides WHERE id = ?");
    $stmt->execute([$id]);
    $slide = $stmt->fetch();

    if ($slide) {
        $full_path = '../' . $slide['image_path'];
        if (file_exists($full_path)) {
            unlink($full_path);
        }
        
        $stmt = $pdo->prepare("DELETE FROM hero_slides WHERE id = ?");
        $stmt->execute([$id]);
        
        header("Location: manage_hero.php?success=1");
        exit();
    }
}

header("Location: manage_hero.php");
exit();
