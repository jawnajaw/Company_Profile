<?php
session_start();
if (isset($_SESSION['admin_logged_in'])) {
    header("Location: dashboard.php");
    exit();
}

require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Validation
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("Invalid CSRF Token");
    }
    
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user) {
        $error = 'Username tidak ditemukan di database!';
    } else if (!password_verify($password, $user['password'])) {
        $error = 'Password salah! Hash di DB: ' . substr($user['password'], 0, 10) . '...';
    } else {
        // Check if account is active
        if ($user['status'] !== 'active') {
            $error = 'Akun Anda dinonaktifkan. Silakan hubungi Super Admin.';
        } else {
            session_regenerate_id(true); // Prevent session fixation
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_name'] = $user['full_name'];
            $_SESSION['admin_role'] = $user['role']; // Save role to session
            header("Location: dashboard.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | PT JSMP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --color-obsidian: #030712;
            --color-slate-deep: #0f172a;
            --color-gold: #eab308;
            --btn-grad: linear-gradient(135deg, #fbbf24 0%, #d97706 100%);
        }
        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at center, #111827 0%, #030712 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            overflow: hidden;
        }
        .login-card {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(25px);
            border: 1px solid rgba(234, 179, 8, 0.15);
            border-radius: 32px;
            padding: 4rem 3.5rem;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 40px 100px -20px rgba(0, 0, 0, 0.8), inset 0 1px 1px rgba(255,255,255,0.05);
            position: relative;
        }
        .login-card::before {
            content: '';
            position: absolute;
            top: -50px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 100px;
            background: var(--color-gold);
            filter: blur(100px);
            opacity: 0.15;
            z-index: -1;
        }
        h3 {
            font-family: 'Outfit', sans-serif;
            letter-spacing: 2px;
            font-weight: 800;
        }
        .form-control {
            background: rgba(0, 0, 0, 0.2) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: #fff !important;
            padding: 1rem 1.25rem;
            border-radius: 16px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .form-control:focus {
            background: rgba(0, 0, 0, 0.3) !important;
            border-color: var(--color-gold) !important;
            box-shadow: 0 0 0 4px rgba(234, 179, 8, 0.1) !important;
        }
        .btn-primary {
            padding: 1.1rem;
            border-radius: 18px;
            font-weight: 800;
            background: var(--btn-grad) !important;
            border: none !important;
            color: #000 !important;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-size: 0.85rem;
            box-shadow: 0 15px 30px rgba(217, 119, 6, 0.2) !important;
        }
        .btn-primary:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 20px 40px rgba(217, 119, 6, 0.4) !important;
        }
        .logo-icon {
            font-size: 4rem;
            color: var(--color-gold);
            margin-bottom: 1.5rem;
            display: inline-block;
            filter: drop-shadow(0 0 10px rgba(234, 179, 8, 0.3));
        }
        .x-small { font-size: 0.75rem; letter-spacing: 0.5px; }
    </style>
</head>
<body>

<div class="login-card text-center">
    <div class="logo-icon">
        <i class="bi bi-shield-lock-fill"></i>
    </div>
    <h3 class="mb-1">JSMP CONTROL</h3>
    <p class="text-muted small mb-5 fw-medium opacity-75">Secure Infrastructure Management</p>

    <?php if($error): ?>
        <div class="alert alert-danger bg-danger bg-opacity-10 text-danger border-0 small py-3 rounded-4 mb-4" role="alert">
            <i class="bi bi-exclamation-circle-fill me-2"></i> <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <?php csrf_field(); ?>
        <div class="mb-4 text-start">
            <label class="form-label x-small fw-bold text-muted text-uppercase mb-2 ms-1">Authorized ID</label>
            <input type="text" name="username" class="form-control" placeholder="Enter username" required>
        </div>
        <div class="mb-5 text-start">
            <label class="form-label x-small fw-bold text-muted text-uppercase mb-2 ms-1">Access Token</label>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Establish Session</button>
        <div class="mt-4">
            <a href="forgot_password.php" class="text-decoration-none x-small text-warning fw-bold opacity-50 hover-opacity-100">Credential Recovery?</a>
        </div>
    </form>
    
    <div class="mt-5 pt-4 border-top border-white border-opacity-5">
        <p class="x-small text-muted mb-0 opacity-40">© 2026 PT. JAKA SATRIA MANDALA PUTRA</p>
    </div>
</div>

</body>
</html>
