<?php
require_once '../includes/db_connect.php';

$username = 'admin';
$new_password = 'password123'; // Ini password baru Anda
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

try {
    // Cek apakah user admin ada
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user) {
        // Update password
        $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update->execute([$hashed_password, $user['id']]);
        echo "✅ Berhasil! Password untuk 'admin' telah direset menjadi: <strong>password123</strong><br>";
    } else {
        // Buat user baru jika belum ada
        $insert = $pdo->prepare("INSERT INTO users (username, password, full_name) VALUES (?, ?, ?)");
        $insert->execute([$username, $hashed_password, 'Administrator JSMP']);
        echo "✅ User 'admin' tidak ditemukan, maka baru saja dibuatkan dengan password: <strong>password123</strong><br>";
    }
    echo "Silakan coba login kembali di halaman login admin. Jangan lupa hapus file ini setelah berhasil.";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
