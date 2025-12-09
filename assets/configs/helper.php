<?php
if (!function_exists('debug')) {
    function debug($data)
    {
        echo '<pre>';
        print_r($data);
        die;
    }
}

if (!function_exists('ensure_upload_path')) {
    function ensure_upload_path($folder)
    {
        $path = rtrim(PATH_ASSETS_UPLOADS, '/') . '/' . trim($folder, '/');
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        return trim($folder, '/');
    }
}

if (!function_exists('upload_file')) {
    function upload_file($folder, $file)
    {
        if (empty($file['name']) || ($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            return null;
        }

        $folder = ensure_upload_path($folder);
        $safeName = preg_replace('/[^a-zA-Z0-9_\.\-]/', '_', $file['name']);
        $targetFile = $folder . '/' . time() . '-' . $safeName;

        if (move_uploaded_file($file["tmp_name"], PATH_ASSETS_UPLOADS . $targetFile)) {
            return $targetFile;
        }

        throw new Exception('Upload file không thành công!');
    }
}

if (!function_exists('upload_multiple_files')) {
    function upload_multiple_files($folder, $files)
    {
        $paths = [];
        if (!isset($files['name']) || !is_array($files['name'])) {
            return $paths;
        }

        $count = count($files['name']);
        for ($i = 0; $i < $count; $i++) {
            if (empty($files['name'][$i])) continue;
            $single = [
                'name' => $files['name'][$i],
                'type' => $files['type'][$i] ?? '',
                'tmp_name' => $files['tmp_name'][$i] ?? '',
                'error' => $files['error'][$i] ?? 0,
                'size' => $files['size'][$i] ?? 0,
            ];
            if ($single['error'] === UPLOAD_ERR_OK) {
                $path = upload_file($folder, $single);
                if ($path) $paths[] = $path;
            }
        }
        return $paths;
    }
}

if (!function_exists('flash')) {
    function flash($key, $value = null)
    {
        if ($value === null) {
            if (isset($_SESSION['flash'][$key])) {
                $data = $_SESSION['flash'][$key];
                unset($_SESSION['flash'][$key]);
                return $data;
            }
            return null;
        }
        $_SESSION['flash'][$key] = $value;
    }
}

if (!function_exists('redirect_with_flash')) {
    function redirect_with_flash($url, $errors = [], $old = [])
    {
        if (!empty($errors)) flash('errors', $errors);
        if (!empty($old)) flash('old', $old);
        header('Location: ' . $url);
        exit;
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('csrf_validate')) {
    function csrf_validate($token)
    {
        $t = (string)($token ?? '');
        $s = (string)($_SESSION['csrf_token'] ?? '');
        return $t !== '' && hash_equals($s, $t);
    }
}

if (!function_exists('send_email')) {
    function send_email($to, $subject, $html, $text = '')
    {
        $fromEmail = defined('MAIL_FROM_EMAIL') ? MAIL_FROM_EMAIL : 'no-reply@localhost';
        $fromName = defined('MAIL_FROM_NAME') ? MAIL_FROM_NAME : 'TripMate';
        $headers = [];
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=UTF-8';
        $headers[] = 'From: ' . sprintf('"%s" <%s>', $fromName, $fromEmail);
        $headers[] = 'Reply-To: ' . sprintf('"%s" <%s>', $fromName, $fromEmail);
        $headers[] = 'X-Mailer: PHP/' . phpversion();
        $headersStr = implode("\r\n", $headers);
        if ($html === '' && $text !== '') {
            $html = nl2br(htmlspecialchars($text, ENT_QUOTES, 'UTF-8'));
        }
        return mail($to, $subject, $html, $headersStr);
    }
}
