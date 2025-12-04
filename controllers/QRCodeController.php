<?php
class QRCodeController {
    public function generate() {
        $data = $_GET['data'] ?? '';
        $size = (int)($_GET['size'] ?? 128);
        if ($size < 64) $size = 64; if ($size > 512) $size = 512;
        if ($data === '') {
            header('HTTP/1.1 400 Bad Request');
            echo 'Missing data';
            exit;
        }
        $remote = 'https://api.qrserver.com/v1/create-qr-code/?size=' . $size . 'x' . $size . '&data=' . urlencode($data);
        try {
            $img = @file_get_contents($remote);
            if ($img !== false) {
                header('Content-Type: image/png');
                echo $img;
                exit;
            }
        } catch (Exception $e) {}
        header('Location: ' . $remote);
        exit;
    }
}
?>
