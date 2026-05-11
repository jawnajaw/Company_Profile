<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $role = $_POST['role'];
    $rank = $_POST['rank'] ?? '';
    $bio  = $_POST['bio'] ?? '';
    $sort_order = 0;

    $image_path = 'assets/img/team/default.jpg';

    // Handle Image Upload
    if (isset($_FILES['team_image']) && $_FILES['team_image']['error'] === 0) {
        $filename = 'team_' . time() . '_' . $_FILES['team_image']['name'];
        $target = '../assets/img/team/' . $filename;
        
        if (!is_dir('../assets/img/team/')) {
            mkdir('../assets/img/team/', 0777, true);
        }

        if (compressImage($_FILES['team_image']['tmp_name'], $target, 75)) {
            $image_path = 'assets/img/team/' . $filename;
        }
    }

    try {
        $sql = "INSERT INTO team (name, role, rank, bio, image) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $role, $rank, $bio, $image_path]);

        header("Location: manage_team.php?success=1");
        exit();
    } catch (PDOException $e) {
        die("Error saving team member: " . $e->getMessage());
    }
}
?>
