<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    die("Unauthorized");
}

require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die("Invalid CSRF Token");
    }

    if (isset($_FILES['hero_slide']) && $_FILES['hero_slide']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $filename = $_FILES['hero_slide']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $new_filename = 'slide_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $target_dir = '../assets/img/hero/slides/';
            
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $target_file = $target_dir . $new_filename;
            $db_path = 'assets/img/hero/slides/' . $new_filename;

            if (compressImage($_FILES['hero_slide']['tmp_name'], $target_file, 75)) {
                $stmt = $pdo->prepare("INSERT INTO hero_slides (image_path) VALUES (?)");
                $stmt->execute([$db_path]);
                
                header("Location: manage_hero.php?success=1");
                exit();
            }
        }
    }
}

header("Location: manage_hero.php?error=1");
exit();
