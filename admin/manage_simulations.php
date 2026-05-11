<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Pastikan tabel ada (Self-healing database)
$pdo->exec("CREATE TABLE IF NOT EXISTS simulation_options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    location_name VARCHAR(100) NOT NULL,
    multiplier DECIMAL(10,2) DEFAULT 1.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Handle CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_opt'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die("Token keamanan tidak valid.");
    }
    $name = sanitize_input($_POST['location_name']);
    $multiplier = sanitize_input($_POST['multiplier']);

    if (isset($_POST['opt_id']) && !empty($_POST['opt_id'])) {
        $stmt = $pdo->prepare("UPDATE simulation_options SET location_name=?, multiplier=? WHERE id=?");
        $stmt->execute([$name, $multiplier, $_POST['opt_id']]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO simulation_options (location_name, multiplier) VALUES (?, ?)");
        $stmt->execute([$name, $multiplier]);
    }
    header("Location: manage_simulations.php?success=1");
    exit();
}

if (isset($_POST['delete_opt'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die("Token keamanan tidak valid.");
    }
    $stmt = $pdo->prepare("DELETE FROM simulation_options WHERE id=?");
    $stmt->execute([$_POST['opt_id']]);
    header("Location: manage_simulations.php?deleted=1");
    exit();
}

// Get all options
$options = $pdo->query("SELECT * FROM simulation_options ORDER BY location_name ASC")->fetchAll();

include 'includes/admin_header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-calculator me-2 text-warning"></i> Simulation Rules</h2>
        <button class="btn btn-premium btn-premium-primary" data-bs-toggle="modal" data-bs-target="#simModal">
            <i class="bi bi-plus-lg me-1"></i> Tambah Lokasi
        </button>
    </div>

    <div class="card-premium mb-4 overflow-hidden border-warning border-opacity-25 bg-app">
        <div class="card-body-premium p-4 d-flex align-items-center gap-3">
            <div class="bg-warning bg-opacity-10 p-3 rounded-4">
                <i class="bi bi-info-circle fs-3 text-warning"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-1">Cost Multiplier Logic</h6>
                <p class="text-muted-custom small mb-0">Gunakan pengali untuk menyesuaikan estimasi biaya berdasarkan resiko area (contoh: 1.0 standar, 1.2 resiko tinggi).</p>
            </div>
        </div>
    </div>

    <div class="card-premium overflow-hidden">
        <div class="card-body-premium p-0">
            <div class="table-responsive">
                <table class="table table-premium table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Lokasi / Tipe Area</th>
                            <th>Cost Multiplier</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($options as $opt): ?>
                        <tr>
                            <td class="ps-4 fw-bold text-primary"><?php echo htmlspecialchars($opt['location_name']); ?></td>
                            <td><span class="badge badge-premium bg-app border text-warning">x<?php echo $opt['multiplier']; ?></span></td>
                            <td class="text-end pe-4">
                                <button class="btn btn-premium btn-light border py-1 px-3" onclick='editOpt(<?php echo json_encode($opt); ?>)'>Edit</button>
                                <form method="POST" class="d-inline" onsubmit="return confirm('Hapus opsi ini?')">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="opt_id" value="<?php echo $opt['id']; ?>">
                                    <button type="submit" name="delete_opt" class="btn btn-premium btn-outline-danger border-0 py-1 px-2"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="simModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card-premium border-0">
            <form method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="opt_id" id="opt_id">
                <div class="modal-header-premium d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold" id="modalTitle">Konfigurasi Simulasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body-premium">
                    <div class="mb-4">
                        <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Nama Lokasi / Tipe Area</label>
                        <input type="text" name="location_name" id="o_name" class="form-control" placeholder="ex: Kawasan Industri, Perkantoran, Gudang" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Multiplier (Pengali)</label>
                        <input type="number" step="0.01" name="multiplier" id="o_mult" class="form-control" value="1.00" required>
                    </div>
                </div>
                <div class="modal-footer-premium d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-premium btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="save_opt" class="btn btn-premium btn-premium-primary">Simpan Konfigurasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editOpt(opt) {
    document.getElementById('modalTitle').innerText = 'Edit Opsi Lokasi';
    document.getElementById('opt_id').value = opt.id;
    document.getElementById('o_name').value = opt.location_name;
    document.getElementById('o_mult').value = opt.multiplier;
    new bootstrap.Modal(document.getElementById('simModal')).show();
}
</script>

<?php include 'includes/admin_footer.php'; ?>
