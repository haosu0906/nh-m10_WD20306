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
            if (empty($files['name'][$i])) {
                continue;
            }
            $single = [
                'name' => $files['name'][$i],
                'type' => $files['type'][$i] ?? '',
                'tmp_name' => $files['tmp_name'][$i] ?? '',
                'error' => $files['error'][$i] ?? 0,
                'size' => $files['size'][$i] ?? 0,
            ];
            if ($single['error'] === UPLOAD_ERR_OK) {
                $path = upload_file($folder, $single);
                if ($path) {
                    $paths[] = $path;
                }
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
        if (!empty($errors)) {
            flash('errors', $errors);
        }

        if (!empty($old)) {
            flash('old', $old);
        }

        header('Location: ' . $url);
        exit;
    }
}