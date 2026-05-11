<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Handle Save Settings
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Validation
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("Invalid CSRF Token");
    }
    
    foreach ($_POST['settings'] as $key => $value) {
        $clean_value = sanitize_input($value);
        $clean_key = sanitize_input($key);
        $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
        $stmt->execute([$clean_value, $clean_key]);
    }
    header("Location: manage_settings.php?success=1");
    exit();
}

// Fetch all settings
$settings_raw = $pdo->query("SELECT * FROM settings")->fetchAll();
$settings = [];
foreach ($settings_raw as $row) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

include 'includes/admin_header.php';
?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <h2 class="fw-bold mb-4"><i class="bi bi-gear-fill me-2 text-warning"></i> Pengaturan Website</h2>
            
            <form method="POST">
                <?php csrf_field(); ?>
                <div class="row g-4">
                    <!-- Contact & Location -->
                    <div class="col-md-7">
                        <div class="card-premium h-100">
                            <div class="card-header-premium">
                                <h5 class="card-title mb-0">Identitas & Kontak</h5>
                            </div>
                            <div class="card-body-premium">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Nama Perusahaan</label>
                                        <input type="text" name="settings[site_title]" class="form-control" value="<?php echo htmlspecialchars($settings['site_title'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Email Official</label>
                                        <input type="email" name="settings[contact_email]" class="form-control" value="<?php echo htmlspecialchars($settings['contact_email'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Phone / WA</label>
                                        <input type="text" name="settings[contact_phone]" class="form-control" value="<?php echo htmlspecialchars($settings['contact_phone'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Jam Kerja</label>
                                        <input type="text" name="settings[contact_hours]" class="form-control" value="<?php echo htmlspecialchars($settings['contact_hours'] ?? ''); ?>">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Google Maps URL</label>
                                        <input type="text" name="settings[contact_maps]" class="form-control" value="<?php echo htmlspecialchars($settings['contact_maps'] ?? ''); ?>">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Alamat Kantor Pusat</label>
                                        <textarea name="settings[contact_address]" class="form-control" rows="3"><?php echo htmlspecialchars($settings['contact_address'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SEO Settings -->
                    <div class="col-md-5">
                        <div class="card-premium h-100">
                            <div class="card-header-premium">
                                <h5 class="card-title mb-0">Optimasi SEO</h5>
                            </div>
                            <div class="card-body-premium">
                                <div class="mb-4">
                                    <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Meta Description</label>
                                    <textarea name="settings[meta_description]" class="form-control" rows="5" placeholder="Tulis deskripsi untuk pencarian Google..."><?php echo htmlspecialchars($settings['meta_description'] ?? ''); ?></textarea>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Meta Keywords</label>
                                    <input type="text" name="settings[meta_keywords]" class="form-control" value="<?php echo htmlspecialchars($settings['meta_keywords'] ?? ''); ?>" placeholder="keyword1, keyword2...">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 text-center mt-5">
                        <button type="submit" class="btn btn-premium btn-premium-primary px-5">
                            <i class="bi bi-save2 me-2"></i> Simpan Seluruh Pengaturan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>
