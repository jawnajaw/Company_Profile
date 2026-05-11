<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db_connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        
        header("Location: manage_projects.php?deleted=1");
        exit();
    } catch (PDOException $e) {
        die("Error deleting project: " . $e->getMessage());
    }
}
?>
