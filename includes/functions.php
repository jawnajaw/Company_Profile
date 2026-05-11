<?php
/**
 * Global Functions for PTJSMP
 */

require_once 'db_connect.php';

/**
 * Get a specific site setting
 */
function get_setting($key, $default = '') {
    global $pdo;
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $result = $stmt->fetch();
    return $result ? $result['setting_value'] : $default;
}

/**
 * Get all site settings as an associative array
 */
function get_settings() {
    global $pdo;
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
    $settings = [];
    while ($row = $stmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    return $settings;
}

/**
 * Get all team members
 */
function get_team_members() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM team ORDER BY id ASC");
    return $stmt->fetchAll();
}

/**
 * Get Hero Settings
 */
function get_hero_settings() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM hero_settings LIMIT 1");
    return $stmt->fetch();
}

/**
 * Get All Stats
 */
function get_all_stats() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM stats ORDER BY id ASC");
    return $stmt->fetchAll();
}

/**
 * Get All Advantages
 */
function get_all_advantages() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM advantages ORDER BY id ASC");
    return $stmt->fetchAll();
}

/**
 * Get All Work Steps
 */
function get_all_work_steps() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM work_steps ORDER BY step_number ASC");
    return $stmt->fetchAll();
}

/**
 * Get All Service Packages
 */
function get_all_packages() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM service_packages ORDER BY id ASC");
    return $stmt->fetchAll();
}

/**
 * Get All Services
 */
function get_all_services() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM services ORDER BY id ASC");
    return $stmt->fetchAll();
}

/**
 * Generate or get CSRF token
 */
function generate_csrf_token() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Output hidden CSRF field
 */
function csrf_field() {
    $token = generate_csrf_token();
    echo '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Verify CSRF token
 */
function verify_csrf_token($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    return true;
}

/**
 * Sanitize input
 */
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

/**
 * Smart Image Compressor
 * Reduces file size while maintaining professional quality
 */
function compressImage($source, $destination, $quality = 75) {
    // Check if GD Library is enabled
    if (!function_exists('imagecreatefromjpeg')) {
        // Fallback: Just move the file without compression if GD is missing
        return move_uploaded_file($source, $destination);
    }

    $info = getimagesize($source);
    
    if ($info['mime'] == 'image/jpeg') {
        $image = imagecreatefromjpeg($source);
        imagejpeg($image, $destination, $quality);
    } elseif ($info['mime'] == 'image/png') {
        $image = imagecreatefrompng($source);
        // PNG quality is 0-9, inverse of JPG
        imagepng($image, $destination, floor((100 - $quality) / 10));
    } elseif ($info['mime'] == 'image/webp') {
        $image = imagecreatefromwebp($source);
        imagewebp($image, $destination, $quality);
    } else {
        // Fallback for other formats
        return move_uploaded_file($source, $destination);
    }
    
    if (isset($image)) {
        imagedestroy($image);
    }
    return true;
}
?>
