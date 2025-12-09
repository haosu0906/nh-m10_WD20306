<?php
require_once __DIR__ . '/../assets/configs/helper.php';
require_once __DIR__ . '/../models/UserModel.php';

class AuthController
{
    // Hiển thị form login HDV
    public function showGuideLogin()
    {
        // Nếu HDV đã login, chuyển thẳng dashboard
        if (isset($_SESSION['user_id']) && ($_SESSION['role'] ?? '') === 'guide') {
            header('Location: ' . BASE_URL . '?r=guide_dashboard');
            exit;
        }

        $errors = flash('errors') ?? [];
        $old = flash('old') ?? [];
        require __DIR__ . '/../views/auth/guide_login.php';
    }

    // Xử lý login HDV
    public function handleGuideLogin()
    {
        $userModel = new UserModel();
        if (!csrf_validate($_POST['csrf_token'] ?? '')) {
            redirect_with_flash(BASE_URL . '?r=guide_login', ['login' => 'CSRF token không hợp lệ.'], $_POST);
        }
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $errors = [];

        if ($email === '' || $password === '') {
            $errors['login'] = 'Vui lòng nhập đầy đủ email và mật khẩu.';
        }

        if (!empty($errors)) {
            redirect_with_flash(BASE_URL . '?r=guide_login', $errors, $_POST);
        }

        $user = $userModel->findByEmail($email);

        if (!$user || strtolower($user['role'] ?? '') !== 'guide') {
            $errors['login'] = 'Thông tin đăng nhập không chính xác hoặc không phải HDV.';
            redirect_with_flash(BASE_URL . '?r=guide_login', $errors, ['email' => $email]);
        }

        // Kiểm tra mật khẩu (giả sử lưu hash)
        if (!password_verify($password, $user['password'])) {
            $errors['login'] = 'Mật khẩu không đúng.';
            redirect_with_flash(BASE_URL . '?r=guide_login', $errors, ['email' => $email]);
        }

        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['role'] = $user['role'];
        flash_set('success', 'Đăng nhập HDV thành công');
        header('Location: ' . BASE_URL . '?r=guide_dashboard');
        exit;
    }

    // Logout HDV
    public function guideLogout()
    {
        unset($_SESSION['user_id'], $_SESSION['role']);
        header('Location: ' . BASE_URL . '?r=guide_login');
        exit;
    }

    // Hiển thị form login Admin
    public function showAdminLogin()
    {
        if (isset($_SESSION['user_id']) && ($_SESSION['role'] ?? '') === 'admin') {
            header('Location: ' . BASE_URL . '?r=home');
            exit;
        }

        $errors = flash('errors') ?? [];
        $old = flash('old') ?? [];
        require __DIR__ . '/../views/auth/login_admin.php';
    }

    // Xử lý login Admin
    public function handleAdminLogin()
    {
        $userModel = new UserModel();
        if (!csrf_validate($_POST['csrf_token'] ?? '')) {
            redirect_with_flash(BASE_URL . '?r=admin_login', ['login' => 'CSRF token không hợp lệ.'], $_POST);
        }
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $errors = [];

        if ($email === '' || $password === '') {
            $errors['login'] = 'Vui lòng nhập đầy đủ email và mật khẩu.';
        }

        if (!empty($errors)) {
            redirect_with_flash(BASE_URL . '?r=admin_login', $errors, $_POST);
        }

        $user = $userModel->findByEmail($email);

        if (!$user || strtolower($user['role'] ?? '') !== 'admin') {
            $errors['login'] = 'Thông tin đăng nhập không chính xác hoặc không phải admin.';
            redirect_with_flash(BASE_URL . '?r=admin_login', $errors, ['email' => $email]);
        }

        // Kiểm tra mật khẩu
        if (!password_verify($password, $user['password'])) {
            $errors['login'] = 'Mật khẩu không đúng.';
            redirect_with_flash(BASE_URL . '?r=admin_login', $errors, ['email' => $email]);
        }

        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['role'] = $user['role'];
        flash_set('success', 'Đăng nhập admin thành công');
        header('Location: ' . BASE_URL . '?r=home');
        exit;
    }

    // Logout Admin
    public function adminLogout()
    {
        unset($_SESSION['user_id'], $_SESSION['role']);
        header('Location: ' . BASE_URL . '?r=admin_login');
        exit;
    }
}
