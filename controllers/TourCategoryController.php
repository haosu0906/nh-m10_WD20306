<?php

require_once __DIR__ . '/../models/TourCategoryModel.php';

class TourCategoryController {
    protected $model;
    protected $types = [
        'domestic' => 'Tour trong nước',
        'international' => 'Tour quốc tế',
        'custom' => 'Tour theo yêu cầu'
    ];

    public function __construct(){ $this->model = new TourCategoryModel(); }

    public function index(){
        $items = $this->model->all();
        $types = $this->types;
        require __DIR__ . '/../views/tour_categories/index.php';
    }

    public function create(){
        $item = null;
        $errors = flash('errors') ?? [];
        $old = flash('old') ?? [];
        $types = $this->types;
        require __DIR__ . '/../views/tour_categories/form.php';
    }

    public function store(){
        $name = trim($_POST['name'] ?? '');
        $categoryType = $_POST['category_type'] ?? 'domestic';
        
        // Tự động sửa category_type dựa trên tên nếu không khớp
        $categoryType = $this->autoFixCategoryType($name, $categoryType);
        
        $data = [
            'name' => $name,
            'description' => trim($_POST['description'] ?? ''),
            'category_type' => $categoryType,
        ];

        $errors = $this->validate($data);
        if (!empty($errors)) {
            redirect_with_flash(BASE_URL . '?r=tour_categories_create', $errors, $_POST);
        }

        $this->model->create($data);
        header('Location: ' . BASE_URL . '?r=tour_categories');
        exit;
    }

    public function edit($id){
        $item = $this->model->find($id);
        if (!$item) {
            header('Location: ' . BASE_URL . '?r=tour_categories');
            exit;
        }
        $errors = flash('errors') ?? [];
        $old = flash('old') ?? [];
        $types = $this->types;
        require __DIR__ . '/../views/tour_categories/form.php';
    }

    public function update($id){
        $item = $this->model->find($id);
        if (!$item) {
            header('Location: ' . BASE_URL . '?r=tour_categories');
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $categoryType = $_POST['category_type'] ?? 'domestic';
        
        // Tự động sửa category_type dựa trên tên nếu không khớp
        $categoryType = $this->autoFixCategoryType($name, $categoryType);

        $data = [
            'name' => $name,
            'description' => trim($_POST['description'] ?? ''),
            'category_type' => $categoryType,
        ];

        $errors = $this->validate($data, $id);
        if (!empty($errors)) {
            redirect_with_flash(BASE_URL . '?r=tour_categories_edit&id=' . $id, $errors, $_POST);
        }

        $this->model->update($id, $data);
        header('Location: ' . BASE_URL . '?r=tour_categories');
        exit;
    }

    public function delete($id){
        $this->model->delete($id);
        header('Location: ' . BASE_URL . '?r=tour_categories');
        exit;
    }

    public function show($id){
        $item = $this->model->find($id);
        if (!$item) {
            header('Location: ' . BASE_URL . '?r=tour_categories');
            exit;
        }
        $types = $this->types;
        require __DIR__ . '/../views/tour_categories/show.php';
    }

    protected function validate($data, $ignoreId = null)
    {
        $errors = [];
        if ($data['name'] === '') {
            $errors['name'] = 'Tên danh mục không được để trống.';
        } elseif ($this->model->existsByName($data['name'], $ignoreId)) {
            $errors['name'] = 'Tên danh mục đã tồn tại.';
        }

        if (!array_key_exists($data['category_type'], $this->types)) {
            $errors['category_type'] = 'Loại tour không hợp lệ.';
        }

        return $errors;
    }

    /**
     * Tự động sửa category_type dựa trên tên danh mục
     * Nếu tên chứa từ khóa "quốc tế", "nước ngoài" -> international
     * Nếu tên chứa từ khóa "yêu cầu", "custom" -> custom
     * Còn lại -> domestic
     */
    protected function autoFixCategoryType($name, $currentType)
    {
        $nameLower = mb_strtolower($name, 'UTF-8');
        
        // Kiểm tra từ khóa quốc tế/nước ngoài
        if (preg_match('/(quốc\s*tế|nước\s*ngoài|international)/i', $nameLower)) {
            return 'international';
        }
        
        // Kiểm tra từ khóa yêu cầu/custom
        if (preg_match('/(yêu\s*cầu|custom)/i', $nameLower)) {
            return 'custom';
        }
        
        // Nếu không khớp, giữ nguyên loại hiện tại hoặc mặc định domestic
        return in_array($currentType, ['domestic', 'international', 'custom']) 
            ? $currentType 
            : 'domestic';
    }
}
