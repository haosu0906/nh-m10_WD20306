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
            'guide'      => 'Hướng dẫn viên',
            'meal'       => 'Ăn uống',
            'entertain'  => 'Giải trí',
            'other'      => 'Dịch vụ khác',
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
}
