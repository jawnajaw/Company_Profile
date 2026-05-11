<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

$success = '';
$error = '';

// 1. HANDLE TEXT UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_text'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error = "Token keamanan tidak valid.";
    } else {
        $title = sanitize_input($_POST['title']);
        $desc = sanitize_input($_POST['description']);
        $s1v = sanitize_input($_POST['stat1_value']);
        $s1l = sanitize_input($_POST['stat1_label']);
        $s2v = sanitize_input($_POST['stat2_value']);
        $s2l = sanitize_input($_POST['stat2_label']);

        $sql = "UPDATE about_section SET title=?, description=?, stat1_value=?, stat1_label=?, stat2_value=?, stat2_label=? WHERE id=1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $desc, $s1v, $s1l, $s2v, $s2l]);
        $success = "Teks About berhasil diperbarui!";
    }
}

// 2. HANDLE IMAGE UPLOAD
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_image'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error = "Token keamanan tidak valid.";
    } else if (isset($_FILES['about_photo']) && $_FILES['about_photo']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $filename = $_FILES['about_photo']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $error = "Format file tidak didukung! Gunakan JPG, PNG, atau WEBP.";
        } else if ($_FILES['about_photo']['size'] > 5 * 1024 * 1024) {
            $error = "Ukuran file terlalu besar! Maksimal 5MB.";
        } else {
            $target_dir = "../assets/img/about/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

            $file_name = "about_" . time() . "_" . rand(100,999) . "." . $ext;
            $target_file = $target_dir . $file_name;
            $db_path = "assets/img/about/" . $file_name;

            if (move_uploaded_file($_FILES['about_photo']['tmp_name'], $target_file)) {
                $stmt = $pdo->prepare("INSERT INTO about_images (image_path) VALUES (?)");
                $stmt->execute([$db_path]);
                $success = "Foto berhasil ditambahkan ke slider!";
            }
        }
    }
}

// 3. HANDLE IMAGE DELETE (Converted to POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_img'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error = "Token keamanan tidak valid.";
    } else {
        $img_id = $_POST['img_id'];
        $stmt = $pdo->prepare("SELECT image_path FROM about_images WHERE id = ?");
        $stmt->execute([$img_id]);
        $img = $stmt->fetch();
        
        if ($img) {
            $file_path = "../" . $img['image_path'];
            if (file_exists($file_path)) unlink($file_path);
            
            $pdo->prepare("DELETE FROM about_images WHERE id = ?")->execute([$img_id]);
            $success = "Foto berhasil dihapus!";
        }
    }
}

// Fetch Data
$about = $pdo->query("SELECT * FROM about_section WHERE id=1")->fetch();
$images = $pdo->query("SELECT * FROM about_images ORDER BY sort_order ASC, id DESC")->fetchAll();

include 'includes/admin_header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-info-circle-fill me-2 text-warning"></i> Company Profile</h2>
    </div>
    
    <div class="row g-4">
        <!-- Text & Stats Management -->
        <div class="col-lg-7">
            <div class="card-premium h-100">
                <div class="card-header-premium">
                    <h5 class="card-title mb-0">Narasi & Statistik</h5>
                </div>
                <div class="card-body-premium">
                    
                    <form method="POST">
                        <?php csrf_field(); ?>
                        <div class="mb-4">
                            <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Main Title Section</label>
                            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($about['title']); ?>" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Company Narrative</label>
                            <textarea name="description" class="form-control" rows="8" required><?php echo htmlspecialchars($about['description']); ?></textarea>
                        </div>
                        
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="p-4 bg-app rounded-4 border border-ui">
                                    <label class="form-label text-warning fw-bold small text-uppercase mb-3">Key Metric 1</label>
                                    <div class="mb-3">
                                        <label class="small text-muted-custom fw-bold text-uppercase" style="font-size: 0.65rem;">Value (e.g. 500+)</label>
                                        <input type="text" name="stat1_value" class="form-control" value="<?php echo htmlspecialchars($about['stat1_value']); ?>">
                                    </div>
                                    <div>
                                        <label class="small text-muted-custom fw-bold text-uppercase" style="font-size: 0.65rem;">Label Description</label>
                                        <input type="text" name="stat1_label" class="form-control" value="<?php echo htmlspecialchars($about['stat1_label']); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-4 bg-app rounded-4 border border-ui">
                                    <label class="form-label text-warning fw-bold small text-uppercase mb-3">Key Metric 2</label>
                                    <div class="mb-3">
                                        <label class="small text-muted-custom fw-bold text-uppercase" style="font-size: 0.65rem;">Value (e.g. 25+)</label>
                                        <input type="text" name="stat2_value" class="form-control" value="<?php echo htmlspecialchars($about['stat2_value']); ?>">
                                    </div>
                                    <div>
                                        <label class="small text-muted-custom fw-bold text-uppercase" style="font-size: 0.65rem;">Label Description</label>
                                        <input type="text" name="stat2_label" class="form-control" value="<?php echo htmlspecialchars($about['stat2_label']); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" name="update_text" class="btn btn-premium btn-premium-primary px-5">
                            <i class="bi bi-save2-fill me-2"></i> Update Profile Narrative
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Image Slider Management -->
        <div class="col-lg-5">
            <div class="card-premium h-100">
                <div class="card-header-premium">
                    <h5 class="card-title mb-0">Activity Gallery</h5>
                </div>
                <div class="card-body-premium">
                    <form method="POST" enctype="multipart/form-data" class="mb-5">
                        <?php csrf_field(); ?>
                        <label class="form-label text-muted-custom fw-bold small text-uppercase mb-3">Upload Activity Photo</label>
                        <div class="input-group">
                            <input type="file" name="about_photo" class="form-control" accept="image/*" required>
                            <button type="submit" name="upload_image" class="btn btn-premium btn-premium-primary px-3">
                                <i class="bi bi-cloud-upload"></i>
                            </button>
                        </div>
                        <div class="small text-muted-custom mt-2"><i class="bi bi-info-circle me-1"></i> Max 5MB. WEBP recommended.</div>
                    </form>

                    <div class="row g-3">
                        <?php foreach($images as $img): ?>
                        <div class="col-md-6 col-xl-4">
                            <div class="position-relative overflow-hidden rounded-4 shadow-sm border border-ui group h-100">
                                <img src="../<?php echo $img['image_path']; ?>" class="img-fluid" style="height: 120px; width: 100%; object-fit: cover;">
                                <div class="position-absolute top-0 end-0 m-2">
                                    <form method="POST">
                                        <?php csrf_field(); ?>
                                        <input type="hidden" name="img_id" value="<?php echo $img['id']; ?>">
                                        <button type="submit" name="delete_img" class="btn btn-premium btn-outline-danger border-0 p-1" onclick="return confirm('Hapus foto ini?')">
                                            <i class="bi bi-x-circle-fill fs-5"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <?php if(empty($images)): ?>
                            <div class="col-12">
                                <div class="text-center py-5 bg-app rounded-4 border border-ui border-dashed">
                                    <i class="bi bi-images fs-1 text-muted opacity-25"></i>
                                    <p class="small text-muted-custom mt-2">No activity photos yet.</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>
