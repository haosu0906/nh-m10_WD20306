<?php

class SupplierController
{
    protected $supplierModel;

    public function __construct()
    {
        $this->supplierModel = new TourSupplierModel();
    }

    public function index()
    {
        // Loại dịch vụ để filter (hotel, restaurant, transport, ticket, insurance, guide, meal, entertain, other)
        $currentType = isset($_GET['type']) ? trim((string)$_GET['type']) : '';

        $serviceTypes = [
            ''           => 'Tất cả',
            'hotel'      => 'Khách sạn',
            'restaurant' => 'Nhà hàng',
            'transport'  => 'Vận chuyển',
            'ticket'     => 'Vé tham quan',
            'insurance'  => 'Bảo hiểm',
        ];

        $suppliers = $this->supplierModel->allWithMeta();

        if ($currentType !== '') {
            $suppliers = array_values(array_filter($suppliers, function ($row) use ($currentType) {
                return isset($row['service_type']) && $row['service_type'] === $currentType;
            }));
        }

        require __DIR__ . '/../views/suppliers/index.php';
    }

    public function show($id)
    {
        $id = (int)$id;
        $supplier = $this->supplierModel->findWithExpenses($id);
        if (!$supplier) {
            header('Location: ' . BASE_URL . '?r=suppliers');
            exit;
        }
        require __DIR__ . '/../views/suppliers/show.php';
    }

    public function create()
    {
        $serviceTypes = [
            'hotel'      => 'Khách sạn',
            'restaurant' => 'Nhà hàng',
            'transport'  => 'Vận chuyển',
            'ticket'     => 'Vé tham quan',
            'insurance'  => 'Bảo hiểm',
        ];
        $supplier = null;
        require __DIR__ . '/../views/suppliers/form.php';
    }

    public function store()
    {
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'service_type' => trim($_POST['service_type'] ?? ''),
            'contact_person' => trim($_POST['contact_person'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'is_active' => (int)($_POST['is_active'] ?? 1),
        ];

        $errors = [];
        if ($data['name'] === '') $errors['name'] = 'Vui lòng nhập tên nhà cung cấp';
        $allowed = ['hotel','restaurant','transport','ticket','insurance'];
        if ($data['service_type'] === '' || !in_array($data['service_type'], $allowed, true)) {
            $errors['service_type'] = 'Loại dịch vụ không hợp lệ';
        }

        if (!empty($errors)) {
            flash('errors', $errors);
            flash('old', $_POST);
            header('Location: ' . BASE_URL . '?r=suppliers_create');
            exit;
        }

        $ok = $this->supplierModel->create($data);
        flash_set($ok ? 'success' : 'danger', $ok ? 'Đã thêm nhà cung cấp' : 'Không thể thêm nhà cung cấp');
        header('Location: ' . BASE_URL . '?r=suppliers');
        exit;
    }

    public function edit($id)
    {
        $id = (int)$id;
        $supplier = $this->supplierModel->findWithExpenses($id);
        if (!$supplier) {
            header('Location: ' . BASE_URL . '?r=suppliers');
            exit;
        }
        $serviceTypes = [
            'hotel'      => 'Khách sạn',
            'restaurant' => 'Nhà hàng',
            'transport'  => 'Vận chuyển',
            'ticket'     => 'Vé tham quan',
            'insurance'  => 'Bảo hiểm',
        ];
        require __DIR__ . '/../views/suppliers/form.php';
    }

    public function update($id)
    {
        $id = (int)$id;
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'service_type' => trim($_POST['service_type'] ?? ''),
            'contact_person' => trim($_POST['contact_person'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'is_active' => (int)($_POST['is_active'] ?? 1),
        ];
        $allowed = ['hotel','restaurant','transport','ticket','insurance'];
        if (!in_array($data['service_type'], $allowed, true)) {
            $data['service_type'] = 'hotel';
        }
        $ok = $this->supplierModel->update($id, $data);
        flash_set($ok ? 'success' : 'danger', $ok ? 'Đã cập nhật nhà cung cấp' : 'Không thể cập nhật nhà cung cấp');
        header('Location: ' . BASE_URL . '?r=suppliers');
        exit;
    }

    public function delete($id)
    {
        $id = (int)$id;
        try {
            $this->supplierModel->delete($id);
            flash_set('warning', 'Đã xóa nhà cung cấp');
        } catch (Exception $e) {
            flash_set('danger', 'Không thể xóa nhà cung cấp: ' . $e->getMessage());
        }
        header('Location: ' . BASE_URL . '?r=suppliers');
        exit;
    }
}
