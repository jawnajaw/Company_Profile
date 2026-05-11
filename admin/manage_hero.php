<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Self-healing database: Create hero_slides table if not exists
$pdo->exec("CREATE TABLE IF NOT EXISTS hero_slides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Fetch current hero settings
$hero = get_hero_settings();
$slides = $pdo->query("SELECT * FROM hero_slides ORDER BY id DESC")->fetchAll();

// Fix: if table is empty, initialize with defaults
if (!$hero) {
    $hero = [
        'headline' => 'PROFESIONALISME & KEAMANAN TERJAMIN',
        'subheadline' => 'Penyedia layanan keamanan profesional untuk korporat dan industri.',
        'badge_1' => '24/7 Monitoring',
        'badge_2' => 'Sertifikat ISO',
        'cta_text' => 'Pelajari Lebih Lanjut',
        'cta_link' => '#about',
        'image' => 'assets/img/hero-bg.jpg'
    ];
}

include 'includes/admin_header.php';
?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card-premium">
                <div class="card-header-premium d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-display me-2 text-warning"></i> Visual Hero Section</h5>
                </div>
                <div class="card-body-premium">
                    <?php if(isset($_GET['success'])): ?>
                        <div class="alert alert-success border-0 rounded-4 mb-4 shadow-sm">
                            <i class="bi bi-check-circle-fill me-2 text-success"></i> Konfigurasi hero berhasil diperbarui!
                        </div>
                    <?php endif; ?>

                    <form action="save_hero.php" method="POST" enctype="multipart/form-data">
                        <?php csrf_field(); ?>
                        <div class="row g-4">
                            <!-- Headline -->
                            <div class="col-12">
                                <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Headline Utama</label>
                                <textarea name="headline" class="form-control" rows="2" required placeholder="Gunakan <span> untuk teks emas"><?php echo htmlspecialchars($hero['headline']); ?></textarea>
                            </div>

                            <!-- Subheadline -->
                            <div class="col-12">
                                <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Subheadline / Deskripsi</label>
                                <textarea name="subheadline" class="form-control" rows="3" required><?php echo htmlspecialchars($hero['subheadline']); ?></textarea>
                            </div>

                            <!-- Badges -->
                            <div class="col-md-6">
                                <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Feature Badge 1</label>
                                <input type="text" name="badge_1" class="form-control" value="<?php echo htmlspecialchars($hero['badge_1']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Feature Badge 2</label>
                                <input type="text" name="badge_2" class="form-control" value="<?php echo htmlspecialchars($hero['badge_2']); ?>">
                            </div>

                            <!-- CTA Button -->
                            <div class="col-md-6">
                                <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">CTA Button Text</label>
                                <input type="text" name="cta_text" class="form-control" value="<?php echo htmlspecialchars($hero['cta_text']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">CTA Link Destination</label>
                                <input type="text" name="cta_link" class="form-control" value="<?php echo htmlspecialchars($hero['cta_link']); ?>">
                            </div>

                            <!-- Hero Image -->
                            <div class="col-12">
                                <label class="form-label text-muted-custom fw-bold small text-uppercase mb-3 d-block">Background Media</label>
                                <div class="row align-items-center">
                                    <div class="col-md-5 mb-3 mb-md-0">
                                        <div class="position-relative">
                                            <img id="imagePreview" src="../<?php echo $hero['image']; ?>" class="img-fluid rounded-4 shadow-sm border border-ui" style="max-height: 250px; width: 100%; object-fit: cover;">
                                            <div class="position-absolute top-0 end-0 m-2">
                                                <span class="badge badge-premium bg-dark text-white opacity-75">Preview</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <input type="file" name="hero_image" id="heroImageInput" class="form-control mb-2" accept="image/*">
                                        <div class="small text-muted-custom"><i class="bi bi-info-circle me-1"></i> Max 2MB. Recommendation: High resolution WEBP or JPG.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 text-center mt-5">
                                <button type="submit" class="btn btn-premium btn-premium-primary px-5">
                                    <i class="bi bi-save2-fill me-2"></i> Simpan Perubahan Teks Hero
                                </button>
                            </div>
                        </div>
                    </form>

                    <hr class="my-5 opacity-10">

                    <!-- HERO SLIDER MANAGEMENT -->
                    <div id="heroSliderManagement">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold mb-0 text-warning"><i class="bi bi-images me-2"></i> Hero Auto-Slider Gallery</h5>
                            <button class="btn btn-premium btn-light border py-1" onclick="document.getElementById('slideUploadInput').click()">
                                <i class="bi bi-plus-circle me-1"></i> Add New Slide
                            </button>
                        </div>

                        <form id="slideUploadForm" action="save_hero_slide.php" method="POST" enctype="multipart/form-data" class="d-none">
                            <?php csrf_field(); ?>
                            <input type="file" name="hero_slide" id="slideUploadInput" onchange="this.form.submit()" accept="image/*">
                        </form>

                        <div class="row g-3">
                            <?php if(empty($slides)): ?>
                                <div class="col-12 text-center py-4 bg-app rounded-4 border border-dashed opacity-50">
                                    <p class="mb-0">Belum ada slide tambahan. Website akan menggunakan gambar default.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach($slides as $slide): ?>
                                <div class="col-md-3 col-6">
                                    <div class="position-relative gallery-item rounded-4 overflow-hidden shadow-sm border border-ui" style="height: 120px;">
                                        <img src="../<?php echo $slide['image_path']; ?>" class="w-100 h-100 object-fit-cover">
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <a href="delete_hero_slide.php?id=<?php echo $slide['id']; ?>&token=<?php echo generate_csrf_token(); ?>" 
                                               class="btn btn-danger btn-sm rounded-circle py-0 px-1 shadow"
                                               onclick="return confirm('Hapus slide ini?')">
                                                <i class="bi bi-x"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Image Preview Logic
document.getElementById('heroImageInput').onchange = function (evt) {
    const [file] = this.files;
    if (file) {
        // Validation Size (2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file terlalu besar! Maksimal 2MB.');
            this.value = '';
            return;
        }
        document.getElementById('imagePreview').src = URL.createObjectURL(file);
    }
}
</script>

<?php include 'includes/admin_footer.php'; ?>
