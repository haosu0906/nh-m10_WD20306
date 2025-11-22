<?php

class TourCategoryModel extends BaseModel {
    protected $table_name = 'tour_categories';

    public function __construct(){ parent::__construct(); }

    public function all(){
        $stmt = $this->pdo->query("SELECT * FROM {$this->table_name} ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id){
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table_name} WHERE id = ?");
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function existsByName($name, $ignoreId = null)
    {
        $sql = "SELECT COUNT(*) FROM {$this->table_name} WHERE name = ?";
        $params = [$name];

        if ($ignoreId) {
            $sql .= " AND id != ?";
            $params[] = (int)$ignoreId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (bool)$stmt->fetchColumn();
    }

    public function create($data){
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table_name} (name, description, category_type) VALUES (?, ?, ?)");
        return $stmt->execute([
            trim($data['name'] ?? ''),
            $data['description'] ?? '',
            $data['category_type'] ?? 'domestic',
        ]);
    }

    public function update($id, $data){
        $stmt = $this->pdo->prepare("UPDATE {$this->table_name} SET name = ?, description = ?, category_type = ? WHERE id = ?");
        return $stmt->execute([
            trim($data['name'] ?? ''),
            $data['description'] ?? '',
            $data['category_type'] ?? 'domestic',
            (int)$id
        ]);
    }

    public function delete($id){
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table_name} WHERE id = ?");
        return $stmt->execute([(int)$id]);
    }
}