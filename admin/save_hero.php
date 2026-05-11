<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../includes/functions.php';

    if (!verify_csrf_token($_POST['csrf_token'])) {
        die("Token keamanan tidak valid.");
    }

    $headline = $_POST['headline']; // Allowed some HTML spans
    $subheadline = sanitize_input($_POST['subheadline']);
    $badge_1 = sanitize_input($_POST['badge_1']);
    $badge_2 = sanitize_input($_POST['badge_2']);
    $cta_text = sanitize_input($_POST['cta_text']);
    $cta_link = sanitize_input($_POST['cta_link']);

    $image_path = null;

    // Handle Image Upload
    if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $filename = $_FILES['hero_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            if ($_FILES['hero_image']['size'] <= 2 * 1024 * 1024) {
                $new_filename = 'hero_' . time() . '.' . $ext;
                $target = '../assets/img/bg/' . $new_filename;

                if (move_uploaded_file($_FILES['hero_image']['tmp_name'], $target)) {
                    $image_path = 'assets/img/bg/' . $new_filename;
                }
            }
        }
    }

    try {
        $check = $pdo->query("SELECT id FROM hero_settings LIMIT 1")->fetch();

        if ($check) {
            $id = $check['id'];
            if ($image_path) {
                $sql = "UPDATE hero_settings SET headline=?, subheadline=?, badge_1=?, badge_2=?, cta_text=?, cta_link=?, image=? WHERE id=?";
                $params = [$headline, $subheadline, $badge_1, $badge_2, $cta_text, $cta_link, $image_path, $id];
            } else {
                $sql = "UPDATE hero_settings SET headline=?, subheadline=?, badge_1=?, badge_2=?, cta_text=?, cta_link=? WHERE id=?";
                $params = [$headline, $subheadline, $badge_1, $badge_2, $cta_text, $cta_link, $id];
            }
        } else {
            $final_image = $image_path ? $image_path : 'assets/img/hero-bg.jpg';
            $sql = "INSERT INTO hero_settings (headline, subheadline, badge_1, badge_2, cta_text, cta_link, image) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $params = [$headline, $subheadline, $badge_1, $badge_2, $cta_text, $cta_link, $final_image];
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        header("Location: manage_hero.php?success=1");
        exit();
    } catch (PDOException $e) {
        die("Error updating hero: " . $e->getMessage());
    }
}
?>
