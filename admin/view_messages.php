<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Fetch messages from leads table (since contact form sends here)
$stmt = $pdo->query("SELECT *, service_requested as service FROM leads ORDER BY created_at DESC");
$messages = $stmt->fetchAll();

include 'includes/admin_header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-chat-dots-fill me-2 text-warning"></i> Client Messages</h2>
            <p class="text-muted-custom small mb-0">Kelola pesan dan pertanyaan dari calon klien melalui website.</p>
        </div>
        <div class="badge badge-premium bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill fw-bold">
            Total: <?php echo count($messages); ?> Pesan
        </div>
    </div>

    <div class="card-premium border-0 shadow-sm">
        <div class="card-body-premium p-0">
            <div class="table-responsive">
                <table class="table table-premium table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Pengirim & Waktu</th>
                            <th>Kontak</th>
                            <th>Layanan</th>
                            <th>Pesan Singkat</th>
                            <th class="text-end pe-4">Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($messages)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="py-4">
                                    <i class="bi bi-chat-left-dots fs-1 d-block mb-3 opacity-25"></i>
                                    <p class="text-muted-custom mb-0">Belum ada pesan masuk dari klien.</p>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                        
                        <?php foreach ($messages as $msg): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-primary mb-1"><?php echo htmlspecialchars($msg['name']); ?></div>
                                <div class="x-small text-muted-custom">
                                    <i class="bi bi-calendar3 me-1"></i> <?php echo date('d M Y', strtotime($msg['created_at'])); ?> 
                                    <span class="mx-1">•</span> 
                                    <i class="bi bi-clock me-1"></i> <?php echo date('H:i', strtotime($msg['created_at'])); ?>
                                </div>
                            </td>
                            <td>
                                <div class="small fw-medium mb-1"><i class="bi bi-envelope me-1"></i> <?php echo htmlspecialchars($msg['email']); ?></div>
                                <div class="small text-muted-custom"><i class="bi bi-whatsapp me-1"></i> <?php echo htmlspecialchars($msg['phone']); ?></div>
                            </td>
                            <td>
                                <span class="badge badge-premium bg-light border text-dark fw-bold" style="font-size: 0.65rem;">
                                    <?php echo strtoupper(htmlspecialchars($msg['service'] ?? 'GENERAL')); ?>
                                </span>
                            </td>
                            <td>
                                <div class="small text-muted-custom text-truncate" style="max-width: 250px;">
                                    <?php echo htmlspecialchars($msg['message']); ?>
                                </div>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-premium btn-light border py-1 px-2" 
                                            onclick='viewMessage(<?php echo json_encode($msg); ?>)'
                                            title="Baca Detail">
                                        <i class="bi bi-eye text-primary"></i>
                                    </button>
                                    <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $msg['phone']); ?>?text=Halo%20<?php echo urlencode($msg['name']); ?>%2C%20kami%20dari%20PT%20JSMP%20ingin%20menanggapi%20pertanyaan%20Anda..." 
                                       target="_blank" 
                                       class="btn btn-premium btn-light border py-1 px-2"
                                       title="Balas via WhatsApp">
                                        <i class="bi bi-whatsapp text-success"></i>
                                    </a>
                                    <button class="btn btn-premium btn-light border py-1 px-2" 
                                            onclick="if(confirm('Hapus pesan ini?')) window.location.href='delete_message.php?id=<?php echo $msg['id']; ?>'"
                                            title="Hapus">
                                        <i class="bi bi-trash text-danger"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Pesan -->
<div class="modal fade" id="messageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card-premium border-0">
            <div class="modal-header-premium d-flex justify-content-between align-items-center">
                <h5 class="modal-title fw-bold">Detail Pesan Klien</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body-premium">
                <div class="mb-4">
                    <label class="form-label text-muted-custom fw-bold small text-uppercase mb-1">Pengirim</label>
                    <div class="h5 fw-bold mb-0" id="msg_name"></div>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <label class="form-label text-muted-custom fw-bold small text-uppercase mb-1">Email</label>
                        <div class="small fw-medium" id="msg_email"></div>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted-custom fw-bold small text-uppercase mb-1">WhatsApp</label>
                        <div class="small fw-medium" id="msg_phone"></div>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label text-muted-custom fw-bold small text-uppercase mb-1">Layanan yang Diminati</label>
                    <div><span class="badge bg-warning text-dark px-3" id="msg_service"></span></div>
                </div>
                <div class="p-3 bg-app rounded-4 border">
                    <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Isi Pesan:</label>
                    <p class="mb-0 lh-base" id="msg_body"></p>
                </div>
            </div>
            <div class="modal-footer-premium d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-premium btn-light border" data-bs-dismiss="modal">Tutup</button>
                <a href="#" id="wa_reply_btn" target="_blank" class="btn btn-premium btn-premium-primary">
                    <i class="bi bi-whatsapp me-2"></i>Balas Sekarang
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function viewMessage(msg) {
    document.getElementById('msg_name').innerText = msg.name;
    document.getElementById('msg_email').innerText = msg.email;
    document.getElementById('msg_phone').innerText = msg.phone;
    document.getElementById('msg_service').innerText = (msg.service || 'General Inquiry').toUpperCase();
    document.getElementById('msg_body').innerText = msg.message;
    
    const waLink = `https://wa.me/${msg.phone.replace(/[^0-9]/g, '')}?text=Halo%20${encodeURIComponent(msg.name)}%2C%20kami%20dari%20PT%20JSMP...`;
    document.getElementById('wa_reply_btn').href = waLink;

    new bootstrap.Modal(document.getElementById('messageModal')).show();
}
</script>

<?php include 'includes/admin_footer.php'; ?>
