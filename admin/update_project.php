<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $category = $_POST['category'];
    $incident_rate = $_POST['incident_rate'];
    $patrol_status = $_POST['patrol_status'];
    $problem = $_POST['problem'];
    $solution = $_POST['solution'];
    $result = $_POST['result'];

    try {
        // Prepare base SQL
        $sql = "UPDATE projects SET 
                title = ?, 
                category = ?, 
                incident_rate = ?, 
                patrol_status = ?, 
                problem = ?, 
                solution = ?, 
                result = ?";
        $params = [$title, $category, $incident_rate, $patrol_status, $problem, $solution, $result];

        // Handle new image if uploaded
        if (isset($_FILES['project_image']) && $_FILES['project_image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['project_image']['tmp_name'];
            $fileName = $_FILES['project_image']['name'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            $newFileName = 'project_' . time() . '.' . $fileExtension;
            $uploadFileDir = '../assets/img/projects/';
            
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true);
            }

            $dest_path = $uploadFileDir . $newFileName;
            $image_path = 'assets/img/projects/' . $newFileName;

            if(compressImage($fileTmpPath, $dest_path, 75)) {
                $sql .= ", image = ?";
                $params[] = $image_path;
            }
        }

        $sql .= " WHERE id = ?";
        $params[] = $id;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        header("Location: manage_projects.php?updated=1");
        exit();
    } catch (PDOException $e) {
        die("Error updating project: " . $e->getMessage());
    }
}
?>
