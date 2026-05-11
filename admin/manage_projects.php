<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Self-healing database: Create project_images table if not exists
$pdo->exec("CREATE TABLE IF NOT EXISTS project_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Handle Delete
if (isset($_GET['delete'])) {
    if (!isset($_GET['token']) || !verify_csrf_token($_GET['token'])) {
        die("Invalid CSRF Token");
    }
    
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: manage_projects.php?deleted=1");
    exit();
}

// Fetch Projects
$projects = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC")->fetchAll();

include 'includes/admin_header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-journal-check me-2 text-warning"></i> Case Studies</h2>
        <button class="btn btn-premium btn-premium-primary" data-bs-toggle="modal" data-bs-target="#addProjectModal">
            <i class="bi bi-plus-lg me-1"></i> Tambah Proyek
        </button>
    </div>

    <div class="row g-4">
        <?php foreach ($projects as $project): ?>
        <div class="col-lg-4 col-md-6">
            <div class="card-premium h-100">
                <div class="position-relative">
                    <img src="../<?php echo $project['image']; ?>" class="card-img-top" style="height: 220px; object-fit: cover;">
                    <div class="position-absolute top-0 start-0 m-3">
                        <span class="badge badge-premium bg-warning text-dark"><?php echo $project['category']; ?></span>
                    </div>
                </div>
                <div class="card-body-premium">
                    <h5 class="fw-bold mb-3"><?php echo htmlspecialchars($project['title']); ?></h5>
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <span class="badge badge-premium bg-light border text-muted">IR: <?php echo htmlspecialchars($project['incident_rate']); ?></span>
                        <span class="badge badge-premium bg-light border text-muted"><?php echo htmlspecialchars($project['patrol_status']); ?></span>
                    </div>
                    <div class="p-3 bg-app rounded-3 border border-dashed">
                        <small class="text-muted-custom fw-bold small text-uppercase d-block mb-1" style="font-size: 0.65rem;">Outcome:</small>
                        <p class="small mb-0 lh-base" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            <?php echo htmlspecialchars($project['result']); ?>
                        </p>
                    </div>
                </div>
                <div class="card-footer-premium d-flex gap-2">
                    <button class="btn btn-premium btn-light border py-2" onclick="manageGallery(<?php echo $project['id']; ?>, '<?php echo addslashes($project['title']); ?>')">
                        <i class="bi bi-images text-warning"></i>
                    </button>
                    <button class="btn btn-premium btn-light border flex-grow-1 py-2" onclick="editProject(<?php echo htmlspecialchars(json_encode($project), ENT_QUOTES, 'UTF-8'); ?>)">
                        <i class="bi bi-pencil-square text-primary"></i> Edit
                    </button>
                    <a href="?delete=<?php echo $project['id']; ?>&token=<?php echo generate_csrf_token(); ?>" class="btn btn-premium btn-light border py-2" onclick="return confirm('Hapus proyek ini?')">
                        <i class="bi bi-trash text-danger"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal Add Project -->
<div class="modal fade" id="addProjectModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content card-premium border-0">
            <form action="save_project.php" method="POST" enctype="multipart/form-data">
                <?php require_once '../includes/functions.php'; echo csrf_field(); ?>
                <div class="modal-header-premium d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold">Tambah Proyek Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body-premium">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Judul Proyek / Klien</label>
                            <input type="text" name="title" class="form-control" placeholder="ex: Kawasan Industri Srengseng" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Kategori</label>
                            <select name="category" class="form-select">
                                <option value="INDUSTRIAL">INDUSTRIAL</option>
                                <option value="RESIDENTIAL">RESIDENTIAL</option>
                                <option value="CORPORATE">CORPORATE</option>
                                <option value="EVENT">EVENT</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Media Image</label>
                            <input type="file" name="project_image" class="form-control" accept="image/*">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Incident Rate Target</label>
                            <input type="text" name="incident_rate" class="form-control" placeholder="0%">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Patrol Status</label>
                            <input type="text" name="patrol_status" class="form-control" placeholder="24/7 Monitoring">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase mb-2 text-danger">Tantangan (Problem)</label>
                            <textarea name="problem" class="form-control" rows="4" placeholder="Jelaskan masalah awal..."></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase mb-2 text-success">Solusi JSMP (Solution)</label>
                            <textarea name="solution" class="form-control" rows="4" placeholder="Jelaskan langkah pengamanan..."></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase mb-2 text-warning">Hasil Akhir (Result)</label>
                            <textarea name="result" class="form-control" rows="4" placeholder="Jelaskan dampak positifnya..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer-premium d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-premium btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-premium btn-premium-primary">Simpan Proyek</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Project -->
<div class="modal fade" id="editProjectModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content card-premium border-0">
            <form action="update_project.php" method="POST" enctype="multipart/form-data">
                <?php csrf_field(); ?>
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-header-premium d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold">Edit Project Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body-premium">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Judul Proyek</label>
                            <input type="text" name="title" id="edit_title" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Kategori</label>
                            <select name="category" id="edit_category" class="form-select">
                                <option value="INDUSTRIAL">INDUSTRIAL</option>
                                <option value="RESIDENTIAL">RESIDENTIAL</option>
                                <option value="CORPORATE">CORPORATE</option>
                                <option value="EVENT">EVENT</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Update Media</label>
                            <input type="file" name="project_image" class="form-control" accept="image/*">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Incident Rate</label>
                            <input type="text" name="incident_rate" id="edit_incident_rate" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted-custom fw-bold small text-uppercase mb-2">Patrol Status</label>
                            <input type="text" name="patrol_status" id="edit_patrol_status" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase mb-2 text-danger">Masalah (Problem)</label>
                            <textarea name="problem" id="edit_problem" class="form-control" rows="4"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase mb-2 text-success">Solusi (Solution)</label>
                            <textarea name="solution" id="edit_solution" class="form-control" rows="4"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase mb-2 text-warning">Hasil (Result)</label>
                            <textarea name="result" id="edit_result" class="form-control" rows="4"></textarea>
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

<!-- Modal Gallery -->
<div class="modal fade" id="galleryModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content card-premium border-0">
            <div class="modal-header-premium d-flex justify-content-between align-items-center">
                <h5 class="modal-title fw-bold" id="galleryTitle">Project Gallery</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body-premium">
                <!-- Upload Form -->
                <form action="save_project_image.php" method="POST" enctype="multipart/form-data" class="mb-4 p-3 bg-app rounded-4 border">
                    <?php csrf_field(); ?>
                    <input type="hidden" name="project_id" id="gallery_project_id">
                    <label class="form-label fw-bold small text-uppercase mb-3">Add New Photo to Gallery</label>
                    <div class="input-group">
                        <input type="file" name="project_photo" class="form-control" accept="image/*" required>
                        <button type="submit" class="btn btn-premium btn-premium-primary">Upload</button>
                    </div>
                </form>

                <!-- Gallery Preview Area -->
                <div id="galleryContainer" class="row g-3">
                    <!-- Images will be loaded here via AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function editProject(data) {
    document.getElementById('edit_id').value = data.id;
    document.getElementById('edit_title').value = data.title;
    document.getElementById('edit_category').value = data.category;
    document.getElementById('edit_incident_rate').value = data.incident_rate;
    document.getElementById('edit_patrol_status').value = data.patrol_status;
    document.getElementById('edit_problem').value = data.problem;
    document.getElementById('edit_solution').value = data.solution;
    document.getElementById('edit_result').value = data.result;
    
    new bootstrap.Modal(document.getElementById('editProjectModal')).show();
}

function manageGallery(id, title) {
    document.getElementById('gallery_project_id').value = id;
    document.getElementById('galleryTitle').innerText = 'Gallery: ' + title;
    
    const container = document.getElementById('galleryContainer');
    container.innerHTML = '<div class="col-12 text-center py-5"><div class="spinner-border text-warning"></div></div>';
    
    fetch(`fetch_project_gallery.php?id=${id}`)
        .then(response => response.text())
        .then(html => {
            container.innerHTML = html;
        });

    new bootstrap.Modal(document.getElementById('galleryModal')).show();
}

function deleteGalleryImage(imgId, projectId) {
    if(confirm('Hapus foto ini dari galeri?')) {
        fetch(`delete_project_image.php?id=${imgId}`)
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    manageGallery(projectId, document.getElementById('galleryTitle').innerText.replace('Gallery: ', ''));
                }
            });
    }
}
</script>

<?php include 'includes/admin_footer.php'; ?>
