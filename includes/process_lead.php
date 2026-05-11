<?php
require_once 'db_connect.php';
require_once 'functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $phone = sanitize_input($_POST['phone']);
    $service = sanitize_input($_POST['service']);
    $message = sanitize_input($_POST['message']);

    try {
        // 1. Simpan ke Database (Mini CRM)
        $sql = "INSERT INTO leads (name, email, phone, service_requested, message, status) VALUES (?, ?, ?, ?, ?, 'New')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $email, $phone, $service, $message]);

        // 2. Siapkan Pesan WhatsApp
        $admin_phone = get_setting('contact_phone'); // Ambil dari setting
        $admin_phone = preg_replace('/[^0-9]/', '', $admin_phone); // Bersihkan nomor
        
        // Format Pesan
        $wa_message = "*Request Survey / Penawaran Baru (JSMP)*\n\n";
        $wa_message .= "Nama: $name\n";
        $wa_message .= "Email: $email\n";
        $wa_message .= "Layanan: $service\n";
        $wa_message .= "Pesan: $message\n\n";
        $wa_message .= "Sent via Website PT JSMP";

        $wa_url = "https://api.whatsapp.com/send?phone=" . $admin_phone . "&text=" . urlencode($wa_message);

        echo json_encode([
            'status' => 'success',
            'message' => 'Permintaan Anda telah kami terima.',
            'wa_url' => $wa_url
        ]);

    } catch (PDOException $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal mengirim permintaan: ' . $e->getMessage()
        ]);
    }
}
?>
