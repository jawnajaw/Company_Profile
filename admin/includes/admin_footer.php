</div> <!-- End of main-content (opened in header) -->

<!-- Premium Toast Container -->
<div class="toast-container" id="toastContainer"></div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
/**
 * JSMP Premium Toast Notification System
 */
function showToast(title, message, type = 'success') {
    const container = document.getElementById('toastContainer');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = `premium-toast toast-${type}`;
    
    let icon = 'bi-check-circle-fill';
    if(type === 'error') icon = 'bi-x-circle-fill';
    if(type === 'warning') icon = 'bi-exclamation-triangle-fill';
    if(type === 'info') icon = 'bi-info-circle-fill';

    toast.innerHTML = `
        <div class="toast-icon">
            <i class="bi ${icon}"></i>
        </div>
        <div class="toast-content">
            <div class="toast-title">${title}</div>
            <div class="toast-msg">${message}</div>
        </div>
    `;

    container.appendChild(toast);
    
    // Trigger animation
    setTimeout(() => toast.classList.add('show'), 10);

    // Auto remove after 4 seconds
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 500);
    }, 4000);
}

/**
 * Auto-detect PHP Flash Messages from URL or Variables
 */
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    
    // URL-based Toasts
    if(urlParams.has('success')) showToast('Berhasil!', 'Data telah berhasil diperbarui.', 'success');
    if(urlParams.has('deleted')) showToast('Terhapus', 'Data telah dihapus dari database.', 'warning');
    if(urlParams.has('error')) showToast('Kesalahan', 'Gagal memproses permintaan.', 'error');
    if(urlParams.has('updated')) showToast('Simpan', 'Perubahan data telah disimpan.', 'success');

    // PHP Variable-based Toasts (Direct render)
    <?php if (isset($success) && $success): ?>
        showToast('Berhasil!', '<?php echo addslashes($success); ?>', 'success');
    <?php endif; ?>
    
    <?php if (isset($error) && $error): ?>
        showToast('Kesalahan!', '<?php echo addslashes($error); ?>', 'error');
    <?php endif; ?>

    <?php if (isset($_SESSION['toast_msg'])): ?>
        showToast('<?php echo $_SESSION['toast_title'] ?? "Info"; ?>', '<?php echo addslashes($_SESSION['toast_msg']); ?>', '<?php echo $_SESSION['toast_type'] ?? "info"; ?>');
        <?php unset($_SESSION['toast_msg'], $_SESSION['toast_title'], $_SESSION['toast_type']); ?>
    <?php endif; ?>
});
</script>
</body>
</html>
