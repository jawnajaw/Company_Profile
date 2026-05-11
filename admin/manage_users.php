<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_role'] !== 'super_admin') {
    header("Location: dashboard.php");
    exit();
}

require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

$success = '';
$error = '';

// 1. HANDLE DELETE
if (isset($_POST['delete_user'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die("Invalid CSRF Token");
    }
    
    $id = $_POST['user_id'];
    // Prevent self-delete
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $u = $stmt->fetch();
    if ($u && $u['username'] === $_SESSION['admin_username']) {
        $error = "Anda tidak bisa menghapus akun Anda sendiri!";
    } else {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: manage_users.php?deleted=1");
        exit();
    }
}

// 2. HANDLE ADD/UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Validation
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("Invalid CSRF Token");
    }
    
    try {
        $fullname = sanitize_input($_POST['full_name']);
        $email = sanitize_input($_POST['email']);
        $username = sanitize_input($_POST['username']);
        $role = sanitize_input($_POST['role']);
        $status = sanitize_input($_POST['status']);
        $password = $_POST['password']; // Don't sanitize password as it will be hashed

        if (isset($_POST['user_id']) && !empty($_POST['user_id'])) {
            // UPDATE logic...
            $uid = $_POST['user_id'];
            if (!empty($password)) {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET full_name=?, email=?, username=?, role=?, status=?, password=? WHERE id=?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$fullname, $email, $username, $role, $status, $hashed, $uid]);
            } else {
                $sql = "UPDATE users SET full_name=?, email=?, username=?, role=?, status=? WHERE id=?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$fullname, $email, $username, $role, $status, $uid]);
            }
            $success = "User berhasil diperbarui!";
        } else {
            // INSERT NEW
            if (empty($password)) {
                $error = "Password wajib diisi untuk user baru!";
            } else {
                // Check duplicate
                $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
                $stmt->execute([$username, $email]);
                if ($stmt->fetch()) {
                    $error = "Username atau Email sudah terdaftar!";
                } else {
                    $hashed = password_hash($password, PASSWORD_DEFAULT);
                    $sql = "INSERT INTO users (full_name, email, username, password, role, status) VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$fullname, $email, $username, $hashed, $role, $status]);
                    $success = "User baru berhasil ditambahkan!";
                }
            }
        }
    } catch (PDOException $e) {
        $error = "Error Database: " . $e->getMessage();
    }
}

$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
include 'includes/admin_header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-person-gear me-2 text-warning"></i> Manajemen User</h2>
        <button class="btn btn-premium btn-premium-primary" data-bs-toggle="modal" data-bs-target="#userModal">
            <i class="bi bi-person-plus-fill me-1"></i> Tambah User Baru
        </button>
    </div>



    <div class="card-premium">
        <div class="card-body-premium p-0">
            <div class="table-responsive">
                <table class="table table-premium table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Profil Pengguna</th>
                            <th>Username & Email</th>
                            <th>Level Akses</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3 bg-warning text-dark fw-bold d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px; border: 2px solid var(--color-border);">
                                        <?php echo strtoupper(substr($u['full_name'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold"><?php echo htmlspecialchars($u['full_name']); ?></div>
                                        <div class="small text-muted-custom">Bergabung: <?php echo date('d M Y', strtotime($u['created_at'])); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-medium"><?php echo htmlspecialchars($u['username']); ?></div>
                                <div class="small text-muted-custom"><?php echo htmlspecialchars($u['email']); ?></div>
                            </td>
                            <td>
                                <?php if($u['role'] === 'super_admin'): ?>
                                    <span class="badge badge-premium bg-warning text-dark">Super Admin</span>
                                <?php else: ?>
                                    <span class="badge badge-premium bg-light border text-muted">Editor Konten</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($u['status'] === 'active'): ?>
                                    <span class="badge badge-premium bg-success text-white">Aktif</span>
                                <?php else: ?>
                                    <span class="badge badge-premium bg-danger text-white">Inaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-premium btn-light border py-1" onclick='editUser(<?php echo htmlspecialchars(json_encode($u), ENT_QUOTES, "UTF-8"); ?>)'>
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <?php if($u['username'] !== $_SESSION['admin_username']): ?>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                        <?php csrf_field(); ?>
                                        <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                        <button type="submit" name="delete_user" class="btn btn-premium btn-outline-danger border-0 py-1 ms-1"><i class="bi bi-trash"></i></button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal User -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card-premium border-0">
            <form method="POST">
                <?php csrf_field(); ?>
                <input type="hidden" name="user_id" id="user_id">
                <div class="modal-header-premium d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold" id="modalTitle">Konfigurasi Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body-premium">
                    <div class="mb-3">
                        <label class="form-label text-muted-custom fw-bold small text-uppercase">Nama Lengkap</label>
                        <input type="text" name="full_name" id="u_name" class="form-control" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted-custom fw-bold small text-uppercase">Username</label>
                            <input type="text" name="username" id="u_username" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted-custom fw-bold small text-uppercase">Email Official</label>
                            <input type="email" name="email" id="u_email" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted-custom fw-bold small text-uppercase">Password</label>
                        <input type="password" name="password" id="u_pass" class="form-control" placeholder="Kosongkan jika tidak diubah">
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted-custom fw-bold small text-uppercase">Role Access</label>
                            <select name="role" id="u_role" class="form-select" required>
                                <option value="admin">Editor Konten</option>
                                <option value="super_admin">Super Admin</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted-custom fw-bold small text-uppercase">Status Akun</label>
                            <select name="status" id="u_status" class="form-select" required>
                                <option value="active">Aktif</option>
                                <option value="inactive">Inaktif</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer-premium d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-premium btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-premium btn-premium-primary">Simpan User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editUser(user) {
    document.getElementById('modalTitle').innerText = 'Edit User: ' + user.username;
    document.getElementById('user_id').value = user.id;
    document.getElementById('u_name').value = user.full_name;
    document.getElementById('u_username').value = user.username;
    document.getElementById('u_email').value = user.email;
    document.getElementById('u_role').value = user.role;
    document.getElementById('u_status').value = user.status;
    document.getElementById('u_pass').placeholder = "Biarkan kosong jika tidak ganti";
    new bootstrap.Modal(document.getElementById('userModal')).show();
}
</script>

<?php include 'includes/admin_footer.php'; ?>
