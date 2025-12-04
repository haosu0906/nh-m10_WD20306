<?php

require_once __DIR__ . '/../models/GuideModel.php';

class GuidesController
{
    protected $model;
    protected $types = [
        'domestic' => 'HDV Nội địa',
        'international' => 'HDV Quốc tế'
    ];

    public function __construct()
    {
        $this->model = new GuideModel();
    }

    public function index()
    {
        $keyword = trim($_GET['q'] ?? '');
        $type = $_GET['type'] ?? '';
        $guides = $this->model->search($keyword, $type);
        $types = $this->types;

        require __DIR__ . '/../views/guides/index.php';
    }

    public function create()
    {
        $guide = null;
        $errors = flash('errors') ?? [];
        $old = flash('old') ?? [];
        $types = $this->types;

        require __DIR__ . '/../views/guides/form.php';
    }

    public function store()
    {
        $data = $this->filterData($_POST);
        $errors = $this->validate($data);

        if (!empty($errors)) {
            redirect_with_flash(BASE_URL . '?r=guides_create', $errors, $_POST);
        }

        // Upload avatar nếu có
        if (!empty($_FILES['avatar']['name'])) {
            $avatarPath = upload_file('guides', $_FILES['avatar']);
            if ($avatarPath) {
                $data['avatar'] = $avatarPath;
            }
        }

        $this->model->create($data);

        header('Location: ' . BASE_URL . '?r=guides');
        exit;
    }

    public function edit($id)
    {
        $guide = $this->model->find($id);
        if (!$guide) {
            header('Location: ' . BASE_URL . '?r=guides');
            exit;
        }

        $errors = flash('errors') ?? [];
        $old = flash('old') ?? [];
        $types = $this->types;

        require __DIR__ . '/../views/guides/form.php';
    }

    public function update($id)
    {
        $guide = $this->model->find($id);
        if (!$guide) {
            header('Location: ' . BASE_URL . '?r=guides');
            exit;
        }

        $data = $this->filterData($_POST);
        $errors = $this->validate($data);

        if (!empty($errors)) {
            redirect_with_flash(BASE_URL . '?r=guides_edit&id=' . $id, $errors, $_POST);
        }

        // Nếu có avatar mới, xoá avatar cũ rồi upload cái mới
        if (!empty($_FILES['avatar']['name'])) {

            // Xoá avatar cũ
            if (!empty($guide['avatar'])) {
                $oldAvatar = PATH_ASSETS_UPLOADS . $guide['avatar'];
                if (is_file($oldAvatar)) {
                    unlink($oldAvatar);
                }
            }

            // Upload avatar mới
            $avatarPath = upload_file('guides', $_FILES['avatar']);
            if ($avatarPath) {
                $data['avatar'] = $avatarPath;
            }
        }

        $this->model->update($id, $data);

        header('Location: ' . BASE_URL . '?r=guides');
        exit;
    }

    public function delete($id)
    {
        $guide = $this->model->find($id);

        if ($guide && !empty($guide['avatar'])) {
            $avatarPath = PATH_ASSETS_UPLOADS . $guide['avatar'];
            if (is_file($avatarPath)) {
                unlink($avatarPath);
            }
        }

        $this->model->delete($id);

        header('Location: ' . BASE_URL . '?r=guides');
        exit;
    }

    protected function filterData($input)
    {
        return [
            'full_name' => trim($input['full_name'] ?? ''),
            'phone' => trim($input['phone'] ?? ''),
            'email' => trim($input['email'] ?? ''),
            'identity_no' => trim($input['identity_no'] ?? ''),
            'certificate_no' => trim($input['certificate_no'] ?? ''),
            'guide_type' => $input['guide_type'] ?? 'domestic',
            'notes' => trim($input['notes'] ?? ''),
        ];
    }

    protected function validate($data)
    {
        $errors = [];

        if ($data['full_name'] === '') {
            $errors['full_name'] = 'Vui lòng nhập họ tên.';
        }

        if ($data['phone'] === '') {
            $errors['phone'] = 'Vui lòng nhập số điện thoại.';
        }

        if ($data['email'] === '') {
            $errors['email'] = 'Vui lòng nhập email.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không hợp lệ.';
        }

        if (!in_array($data['guide_type'], array_keys($this->types), true)) {
            $errors['guide_type'] = 'Loại HDV không hợp lệ.';
        }

        return $errors;
    }
}
