<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    die(json_encode(['success' => false, 'message' => 'Unauthorized']));
}

require_once '../includes/db_connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Get path first to delete file
    $stmt = $pdo->prepare("SELECT image_path FROM project_images WHERE id = ?");
    $stmt->execute([$id]);
    $img = $stmt->fetch();

    if ($img) {
        $full_path = '../' . $img['image_path'];
        if (file_exists($full_path)) {
            unlink($full_path);
        }
        
        $stmt = $pdo->prepare("DELETE FROM project_images WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode(['success' => true]);
        exit();
    }
}

echo json_encode(['success' => false]);
