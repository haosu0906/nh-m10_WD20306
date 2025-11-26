<?php

class AuthController
{
    public function showGuideLogin()
    {
        $errors = flash('errors') ?? [];
        $old = flash('old') ?? [];
        require __DIR__ . '/../views/auth/guide_login.php';
    }

    public function handleGuideLogin()
    {
        require_once __DIR__ . '/../models/UserModel.php';
        $userModel = new UserModel();

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

        if (!$user) {
            $errors['login'] = 'Thông tin đăng nhập không chính xác.';
            redirect_with_flash(BASE_URL . '?r=guide_login', $errors, ['email' => $email]);
        }

        $role = strtolower((string)($user['role'] ?? ''));

        // Tạm thời: bỏ qua kiểm tra mật khẩu, chỉ kiểm tra quyền theo role
        // - guide/hdv: vào Portal HDV
        // - admin: vào trang quản trị (home)
        if (!in_array($role, ['guide', 'hdv', 'admin'], true)) {
            $errors['login'] = 'Tài khoản này không được phép đăng nhập vào hệ thống hiện tại.';
            redirect_with_flash(BASE_URL . '?r=guide_login', $errors, ['email' => $email]);
        }

        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['role'] = $user['role'];

        if (in_array($role, ['guide', 'hdv'], true)) {
            header('Location: ' . BASE_URL . '?r=guide_dashboard');
        } else {
            // admin
            header('Location: ' . BASE_URL . '?r=home');
        }
        exit;
    }

    public function guideLogout()
    {
        unset($_SESSION['user_id'], $_SESSION['role']);
        header('Location: ' . BASE_URL . '?r=guide_login');
        exit;
    }
}
