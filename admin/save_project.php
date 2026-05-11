<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Validation
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("Invalid CSRF Token");
    }
    
    $title = sanitize_input($_POST['title']);
    $category = sanitize_input($_POST['category']);
    $incident_rate = sanitize_input($_POST['incident_rate']);
    $patrol_status = sanitize_input($_POST['patrol_status']);
    $problem = sanitize_input($_POST['problem']);
    $solution = sanitize_input($_POST['solution']);
    $result = sanitize_input($_POST['result']);

    $image_path = 'assets/img/projects/default.jpg';

    // Handle Image Upload
    if (isset($_FILES['project_image']) && $_FILES['project_image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $filename = $_FILES['project_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (!in_array($ext, $allowed)) {
            die("Error: Format file tidak didukung (Hanya JPG, PNG, WEBP)");
        }
        
        if ($_FILES['project_image']['size'] > 5 * 1024 * 1024) {
            die("Error: Ukuran file maksimal 5MB");
        }

        $new_filename = 'project_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $target = '../assets/img/projects/' . $new_filename;
        
        if (!is_dir('../assets/img/projects/')) {
            mkdir('../assets/img/projects/', 0777, true);
        }

        if (compressImage($_FILES['project_image']['tmp_name'], $target, 75)) {
            $image_path = 'assets/img/projects/' . $new_filename;
        }
    }

    try {
        $sql = "INSERT INTO projects (title, category, incident_rate, patrol_status, problem, solution, result, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $category, $incident_rate, $patrol_status, $problem, $solution, $result, $image_path]);

        header("Location: manage_projects.php?success=1");
        exit();
    } catch (PDOException $e) {
        die("Error saving project: " . $e->getMessage());
    }
}
?>
