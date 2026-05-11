<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $role = $_POST['role'];
    $rank = $_POST['rank'];
    $bio = $_POST['bio'];

    try {
        $sql = "UPDATE team SET name = ?, role = ?, rank = ?, bio = ?";
        $params = [$name, $role, $rank, $bio];

        if (isset($_FILES['team_image']) && $_FILES['team_image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['team_image']['tmp_name'];
            $fileName = $_FILES['team_image']['name'];
            $fileSize = $_FILES['team_image']['size'];
            $fileType = $_FILES['team_image']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            $newFileName = 'team_' . time() . '.' . $fileExtension;
            $uploadFileDir = '../assets/img/team/';
            
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true);
            }

            $dest_path = $uploadFileDir . $newFileName;
            $image_path = 'assets/img/team/' . $newFileName;

            if(compressImage($fileTmpPath, $dest_path, 75)) {
                $sql .= ", image = ?";
                $params[] = $image_path;
            }
        }

        $sql .= " WHERE id = ?";
        $params[] = $id;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        header("Location: manage_team.php?updated=1");
        exit();
    } catch (PDOException $e) {
        die("Error updating team member: " . $e->getMessage());
    }
}
?>
