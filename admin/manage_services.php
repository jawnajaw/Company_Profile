<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Handle Delete
if (isset($_POST['delete_service'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die("Token keamanan tidak valid.");
    }
    $id = $_POST['service_id'];
    $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: manage_services.php?deleted=1");
    exit();
}

// Handle Add/Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_service'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die("Token keamanan tidak valid.");
    }
    $title = sanitize_input($_POST['title']);
    $description = sanitize_input($_POST['description']);
    $icon = sanitize_input($_POST['icon']);

    if (isset($_POST['service_id']) && !empty($_POST['service_id'])) {
        $sql = "UPDATE services SET title=?, description=?, icon=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $description, $icon, $_POST['service_id']]);
    } else {
        $sql = "INSERT INTO services (title, description, icon) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $description, $icon]);
    }
    header("Location: manage_services.php?success=1");
    exit();
}

$services = get_all_services();
include 'includes/admin_header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-grid-fill me-2 text-warning"></i> Core Services</h2>
        <button class="btn btn-premium btn-premium-primary" data-bs-toggle="modal" data-bs-target="#serviceModal">
            <i class="bi bi-plus-lg me-1"></i> Tambah Layanan
        </button>
    </div>

    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4"><i class="bi bi-check-circle me-2 text-success"></i> Layanan berhasil diperbarui!</div>
    <?php endif; ?>

    <div class="row g-4">
        <?php foreach ($services as $svc): ?>
        <div class="col-lg-4">
            <div class="card-premium h-100 text-center">
                <div class="card-body-premium">
                    <div class="mx-auto mb-4 d-flex align-items-center justify-content-center bg-app text-warning rounded-circle shadow-sm" style="width: 80px; height: 80px; border: 1px solid var(--jsmp-border);">
                        <i class="bi <?php echo $svc['icon']; ?> fs-1"></i>
                    </div>
                    <h5 class="fw-bold mb-3"><?php echo htmlspecialchars($svc['title']); ?></h5>
                    <p class="text-muted-custom small lh-lg"><?php echo htmlspecialchars($svc['description']); ?></p>
                </div>
                <div class="card-footer-premium d-flex gap-2">
                    <button class="btn btn-premium btn-light border flex-grow-1 py-2" onclick="editService(<?php echo htmlspecialchars(json_encode($svc)); ?>)">
                        <i class="bi bi-pencil-square"></i> Edit
                    </button>
                    <form method="POST" onsubmit="return confirm('Hapus layanan ini?')">
                        <?php csrf_field(); ?>
                        <input type="hidden" name="service_id" value="<?php echo $svc['id']; ?>">
                        <button type="submit" name="delete_service" class="btn btn-premium btn-outline-danger border-0 py-2 px-3">
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
<div class="modal fade" id="serviceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card-premium border-0">
            <form method="POST">
                <?php csrf_field(); ?>
                <input type="hidden" name="service_id" id="service_id">
                <div class="modal-header-premium d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold" id="modalTitle">Konfigurasi Layanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body-premium">
                    <div class="mb-4">
                        <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Judul Layanan</label>
                        <input type="text" name="title" id="s_title" class="form-control" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Deskripsi Lengkap</label>
                        <textarea name="description" id="s_desc" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="mb-0">
                        <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Bootstrap Icon Class</label>
                        <input type="text" name="icon" id="s_icon" class="form-control" placeholder="bi-shield-check" required>
                        <div class="small text-muted-custom mt-2">
                            <i class="bi bi-info-circle me-1"></i> Reference: <a href="https://icons.getbootstrap.com/" target="_blank" class="text-warning text-decoration-none">Bootstrap Icons &rarr;</a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer-premium d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-premium btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="save_service" class="btn btn-premium btn-premium-primary">Simpan Layanan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editService(svc) {
    document.getElementById('modalTitle').innerText = 'Edit Layanan';
    document.getElementById('service_id').value = svc.id;
    document.getElementById('s_title').value = svc.title;
    document.getElementById('s_desc').value = svc.description;
    document.getElementById('s_icon').value = svc.icon;
    new bootstrap.Modal(document.getElementById('serviceModal')).show();
}
</script>

<?php include 'includes/admin_footer.php'; ?>
