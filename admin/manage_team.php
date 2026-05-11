<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Handle Delete
if (isset($_GET['delete'])) {
    if (!isset($_GET['token']) || !verify_csrf_token($_GET['token'])) {
        die("Invalid CSRF Token");
    }
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM team WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: manage_team.php?deleted=1");
    exit();
}

$team = get_team_members();
include 'includes/admin_header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-person-badge-fill me-2 text-warning"></i> Struktur Tim</h2>
        <button class="btn btn-premium btn-premium-primary" data-bs-toggle="modal" data-bs-target="#addTeamModal">
            <i class="bi bi-person-plus-fill me-1"></i> Tambah Anggota
        </button>
    </div>

    <div class="row g-4">
        <?php foreach ($team as $member): ?>
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card-premium h-100">
                <div class="position-relative">
                    <img src="../<?php echo $member['image']; ?>" class="card-img-top" style="height: 300px; object-fit: cover;">
                    <div class="position-absolute top-0 end-0 m-3">
                        <span class="badge badge-premium bg-dark text-warning border border-warning border-opacity-25"><?php echo $member['rank']; ?></span>
                    </div>
                </div>
                <div class="card-body-premium text-center">
                    <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($member['name']); ?></h5>
                    <p class="text-warning small fw-bold mb-3 text-uppercase letter-spacing-1"><?php echo htmlspecialchars($member['role']); ?></p>
                    <p class="text-muted-custom small mb-0 lh-base">
                        <?php echo htmlspecialchars($member['bio']); ?>
                    </p>
                </div>
                <div class="card-footer-premium d-flex gap-2">
                    <button class="btn btn-premium btn-light border flex-grow-1 py-2" onclick="editTeam(<?php echo htmlspecialchars(json_encode($member), ENT_QUOTES, 'UTF-8'); ?>)">
                        <i class="bi bi-pencil-square"></i> Edit
                    </button>
                    <a href="?delete=<?php echo $member['id']; ?>&token=<?php echo generate_csrf_token(); ?>" class="btn btn-premium btn-outline-danger border-0 py-2" onclick="return confirm('Hapus anggota ini?')">
                        <i class="bi bi-trash"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal Add Team -->
<div class="modal fade" id="addTeamModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content card-premium border-0">
            <form action="save_team.php" method="POST" enctype="multipart/form-data">
                <?php csrf_field(); ?>
                <div class="modal-header-premium d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold">Tambah Anggota Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body-premium">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Pangkat / Jabatan</label>
                            <input type="text" name="rank" class="form-control" placeholder="ex: Komisaris, Kepala Operasional" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Peran / Role</label>
                            <input type="text" name="role" class="form-control" placeholder="ex: Pengawas & Penasehat Perusahaan" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Biografi Singkat</label>
                            <textarea name="bio" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Foto Personil</label>
                            <input type="file" name="team_image" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="modal-footer-premium d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-premium btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-premium btn-premium-primary">Simpan Anggota</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Team -->
<div class="modal fade" id="editTeamModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content card-premium border-0">
            <form action="update_team.php" method="POST" enctype="multipart/form-data">
                <?php csrf_field(); ?>
                <input type="hidden" name="id" id="edit_team_id">
                <div class="modal-header-premium d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold">Edit Detail Anggota</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body-premium">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Nama Lengkap</label>
                            <input type="text" name="name" id="edit_team_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Pangkat / Jabatan</label>
                            <input type="text" name="rank" id="edit_team_rank" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Peran / Role</label>
                            <input type="text" name="role" id="edit_team_role" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Biografi Singkat</label>
                            <textarea name="bio" id="edit_team_bio" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Update Foto</label>
                            <input type="file" name="team_image" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="modal-footer-premium d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-premium btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-premium btn-premium-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editTeam(data) {
    document.getElementById('edit_team_id').value = data.id;
    document.getElementById('edit_team_name').value = data.name;
    document.getElementById('edit_team_rank').value = data.rank;
    document.getElementById('edit_team_role').value = data.role;
    document.getElementById('edit_team_bio').value = data.bio;
    
    new bootstrap.Modal(document.getElementById('editTeamModal')).show();
}
</script>

<?php include 'includes/admin_footer.php'; ?>
