<?php
session_start();
require_once '../includes/db_connect.php';

$message = '';
$error = '';
$reset_link = ''; // For simulation in local environment

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    $stmt = $pdo->prepare("SELECT id, username FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // 1. Generate Unique Token
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // 2. Simpan ke Database
        $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expiry = ? WHERE id = ?");
        $stmt->execute([$token, $expiry, $user['id']]);

        // 3. Siapkan Link Reset
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'];
        $base_url = $protocol . "://" . $host . dirname($_SERVER['PHP_SELF']);
        $reset_url = $base_url . "/reset_password.php?token=" . $token;

        // SIMULASI PENGIRIMAN EMAIL (Karena XAMPP Lokal)
        $message = "Link reset telah dibuat. Dalam sistem produksi, ini akan dikirim ke email Anda.";
        $reset_link = $reset_url;

    } else {
        $error = "Email tidak terdaftar dalam sistem kami!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password | PT JSMP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
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
    <h3 class="fw-bold mb-2">Lupa Password?</h3>
    <p class="text-muted small mb-4">Masukkan email terdaftar untuk menerima link reset.</p>

    <?php if($error): ?>
        <div class="alert alert-danger small py-2 rounded-3"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if($message): ?>
        <div class="alert alert-success small py-2 rounded-3 mb-3"><?php echo $message; ?></div>
        <?php if($reset_link): ?>
            <div class="p-3 bg-dark rounded-3 mb-4 text-start">
                <label class="x-small text-muted d-block mb-1">SIMULASI EMAIL (Klik link di bawah):</label>
                <a href="<?php echo $reset_link; ?>" class="small text-info text-break"><?php echo $reset_link; ?></a>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-4 text-start">
            <label class="form-label small opacity-75">Alamat Email</label>
            <input type="email" name="email" class="form-control" placeholder="admin@ptjsmp.com" required>
        </div>
        <button type="submit" class="btn btn-primary w-100 shadow-lg">Minta Link Reset</button>
        <div class="mt-4">
            <a href="login.php" class="text-decoration-none small text-muted"><i class="bi bi-arrow-left me-1"></i> Kembali ke Login</a>
        </div>
    </form>
</div>

</body>
</html>
