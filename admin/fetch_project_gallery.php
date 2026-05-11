<?php
require_once '../includes/db_connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM project_images WHERE project_id = ? ORDER BY created_at DESC");
    $stmt->execute([$id]);
    $images = $stmt->fetchAll();

    if (empty($images)) {
        echo '<div class="col-12 text-center py-5 opacity-50">Belum ada foto di galeri ini.</div>';
    } else {
        foreach ($images as $img) {
            echo '
            <div class="col-md-4 col-6 position-relative gallery-item">
                <div class="rounded-4 overflow-hidden shadow-sm border" style="height: 120px;">
                    <img src="../' . $img['image_path'] . '" class="w-100 h-100 object-fit-cover">
                </div>
                <button onclick="deleteGalleryImage(' . $img['id'] . ', ' . $id . ')" 
                        class="btn btn-danger btn-sm rounded-circle position-absolute top-0 end-0 m-2 shadow"
                        style="width: 25px; height: 25px; padding: 0;">
                    <i class="bi bi-x small"></i>
                </button>
            </div>';
        }
    }
}
