<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Handle Delete
if (isset($_POST['delete_adv'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die("Token keamanan tidak valid.");
    }
    $id = $_POST['adv_id'];
    $stmt = $pdo->prepare("DELETE FROM advantages WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: manage_advantages.php?deleted=1");
    exit();
}

// Handle Add/Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_adv'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die("Token keamanan tidak valid.");
    }
    $title = sanitize_input($_POST['title']);
    $description = sanitize_input($_POST['description']);
    $icon = sanitize_input($_POST['icon']);

    if (isset($_POST['adv_id']) && !empty($_POST['adv_id'])) {
        $sql = "UPDATE advantages SET title=?, description=?, icon=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $description, $icon, $_POST['adv_id']]);
    } else {
        $sql = "INSERT INTO advantages (title, description, icon) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $description, $icon]);
    }
    header("Location: manage_advantages.php?success=1");
    exit();
}

$advantages = get_all_advantages();
include 'includes/admin_header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-award-fill me-2 text-warning"></i> Competitive Advantages</h2>
        <button class="btn btn-premium btn-premium-primary" data-bs-toggle="modal" data-bs-target="#advModal">
            <i class="bi bi-plus-lg me-1"></i> Tambah Keunggulan
        </button>
    </div>

    <div class="row g-4">
        <?php foreach ($advantages as $adv): ?>
        <div class="col-lg-4">
            <div class="card-premium h-100 text-center">
                <div class="card-body-premium">
                    <div class="mx-auto mb-4 d-flex align-items-center justify-content-center bg-app text-warning rounded-circle shadow-sm" style="width: 80px; height: 80px; border: 1px solid var(--jsmp-border);">
                        <i class="bi <?php echo $adv['icon']; ?> fs-2"></i>
                    </div>
                    <h5 class="fw-bold mb-3"><?php echo htmlspecialchars($adv['title']); ?></h5>
                    <p class="text-muted-custom small lh-lg mb-0"><?php echo htmlspecialchars($adv['description']); ?></p>
                </div>
                <div class="card-footer-premium d-flex gap-2">
                    <button class="btn btn-premium btn-light border flex-grow-1 py-2" onclick="editAdv(<?php echo htmlspecialchars(json_encode($adv)); ?>)">
                        Edit
                    </button>
                    <form method="POST" onsubmit="return confirm('Hapus keunggulan ini?')">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="adv_id" value="<?php echo $adv['id']; ?>">
                        <button type="submit" name="delete_adv" class="btn btn-premium btn-outline-danger border-0 py-2 px-3">
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
<div class="modal fade" id="advModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card-premium border-0">
            <form method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="adv_id" id="adv_id">
                <div class="modal-header-premium d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold" id="modalTitle">Konfigurasi Keunggulan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body-premium">
                    <div class="mb-4">
                        <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Nama Keunggulan</label>
                        <input type="text" name="title" id="a_title" class="form-control" placeholder="ex: Sertifikasi ISO 9001:2015" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Penjelasan Singkat</label>
                        <textarea name="description" id="a_desc" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-0">
                        <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Bootstrap Icon Class</label>
                        <input type="text" name="icon" id="a_icon" class="form-control" placeholder="bi-shield-check" required>
                    </div>
                </div>
                <div class="modal-footer-premium d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-premium btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="save_adv" class="btn btn-premium btn-premium-primary">Simpan Keunggulan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editAdv(adv) {
    document.getElementById('modalTitle').innerText = 'Edit Keunggulan';
    document.getElementById('adv_id').value = adv.id;
    document.getElementById('a_title').value = adv.title;
    document.getElementById('a_desc').value = adv.description;
    document.getElementById('a_icon').value = adv.icon;
    new bootstrap.Modal(document.getElementById('advModal')).show();
}
</script>

<?php include 'includes/admin_footer.php'; ?>
