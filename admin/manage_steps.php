<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Handle Add/Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_step'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die("Token keamanan tidak valid.");
    }
    $step_number = sanitize_input($_POST['step_number']);
    $title = sanitize_input($_POST['title']);
    $description = sanitize_input($_POST['description']);

    if (isset($_POST['step_id']) && !empty($_POST['step_id'])) {
        $sql = "UPDATE work_steps SET step_number=?, title=?, description=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$step_number, $title, $description, $_POST['step_id']]);
    } else {
        $sql = "INSERT INTO work_steps (step_number, title, description) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$step_number, $title, $description]);
    }
    header("Location: manage_steps.php?success=1");
    exit();
}

// Handle Delete
if (isset($_POST['delete_step'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die("Token keamanan tidak valid.");
    }
    $id = $_POST['step_id'];
    $stmt = $pdo->prepare("DELETE FROM work_steps WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: manage_steps.php?deleted=1");
    exit();
}

$steps = get_all_work_steps();
include 'includes/admin_header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-list-ol me-2 text-warning"></i> Operation Steps</h2>
        <button class="btn btn-premium btn-premium-primary" data-bs-toggle="modal" data-bs-target="#stepModal">
            <i class="bi bi-plus-lg me-1"></i> Tambah Langkah
        </button>
    </div>

    <div class="row g-4">
        <?php foreach ($steps as $step): ?>
        <div class="col-lg-3 col-md-6">
            <div class="card-premium h-100 text-center">
                <div class="card-body-premium">
                    <div class="mx-auto mb-4 d-flex align-items-center justify-content-center bg-app text-warning rounded-circle shadow-sm fw-bold" style="width: 60px; height: 60px; border: 2px solid var(--jsmp-border); font-size: 1.5rem; font-family: 'Outfit', sans-serif;">
                        <?php echo $step['step_number']; ?>
                    </div>
                    <h5 class="fw-bold mb-3"><?php echo htmlspecialchars($step['title']); ?></h5>
                    <p class="text-muted-custom small lh-base"><?php echo htmlspecialchars($step['description']); ?></p>
                </div>
                <div class="card-footer-premium d-flex gap-2">
                    <button class="btn btn-premium btn-light border flex-grow-1 py-2" onclick="editStep(<?php echo htmlspecialchars(json_encode($step)); ?>)">
                        Edit
                    </button>
                    <form method="POST" onsubmit="return confirm('Hapus langkah ini?')">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="step_id" value="<?php echo $step['id']; ?>">
                        <button type="submit" name="delete_step" class="btn btn-premium btn-outline-danger border-0 py-2 px-3">
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
<div class="modal fade" id="stepModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card-premium border-0">
            <form method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="step_id" id="step_id">
                <div class="modal-header-premium d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold" id="modalTitle">Konfigurasi Alur Kerja</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body-premium">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Urutan</label>
                            <input type="number" name="step_number" id="s_num" class="form-control" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Judul Langkah</label>
                            <input type="text" name="title" id="s_title" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Deskripsi Detail</label>
                            <textarea name="description" id="s_desc" class="form-control" rows="4" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer-premium d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-premium btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="save_step" class="btn btn-premium btn-premium-primary">Simpan Langkah</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editStep(step) {
    document.getElementById('modalTitle').innerText = 'Edit Langkah Kerja';
    document.getElementById('step_id').value = step.id;
    document.getElementById('s_num').value = step.step_number;
    document.getElementById('s_title').value = step.title;
    document.getElementById('s_desc').value = step.description;
    new bootstrap.Modal(document.getElementById('stepModal')).show();
}
</script>

<?php include 'includes/admin_footer.php'; ?>
