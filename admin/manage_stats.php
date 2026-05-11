<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Handle Add/Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_stat'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die("Token keamanan tidak valid.");
    }
    $label = sanitize_input($_POST['label']);
    $count_value = sanitize_input($_POST['count_value']);

    if (isset($_POST['stat_id']) && !empty($_POST['stat_id'])) {
        $sql = "UPDATE stats SET label=?, count_value=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$label, $count_value, $_POST['stat_id']]);
    } else {
        $sql = "INSERT INTO stats (label, count_value) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$label, $count_value]);
    }
    header("Location: manage_stats.php?success=1");
    exit();
}

// Handle Delete
if (isset($_POST['delete_stat'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die("Token keamanan tidak valid.");
    }
    $id = $_POST['stat_id'];
    $stmt = $pdo->prepare("DELETE FROM stats WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: manage_stats.php?deleted=1");
    exit();
}

$stats = get_all_stats();
include 'includes/admin_header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-graph-up-arrow me-2 text-warning"></i> Statistics</h2>
        <button class="btn btn-premium btn-premium-primary" data-bs-toggle="modal" data-bs-target="#statModal">
            <i class="bi bi-plus-lg me-1"></i> Tambah Statistik
        </button>
    </div>

    <div class="row g-4">
        <?php foreach ($stats as $stat): ?>
        <div class="col-lg-3 col-md-6">
            <div class="card-premium h-100 text-center">
                <div class="card-body-premium">
                    <h2 class="display-4 fw-bold text-warning mb-1" style="font-family: 'Outfit', sans-serif;"><?php echo htmlspecialchars($stat['count_value']); ?></h2>
                    <p class="text-muted-custom small fw-bold text-uppercase letter-spacing-1"><?php echo htmlspecialchars($stat['label']); ?></p>
                </div>
                <div class="card-footer-premium d-flex gap-2">
                    <button class="btn btn-premium btn-light border flex-grow-1 py-2" onclick="editStat(<?php echo htmlspecialchars(json_encode($stat)); ?>)">
                        Edit
                    </button>
                    <form method="POST" onsubmit="return confirm('Hapus statistik ini?')">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="stat_id" value="<?php echo $stat['id']; ?>">
                        <button type="submit" name="delete_stat" class="btn btn-premium btn-outline-danger border-0 py-2 px-3">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="statModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card-premium border-0">
            <form method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="stat_id" id="stat_id">
                <div class="modal-header-premium d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold" id="modalTitle">Konfigurasi Statistik</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body-premium">
                    <div class="mb-4">
                        <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Nilai Angka (ex: 500+, 100%)</label>
                        <input type="text" name="count_value" id="st_value" class="form-control" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Label Deskripsi</label>
                        <input type="text" name="label" id="st_label" class="form-control" placeholder="ex: Personel Aktif" required>
                    </div>
                </div>
                <div class="modal-footer-premium d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-premium btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="save_stat" class="btn btn-premium btn-premium-primary">Simpan Statistik</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editStat(stat) {
    document.getElementById('modalTitle').innerText = 'Edit Statistik';
    document.getElementById('stat_id').value = stat.id;
    document.getElementById('st_value').value = stat.count_value;
    document.getElementById('st_label').value = stat.label;
    new bootstrap.Modal(document.getElementById('statModal')).show();
}
</script>

<?php include 'includes/admin_footer.php'; ?>
