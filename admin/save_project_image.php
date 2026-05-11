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

    $project_id = $_POST['project_id'];
    
    if (isset($_FILES['project_photo']) && $_FILES['project_photo']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $filename = $_FILES['project_photo']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $new_filename = 'gallery_' . $project_id . '_' . time() . '.' . $ext;
            $target_dir = '../assets/img/projects/gallery/';
            
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $target_file = $target_dir . $new_filename;
            $db_path = 'assets/img/projects/gallery/' . $new_filename;

            if (move_uploaded_file($_FILES['project_photo']['tmp_name'], $target_file)) {
                $stmt = $pdo->prepare("INSERT INTO project_images (project_id, image_path) VALUES (?, ?)");
                $stmt->execute([$project_id, $db_path]);
                
                header("Location: manage_projects.php?success=1");
                exit();
            }
        }
    }
}

header("Location: manage_projects.php?error=1");
exit();
