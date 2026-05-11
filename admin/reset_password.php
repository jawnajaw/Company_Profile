<?php
session_start();
require_once '../includes/db_connect.php';

$error = '';
$success = '';
$token = $_GET['token'] ?? '';

// 1. Validasi Token di Awal
if (!$token) {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->prepare("SELECT id, username, reset_expiry FROM users WHERE reset_token = ?");
$stmt->execute([$token]);
$user = $stmt->fetch();

$is_expired = false;
if (!$user) {
    $error = "Token tidak valid!";
} else {
    // Cek Expiry
    if (strtotime($user['reset_expiry']) < time()) {
        $error = "Token sudah kadaluarsa (Expired)! Silakan minta link baru.";
        $is_expired = true;
    }
}

// 2. Proses Update Password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    $new_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (strlen($new_password) < 6) {
        $error = "Password minimal harus 6 karakter!";
    } elseif ($new_password !== $confirm_password) {
        $error = "Konfirmasi password tidak cocok!";
    } else {
        // Hash password baru
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update DB & Hapus Token (Single Use)
        $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE id = ?");
        $stmt->execute([$hashed_password, $user['id']]);

        $success = "Password berhasil diubah! Silakan login kembali.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Ulang Password | PT JSMP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #0d1117; height: 100vh; display: flex; align-items: center; justify-content: center; color: #fff; }
        .reset-card { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 24px; padding: 2.5rem; width: 100%; max-width: 450px; }
        .form-control { background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.1); color: #fff; padding: 0.8rem; border-radius: 12px; }
        .form-control:focus { background: rgba(255, 255, 255, 0.12); border-color: #0d6efd; box-shadow: none; color: #fff; }
        .btn-primary { padding: 0.8rem; border-radius: 12px; font-weight: 600; }
    </style>
</head>
<body>

<div class="reset-card text-center">
    <h3 class="fw-bold mb-2">Password Baru</h3>
    <p class="text-muted small mb-4">Silakan masukkan password baru Anda.</p>

    <?php if($error): ?>
        <div class="alert alert-danger small py-2 rounded-3 mb-4"><?php echo $error; ?></div>
        <?php if($is_expired || !$user): ?>
            <a href="forgot_password.php" class="btn btn-outline-light btn-sm w-100 rounded-pill">Minta Link Baru</a>
        <?php endif; ?>
    <?php endif; ?>

    <?php if($success): ?>
        <div class="alert alert-success small py-2 rounded-3 mb-4"><?php echo $success; ?></div>
        <a href="login.php" class="btn btn-primary w-100 shadow-lg">Login Sekarang</a>
    <?php endif; ?>

    <?php if(!$error && !$success): ?>
    <form method="POST">
        <div class="mb-3 text-start">
            <label class="form-label small opacity-75">Password Baru</label>
            <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required minlength="6">
        </div>
        <div class="mb-4 text-start">
            <label class="form-label small opacity-75">Konfirmasi Password Baru</label>
            <input type="password" name="confirm_password" class="form-control" placeholder="Ulangi password" required minlength="6">
        </div>
        <button type="submit" class="btn btn-primary w-100 shadow-lg">Perbarui Password</button>
    </form>
    <?php endif; ?>
</div>

</body>
</html>
