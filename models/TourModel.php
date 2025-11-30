<?php
// D:\laragon\www\base\models\TourModel.php

class TourModel extends BaseModel {
    protected $table_name = "tours";

    public function __construct() {
        parent::__construct();
    }

    public function all() {
        // Thử JOIN cả suppliers và prices; nếu lỗi do thiếu cột/bảng thì fallback dần
        try {
            $supplierText = $this->hasColumn('supplier') ? ', t.supplier AS supplier_text' : '';
            $query = "SELECT t.*, tc.name AS category_name, ts.name AS supplier_name, tp.adult_price AS adult_price_join{$supplierText}
                      FROM {$this->table_name} t
                      LEFT JOIN tour_categories tc ON t.category_id = tc.id
                      LEFT JOIN tour_suppliers ts ON t.supplier_id = ts.id
                      LEFT JOIN tour_prices tp ON tp.tour_id = t.id
                      ORDER BY t.created_at DESC";
            $stmt = $this->pdo->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $msg = $e->getMessage();
            // Fallback nếu thiếu bảng tour_prices
            if (strpos($msg, 'tour_prices') !== false) {
                try {
                    $supplierText = $this->hasColumn('supplier') ? ', t.supplier AS supplier_text' : '';
                    $query = "SELECT t.*, tc.name AS category_name, ts.name AS supplier_name{$supplierText}
                              FROM {$this->table_name} t
                              LEFT JOIN tour_categories tc ON t.category_id = tc.id
                              LEFT JOIN tour_suppliers ts ON t.supplier_id = ts.id
                              ORDER BY t.created_at DESC";
                    $stmt = $this->pdo->query($query);
                    return $stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e2) {
                    // Nếu tiếp tục lỗi do thiếu cột supplier_id thì bỏ JOIN supplier
                    if (strpos($e2->getMessage(), 'supplier_id') !== false) {
                        // Nếu bảng tours có cột text 'supplier' thì lấy làm supplier_name
                        $supplierCol = $this->hasColumn('supplier') ? ', t.supplier AS supplier_name, t.supplier AS supplier_text' : '';
                        $query = "SELECT t.*, tc.name AS category_name{$supplierCol}
                                  FROM {$this->table_name} t
                                  LEFT JOIN tour_categories tc ON t.category_id = tc.id
                                  ORDER BY t.created_at DESC";
                        $stmt = $this->pdo->query($query);
                        return $stmt->fetchAll(PDO::FETCH_ASSOC);
                    }
                    throw $e2;
                }
            }
            // Nếu lỗi do thiếu cột supplier_id, bỏ JOIN supplier nhưng vẫn cố JOIN prices nếu có
            if (strpos($msg, 'supplier_id') !== false) {
                try {
                    $supplierCol = $this->hasColumn('supplier') ? ', t.supplier AS supplier_name, t.supplier AS supplier_text' : '';
                    $query = "SELECT t.*, tc.name AS category_name{$supplierCol}, tp.adult_price AS adult_price_join
                              FROM {$this->table_name} t
                              LEFT JOIN tour_categories tc ON t.category_id = tc.id
                              LEFT JOIN tour_prices tp ON tp.tour_id = t.id
                              ORDER BY t.created_at DESC";
                    $stmt = $this->pdo->query($query);
                    return $stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e3) {
                    // Nếu vẫn lỗi do tour_prices không có, về query tối thiểu
                    if (strpos($e3->getMessage(), 'tour_prices') !== false) {
                        $supplierCol = $this->hasColumn('supplier') ? ', t.supplier AS supplier_name, t.supplier AS supplier_text' : '';
                        $query = "SELECT t.*, tc.name AS category_name{$supplierCol}
                                  FROM {$this->table_name} t
                                  LEFT JOIN tour_categories tc ON t.category_id = tc.id
                                  ORDER BY t.created_at DESC";
                        $stmt = $this->pdo->query($query);
                        return $stmt->fetchAll(PDO::FETCH_ASSOC);
                    }
                    throw $e3;
                }
            }
            throw $e;
        }
    }

    public function find($id) {
        try {
            $query = "SELECT t.*, tc.name AS category_name, ts.name AS supplier_name
                      FROM {$this->table_name} t
                      LEFT JOIN tour_categories tc ON t.category_id = tc.id
                      LEFT JOIN tour_suppliers ts ON t.supplier_id = ts.id
                      WHERE t.id = ?";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([(int)$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Nếu lỗi do thiếu cột supplier_id, thử query không có JOIN supplier
            if (strpos($e->getMessage(), 'supplier_id') !== false) {
                $query = "SELECT t.*, tc.name AS category_name
                          FROM {$this->table_name} t
                          LEFT JOIN tour_categories tc ON t.category_id = tc.id
                          WHERE t.id = ?";
                $stmt = $this->pdo->prepare($query);
                $stmt->execute([(int)$id]);
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            throw $e;
        }
    }

    public function create($data) {
        // Kiểm tra các cột khả dụng trong DB hiện tại
        $hasSupplier      = $this->hasColumn('supplier_id');
        $hasPolicy        = $this->hasColumn('policy');
        $hasCoverImage    = $this->hasColumn('cover_image');
        $hasImageLegacy   = $this->hasColumn('image');
        $hasPriceLegacy   = $this->hasColumn('price');
        $hasCreatedBy     = $this->hasColumn('created_by_user_id');

        // Xây dựng INSERT động theo schema thực tế
        $cols = ['category_id','title','description','tour_type','status'];
        $vals = [
            !empty($data['category_id']) ? (int)$data['category_id'] : null,
            $data['title'] ?? '',
            $data['description'] ?? '',
            $data['tour_type'] ?? 'domestic',
            $data['status'] ?? 'upcoming',
        ];

        if ($hasSupplier)   { $cols[] = 'supplier_id'; $vals[] = !empty($data['supplier_id']) ? (int)$data['supplier_id'] : null; }
        elseif ($this->hasColumn('supplier')) {
            // Map tên NCC vào cột text 'supplier' nếu có
            $cols[] = 'supplier';
            $vals[] = $this->lookupSupplierName($data['supplier_id'] ?? null);
        }
        if ($hasPolicy)     { $cols[] = 'policy';      $vals[] = $data['policy'] ?? ''; }

        // Ảnh: ưu tiên cover_image nếu có cột, nếu không thì map sang image (legacy)
        if ($hasCoverImage) { $cols[] = 'cover_image'; $vals[] = $data['cover_image'] ?? null; }
        elseif ($hasImageLegacy) { $cols[] = 'image'; $vals[] = $data['cover_image'] ?? ($data['image'] ?? ''); }

        // Giá và người tạo (legacy schema yêu cầu NOT NULL)
        if ($hasPriceLegacy)   { $cols[] = 'price'; $vals[] = isset($data['price']) ? (float)$data['price'] : 0.0; }
        if ($hasCreatedBy)     { $cols[] = 'created_by_user_id'; $vals[] = !empty($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 1; }

        $placeholders = '(' . implode(',', array_fill(0, count($cols), '?')) . ')';
        $query = "INSERT INTO {$this->table_name} (" . implode(',', $cols) . ") VALUES {$placeholders}";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($vals);

        return $this->pdo->lastInsertId();
    }

    protected function hasColumn($columnName)
    {
        try {
            $stmt = $this->pdo->prepare("SHOW COLUMNS FROM `{$this->table_name}` LIKE ?");
            $stmt->execute([$columnName]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Public helper for controllers to check schema capability safely
    public function columnExists($name)
    {
        return $this->hasColumn($name);
    }

    protected function lookupSupplierName($supplierId)
    {
        if (empty($supplierId)) return null;
        try {
            $stmt = $this->pdo->prepare("SHOW TABLES LIKE 'tour_suppliers'");
            $stmt->execute();
            if ($stmt->rowCount() === 0) return null;
            $q = $this->pdo->prepare("SELECT name FROM tour_suppliers WHERE id = ?");
            $q->execute([(int)$supplierId]);
            $row = $q->fetch(PDO::FETCH_ASSOC);
            return $row ? $row['name'] : null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function update($id, $data) {
        // Lấy tour hiện tại để đảm bảo category_id không null
        $current = $this->find($id);
        if (!$current) { return false; }

        $hasSupplier    = $this->hasColumn('supplier_id');
        $hasPolicy      = $this->hasColumn('policy');
        $hasCoverImage  = $this->hasColumn('cover_image');
        $hasImageLegacy = $this->hasColumn('image');
        $hasPriceLegacy = $this->hasColumn('price');

        $fields = [
            'category_id' => !empty($data['category_id']) ? (int)$data['category_id'] : (int)$current['category_id'],
            'title' => $data['title'] ?? '',
            'description' => $data['description'] ?? '',
            'tour_type' => $data['tour_type'] ?? 'domestic',
            'status' => $data['status'] ?? 'upcoming',
        ];

        $set = [
            'category_id = :category_id',
            'title = :title',
            'description = :description',
            'tour_type = :tour_type',
            'status = :status',
        ];

        if ($hasSupplier) {
            $set[] = 'supplier_id = :supplier_id';
            $fields['supplier_id'] = !empty($data['supplier_id']) ? (int)$data['supplier_id'] : null;
        } elseif ($this->hasColumn('supplier')) {
            $set[] = 'supplier = :supplier';
            $fields['supplier'] = $this->lookupSupplierName($data['supplier_id'] ?? null);
        }
        if ($hasPolicy) {
            $set[] = 'policy = :policy';
            $fields['policy'] = $data['policy'] ?? '';
        }
        if ($hasPriceLegacy && isset($data['price'])) {
            $set[] = 'price = :price';
            $fields['price'] = (float)$data['price'];
        }

        if (!empty($data['cover_image'])) {
            if ($hasCoverImage) {
                $set[] = 'cover_image = :cover_image';
                $fields['cover_image'] = $data['cover_image'];
            } elseif ($hasImageLegacy) {
                $set[] = 'image = :cover_image';
                $fields['cover_image'] = $data['cover_image'];
            }
        }

        $sql = "UPDATE {$this->table_name} SET " . implode(', ', $set) . " WHERE id = :id";
        $fields['id'] = (int)$id;
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($fields);
    }

    public function delete($id) {
        // Kiểm tra xem tour có booking nào không
        $checkSql = "SELECT COUNT(*) as booking_count FROM bookings WHERE tour_id = ?";
        $checkStmt = $this->pdo->prepare($checkSql);
        $checkStmt->execute([(int)$id]);
        $result = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['booking_count'] > 0) {
            // Có booking liên quan, không cho xóa
            return [
                'success' => false,
                'message' => "Không thể xóa tour này vì có {$result['booking_count']} booking liên quan. Vui lòng xóa các booking trước hoặc hủy chúng."
            ];
        }
        
        // Chỉ kiểm tra các bảng quan trọng (không cho xóa nếu có dữ liệu)
        $criticalTables = [
            'guide_assignments' => 'tour_id'  // Phân công HDV - quan trọng
        ];
        
        foreach ($criticalTables as $table => $field) {
            try {
                $checkTableSql = "SHOW TABLES LIKE ?";
                $checkStmt = $this->pdo->prepare($checkTableSql);
                $checkStmt->execute([$table]);
                
                if ($checkStmt->rowCount() > 0) {
                    $sql = "SELECT COUNT(*) as count FROM {$table} WHERE {$field} = ?";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute([(int)$id]);
                    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                    
                    if ($count > 0) {
                        return [
                            'success' => false,
                            'message' => "Không thể xóa tour này vì có {$count} phân công hướng dẫn viên liên quan. Vui lòng hủy các phân công trước."
                        ];
                    }
                }
            } catch (PDOException $e) {
                continue;
            }
        }
        
        // Nếu không có ràng buộc quan trọng, thực hiện xóa
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table_name} WHERE id = ?");
        $result = $stmt->execute([(int)$id]);
        
        return [
            'success' => $result,
            'message' => $result ? 'Xóa tour thành công!' : 'Xóa tour thất bại!'
        ];
    }
}
?>