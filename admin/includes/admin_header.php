<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | PT JSMP</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Premium Admin Custom UI -->
    <link href="assets/css/premium-admin.css" rel="stylesheet" type="text/css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Pre-render theme check to prevent flickering
        (function() {
            const savedTheme = localStorage.getItem('admin-theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
</head>
<body>

<div class="sidebar shadow-sm">
    <div class="sidebar-brand">
        <i class="bi bi-shield-check"></i>
        <span>JSMP CONTROL</span>
    </div>

    <div class="px-4 py-3 mb-2 border-bottom border-white border-opacity-5">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-app border rounded-circle d-flex align-items-center justify-content-center text-warning fw-bold" style="width: 40px; height: 40px; font-size: 0.9rem;">
                <?php echo strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1)); ?>
            </div>
            <div class="overflow-hidden">
                <h6 class="mb-0 text-truncate small fw-bold text-primary"><?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Administrator'); ?></h6>
                <div class="x-small text-muted-custom opacity-75">System <?php echo htmlspecialchars($_SESSION['admin_role'] ?? 'Admin'); ?></div>
            </div>
        </div>
    </div>
    
    <div class="nav flex-column">
        <div class="nav-section-title">Overview</div>
        <a href="dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
            <i class="bi bi-house-door"></i> Dashboard
        </a>
        
        <div class="nav-section-title">Client Relations</div>
        <a href="manage_leads.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage_leads.php' ? 'active' : ''; ?>">
            <i class="bi bi-person-lines-fill"></i> Data Leads
        </a>
        <a href="view_messages.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'view_messages.php' ? 'active' : ''; ?>">
            <i class="bi bi-chat-dots"></i> Pesan Masuk
        </a>

        <div class="nav-section-title">Content Manager</div>
        <a href="manage_hero.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage_hero.php' ? 'active' : ''; ?>">
            <i class="bi bi-window-stack"></i> Hero Section
        </a>
        <a href="manage_services.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage_services.php' ? 'active' : ''; ?>">
            <i class="bi bi-briefcase"></i> Layanan
        </a>
        <a href="manage_advantages.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage_advantages.php' ? 'active' : ''; ?>">
            <i class="bi bi-award"></i> Keunggulan
        </a>
        <a href="manage_stats.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage_stats.php' ? 'active' : ''; ?>">
            <i class="bi bi-bar-chart"></i> Statistik
        </a>
        <a href="manage_steps.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage_steps.php' ? 'active' : ''; ?>">
            <i class="bi bi-list-ol"></i> Proses Kerja
        </a>
        <a href="manage_about.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage_about.php' ? 'active' : ''; ?>">
            <i class="bi bi-info-circle"></i> Tentang Kami
        </a>
        
        <div class="nav-section-title">Core Business & Ops</div>
        <a href="manage_team.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage_team.php' ? 'active' : ''; ?>">
            <i class="bi bi-people"></i> Manajemen Tim
        </a>
        <a href="manage_projects.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage_projects.php' ? 'active' : ''; ?>">
            <i class="bi bi-stars"></i> Case Studies
        </a>
        <a href="manage_packages.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage_packages.php' ? 'active' : ''; ?>">
            <i class="bi bi-box-seam"></i> Paket Layanan
        </a>
        <a href="manage_simulations.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage_simulations.php' ? 'active' : ''; ?>">
            <i class="bi bi-calculator"></i> Simulasi Biaya
        </a>

        <div class="nav-section-title">System & Security</div>
        <a href="manage_users.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage_users.php' ? 'active' : ''; ?>">
            <i class="bi bi-shield-lock"></i> Manajemen User
        </a>
        <a href="manage_settings.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage_settings.php' ? 'active' : ''; ?>">
            <i class="bi bi-sliders"></i> System Settings
        </a>
        
        <div class="nav-section-title mt-4 border-top border-white border-opacity-5 pt-4">Session Control</div>
        <a href="logout.php" class="nav-link text-danger mb-5">
            <i class="bi bi-power"></i> Log Out Account
        </a>
    </div>
</div>

<div class="main-content">
    <div class="d-flex justify-content-end mb-4">
        <button id="theme-toggle" class="admin-theme-btn shadow-sm">
            <i class="bi bi-moon-stars-fill" id="theme-icon"></i>
        </button>
    </div>
<script src="../assets/js/theme-control.js"></script>
