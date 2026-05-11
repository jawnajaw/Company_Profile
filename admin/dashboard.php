<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db_connect.php';

// Fetch Statistics
$count_leads = $pdo->query("SELECT COUNT(*) FROM leads WHERE status = 'New'")->fetchColumn();
$total_leads = $pdo->query("SELECT COUNT(*) FROM leads")->fetchColumn();
$total_projects = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
$total_team = $pdo->query("SELECT COUNT(*) FROM team")->fetchColumn();

// Fetch Recent Leads
$recent_leads = $pdo->query("SELECT * FROM leads ORDER BY created_at DESC LIMIT 5")->fetchAll();

include 'includes/admin_header.php';
?>

<div class="container-fluid py-4">
    <div class="mb-5">
        <h1 class="fw-800 letter-spacing-1 mb-1">Welcome back, <span class="text-warning"><?php echo explode(' ', $_SESSION['admin_name'])[0]; ?></span>!</h1>
        <p class="text-muted-custom fw-medium">Berikut adalah ringkasan performa sistem keamanan JSMP hari ini, <?php echo date('d M Y'); ?>.</p>
    </div>

    <div class="row g-4 mb-4">
        <!-- New Leads Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card-premium h-100">
                <div class="card-body-premium">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                            <i class="bi bi-person-plus fs-3 text-warning"></i>
                        </div>
                        <span class="badge badge-premium bg-warning text-dark">BARU</span>
                    </div>
                    <h2 class="fw-bold mb-1 mt-3" style="font-family: 'Outfit', sans-serif;"><?php echo $count_leads; ?></h2>
                    <p class="text-muted-custom mb-0 fw-medium">Leads Baru Masuk</p>
                </div>
            </div>
        </div>
        <!-- Total Leads Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card-premium h-100">
                <div class="card-body-premium">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="rounded-circle bg-info bg-opacity-10 p-3">
                            <i class="bi bi-people fs-3 text-info"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-1 mt-3" style="font-family: 'Outfit', sans-serif;"><?php echo $total_leads; ?></h2>
                    <p class="text-muted-custom mb-0 fw-medium">Total Database Klien</p>
                </div>
            </div>
        </div>
        <!-- Projects Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card-premium h-100">
                <div class="card-body-premium">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3">
                            <i class="bi bi-journal-check fs-3 text-success"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-1 mt-3" style="font-family: 'Outfit', sans-serif;"><?php echo $total_projects; ?></h2>
                    <p class="text-muted-custom mb-0 fw-medium">Portofolio Proyek</p>
                </div>
            </div>
        </div>
        <!-- Team Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card-premium h-100">
                <div class="card-body-premium">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                            <i class="bi bi-person-badge fs-3 text-primary"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-1 mt-3" style="font-family: 'Outfit', sans-serif;"><?php echo $total_team; ?></h2>
                    <p class="text-muted-custom mb-0 fw-medium">Personel Manajemen</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <!-- Lead Trends Chart -->
        <div class="col-lg-8">
            <div class="card-premium h-100">
                <div class="card-header-premium d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Tren Akuisisi Klien (7 Hari Terakhir)</h5>
                    <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill small fw-bold">Live Data</span>
                </div>
                <div class="card-body-premium" style="height: 350px;">
                    <canvas id="leadsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- System Health Widget -->
        <div class="col-lg-4">
            <div class="card-premium h-100">
                <div class="card-header-premium">
                    <h5 class="card-title mb-0">System Health</h5>
                </div>
                <div class="card-body-premium">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small text-muted-custom fw-bold">DATABASE STATUS</span>
                            <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill x-small">STABLE</span>
                        </div>
                        <div class="progress" style="height: 6px; background: rgba(255,255,255,0.05);">
                            <div class="progress-bar bg-success" style="width: 100%"></div>
                        </div>
                    </div>
                    
                    <div class="list-group list-group-flush bg-transparent">
                        <div class="list-group-item bg-transparent border-white border-opacity-5 px-0 py-3 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-cpu text-warning me-3 fs-5"></i>
                                <span class="small fw-medium">PHP Version</span>
                            </div>
                            <span class="small fw-bold text-muted-custom"><?php echo phpversion(); ?></span>
                        </div>
                        <div class="list-group-item bg-transparent border-white border-opacity-5 px-0 py-3 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-database-check text-warning me-3 fs-5"></i>
                                <span class="small fw-medium">Database Engine</span>
                            </div>
                            <span class="small fw-bold text-muted-custom">MySQL / PDO</span>
                        </div>
                        <div class="list-group-item bg-transparent border-0 px-0 py-3 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-clock-history text-warning me-3 fs-5"></i>
                                <span class="small fw-medium">Server Time</span>
                            </div>
                            <span class="small fw-bold text-muted-custom"><?php echo date('H:i T'); ?></span>
                        </div>
                    </div>

                    <div class="mt-4 p-3 bg-app rounded-4 border border-dashed border-warning border-opacity-25">
                        <p class="x-small text-muted-custom mb-0 lh-base italic">
                            <i class="bi bi-shield-lock-fill text-warning me-1"></i>
                            Sistem diproteksi dengan enkripsi Bcrypt 256-bit dan CSRF Token Protection.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <div class="row g-4">
        <!-- Recent Leads Table -->
        <div class="col-lg-8">
            <div class="card-premium h-100">
                <div class="card-header-premium d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Leads Terbaru</h5>
                    <a href="manage_leads.php" class="btn btn-premium btn-light border py-1">Lihat Semua</a>
                </div>
                <div class="card-body-premium p-0">
                    <div class="table-responsive">
                        <table class="table table-premium table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Layanan</th>
                                    <th>Status</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($recent_leads as $lead): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-primary"><?php echo htmlspecialchars($lead['name']); ?></div>
                                        <div class="x-small text-muted-custom"><?php echo htmlspecialchars($lead['phone']); ?></div>
                                    </td>
                                    <td><span class="x-small fw-800 text-uppercase letter-spacing-1"><?php echo $lead['service_requested']; ?></span></td>
                                    <td>
                                        <span class="badge badge-premium bg-warning text-dark"><?php echo $lead['status']; ?></span>
                                    </td>
                                    <td class="text-end">
                                        <a href="manage_leads.php" class="text-warning"><i class="bi bi-arrow-right-circle fs-5"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Access -->
        <div class="col-lg-4">
            <div class="card-premium h-100">
                <div class="card-header-premium">
                    <h5 class="card-title mb-0">Akses Cepat CMS</h5>
                </div>
                <div class="card-body-premium">
                    <div class="d-grid gap-3">
                        <a href="manage_hero.php" class="btn btn-premium btn-light text-start border d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-display me-2 text-warning"></i> Hero Section</span>
                            <i class="bi bi-chevron-right small opacity-50"></i>
                        </a>
                        <a href="manage_settings.php" class="btn btn-premium btn-light text-start border d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-gear me-2 text-warning"></i> Settings & SEO</span>
                            <i class="bi bi-chevron-right small opacity-50"></i>
                        </a>
                        <a href="manage_team.php" class="btn btn-premium btn-light text-start border d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-people me-2 text-warning"></i> Manajemen Tim</span>
                            <i class="bi bi-chevron-right small opacity-50"></i>
                        </a>
                        <a href="manage_projects.php" class="btn btn-premium btn-light text-start border d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-journal-check me-2 text-warning"></i> Case Studies</span>
                            <i class="bi bi-chevron-right small opacity-50"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Prepare chart data
$dates = [];
$counts = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $dates[] = date('D', strtotime($date));
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM leads WHERE DATE(created_at) = ?");
    $stmt->execute([$date]);
    $counts[] = $stmt->fetchColumn();
}
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('leadsChart').getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(234, 179, 8, 0.4)');
    gradient.addColorStop(1, 'rgba(234, 179, 8, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($dates); ?>,
            datasets: [{
                label: 'Leads Baru',
                data: <?php echo json_encode($counts); ?>,
                borderColor: '#eab308',
                borderWidth: 4,
                backgroundColor: gradient,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#eab308',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                    ticks: { color: '#94a3b8', font: { family: 'Inter' } }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#94a3b8', font: { family: 'Inter' } }
                }
            }
        }
    });
});
</script>

<?php include 'includes/admin_footer.php'; ?>
