<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Handle Update Status
if (isset($_POST['update_status'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die("Token keamanan tidak valid.");
    }
    $lead_id = $_POST['lead_id'];
    $new_status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE leads SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $lead_id]);
    header("Location: manage_leads.php?success=1");
    exit();
}

// Fetch Leads
$leads = $pdo->query("SELECT * FROM leads ORDER BY created_at DESC")->fetchAll();

include 'includes/admin_header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-people-fill me-2 text-warning"></i> Calon Klien (Leads)</h2>
    </div>

    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4"><i class="bi bi-check-circle me-2"></i> Status klien berhasil diperbarui!</div>
    <?php endif; ?>

    <div class="card-premium">
        <div class="card-body-premium p-0">
            <div class="table-responsive">
                <table class="table table-premium table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama</th>
                            <th>Kontak</th>
                            <th>Layanan</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($leads)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted-custom">Belum ada calon klien yang masuk.</td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($leads as $lead): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold"><?php echo date('d M Y', strtotime($lead['created_at'])); ?></div>
                                    <div class="small text-muted-custom"><?php echo date('H:i', strtotime($lead['created_at'])); ?></div>
                                </td>
                                <td>
                                    <div class="fw-bold"><?php echo htmlspecialchars($lead['name']); ?></div>
                                    <div class="small text-muted-custom"><?php echo htmlspecialchars($lead['email']); ?></div>
                                </td>
                                <td>
                                    <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $lead['phone']); ?>" target="_blank" class="btn btn-premium btn-light border py-1 px-3">
                                        <i class="bi bi-whatsapp text-success me-1"></i> WA
                                    </a>
                                </td>
                                <td><span class="badge badge-premium bg-warning text-dark"><?php echo htmlspecialchars($lead['service_requested']); ?></span></td>
                                <td>
                                    <?php 
                                    $status_bg = 'bg-secondary';
                                    if ($lead['status'] == 'New') $status_bg = 'bg-info';
                                    if ($lead['status'] == 'Contacted') $status_bg = 'bg-warning';
                                    if ($lead['status'] == 'Deal') $status_bg = 'bg-success';
                                    ?>
                                    <span class="badge badge-premium <?php echo $status_bg; ?> text-dark"><?php echo $lead['status']; ?></span>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-premium btn-light border py-1" data-bs-toggle="modal" data-bs-target="#viewLead<?php echo $lead['id']; ?>">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal Detail & Update -->
                            <div class="modal fade" id="viewLead<?php echo $lead['id']; ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content card-premium border-0">
                                        <form method="POST">
                                            <?php csrf_field(); ?>
                                            <div class="modal-header-premium d-flex justify-content-between align-items-center">
                                                <h5 class="modal-title fw-bold">Detail: <?php echo htmlspecialchars($lead['name']); ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body-premium">
                                                <div class="mb-4">
                                                    <label class="text-muted-custom fw-bold small text-uppercase mb-2 d-block">Pesan Masuk:</label>
                                                    <div class="p-3 bg-app rounded-3 border border-dashed">
                                                        <p class="mb-0 italic"><?php echo nl2br(htmlspecialchars($lead['message'])); ?></p>
                                                    </div>
                                                </div>
                                                <div class="mb-0">
                                                    <label class="text-muted-custom fw-bold small text-uppercase mb-2 d-block">Update Status:</label>
                                                    <input type="hidden" name="lead_id" value="<?php echo $lead['id']; ?>">
                                                    <select name="status" class="form-select">
                                                        <option value="New" <?php echo ($lead['status'] == 'New') ? 'selected' : ''; ?>>New</option>
                                                        <option value="Contacted" <?php echo ($lead['status'] == 'Contacted') ? 'selected' : ''; ?>>Contacted</option>
                                                        <option value="Deal" <?php echo ($lead['status'] == 'Deal') ? 'selected' : ''; ?>>Deal</option>
                                                        <option value="Spam" <?php echo ($lead['status'] == 'Spam') ? 'selected' : ''; ?>>Spam</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer-premium d-flex justify-content-end gap-2">
                                                <button type="button" class="btn btn-premium btn-light border" data-bs-dismiss="modal">Tutup</button>
                                                <button type="submit" name="update_status" class="btn btn-premium btn-premium-primary">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>
