<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Handle Delete
if (isset($_POST['delete_package'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die("Token keamanan tidak valid.");
    }
    $id = $_POST['package_id'];
    $stmt = $pdo->prepare("DELETE FROM service_packages WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: manage_packages.php?deleted=1");
    exit();
}

// Handle Add/Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_package'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die("Token keamanan tidak valid.");
    }
    $name = sanitize_input($_POST['name']);
    $subtitle = sanitize_input($_POST['subtitle']);
    $features = sanitize_input($_POST['features']);
    $badge_label = sanitize_input($_POST['badge_label']);
    $cta_text = sanitize_input($_POST['cta_text']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    if (isset($_POST['package_id']) && !empty($_POST['package_id'])) {
        $sql = "UPDATE service_packages SET name=?, subtitle=?, features=?, badge_label=?, cta_text=?, is_featured=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $subtitle, $features, $badge_label, $cta_text, $is_featured, $_POST['package_id']]);
    } else {
        $sql = "INSERT INTO service_packages (name, subtitle, features, badge_label, cta_text, is_featured) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $subtitle, $features, $badge_label, $cta_text, $is_featured]);
    }
    header("Location: manage_packages.php?success=1");
    exit();
}

$packages = get_all_packages();
include 'includes/admin_header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-box-seam-fill me-2 text-warning"></i> Paket Layanan</h2>
        <button class="btn btn-premium btn-premium-primary" data-bs-toggle="modal" data-bs-target="#addPackageModal">
            <i class="bi bi-plus-lg me-1"></i> Tambah Paket
        </button>
    </div>

    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4"><i class="bi bi-check-circle me-2"></i> Paket berhasil disimpan!</div>
    <?php endif; ?>
    <?php if(isset($_GET['deleted'])): ?>
        <div class="alert alert-info border-0 rounded-4 shadow-sm mb-4"><i class="bi bi-info-circle me-2"></i> Paket telah dihapus.</div>
    <?php endif; ?>

    <div class="row g-4">
        <?php foreach ($packages as $pkg): ?>
        <div class="col-lg-4">
            <div class="card-premium h-100 <?php echo $pkg['is_featured'] ? 'border-warning' : ''; ?>">
                <div class="card-body-premium">
                    <?php if ($pkg['is_featured']): ?>
                        <span class="badge badge-premium bg-warning text-dark mb-3">Paling Populer</span>
                    <?php endif; ?>
                    <h4 class="fw-bold mb-1"><?php echo htmlspecialchars($pkg['name']); ?></h4>
                    <p class="text-muted-custom mb-3"><?php echo htmlspecialchars($pkg['subtitle']); ?></p>
                    <hr class="opacity-10">
                    <ul class="list-unstyled mb-0">
                        <?php 
                        $feats = explode('|', $pkg['features']);
                        foreach($feats as $f): 
                        ?>
                            <li class="small mb-2 d-flex align-items-center">
                                <i class="bi bi-check-circle-fill text-warning me-2"></i> 
                                <span><?php echo htmlspecialchars($f); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="card-footer-premium">
                    <div class="d-flex gap-2">
                        <button class="btn btn-premium btn-light border flex-grow-1" onclick="editPackage(<?php echo htmlspecialchars(json_encode($pkg)); ?>)">
                            <i class="bi bi-pencil-square me-1"></i> Edit
                        </button>
                        <form method="POST" onsubmit="return confirm('Hapus paket ini?')">
                            <?php csrf_field(); ?>
                            <input type="hidden" name="package_id" value="<?php echo $pkg['id']; ?>">
                            <button type="submit" name="delete_package" class="btn btn-outline-danger border-0">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal Add/Edit Package -->
<div class="modal fade" id="addPackageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card-premium border-0">
            <form method="POST">
                <?php csrf_field(); ?>
                <input type="hidden" name="package_id" id="package_id">
                <div class="modal-header-premium d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold" id="modalTitle">Tambah Paket Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body-premium">
                    <div class="mb-3">
                        <label class="form-label text-muted-custom fw-bold small text-uppercase">Nama Paket</label>
                        <input type="text" name="name" id="p_name" class="form-control" placeholder="ex: Corporate" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted-custom fw-bold small text-uppercase">Sub-judul / Deskripsi Pendek</label>
                        <input type="text" name="subtitle" id="p_subtitle" class="form-control" placeholder="ex: SECURITY GEDUNG & INDUSTRI" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted-custom fw-bold small text-uppercase">Fitur-fitur (Pisahkan dengan | )</label>
                        <textarea name="features" id="p_features" class="form-control" rows="4" placeholder="Fitur 1 | Fitur 2 | Fitur 3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted-custom fw-bold small text-uppercase">Label Badge</label>
                            <input type="text" name="badge_label" id="p_badge" class="form-control" placeholder="ex: POPULER">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted-custom fw-bold small text-uppercase">Teks Tombol CTA</label>
                            <input type="text" name="cta_text" id="p_cta" class="form-control" value="Dapatkan Penawaran" required>
                        </div>
                    </div>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="is_featured" id="p_featured">
                        <label class="form-check-label fw-bold small">Tandai sebagai Paket Unggulan</label>
                    </div>
                </div>
                <div class="modal-footer-premium d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-premium btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="save_package" class="btn btn-premium btn-premium-primary">Simpan Paket</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editPackage(pkg) {
    document.getElementById('modalTitle').innerText = 'Edit Paket: ' + pkg.name;
    document.getElementById('package_id').value = pkg.id;
    document.getElementById('p_name').value = pkg.name;
    document.getElementById('p_subtitle').value = pkg.subtitle;
    document.getElementById('p_features').value = pkg.features;
    document.getElementById('p_badge').value = pkg.badge_label;
    document.getElementById('p_cta').value = pkg.cta_text;
    document.getElementById('p_featured').checked = (pkg.is_featured == 1);
    
    var myModal = new bootstrap.Modal(document.getElementById('addPackageModal'));
    myModal.show();
}

// Reset modal on close
document.getElementById('addPackageModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('modalTitle').innerText = 'Tambah Paket Baru';
    document.getElementById('package_id').value = '';
    document.querySelector('form').reset();
});
</script>

<?php include 'includes/admin_footer.php'; ?>
