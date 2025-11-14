
<?php
require_once __DIR__ . '/../assets/configs/db.php';

class TourCategoryModel {
    protected $pdo;
    public function __construct(){ $this->pdo = DB::get(); }

    public function all(){
        $stmt = $this->pdo->query("SELECT * FROM tours ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id){
        $stmt = $this->pdo->prepare("SELECT * FROM tours WHERE id = ?");
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data){
        $stmt = $this->pdo->prepare("INSERT INTO tours (name, description) VALUES (?, ?)");
        return $stmt->execute([trim($data['name'] ?? ''), $data['description'] ?? '']);
    }

    public function update($id, $data){
        $stmt = $this->pdo->prepare("UPDATE tours SET name = ?, description = ? WHERE id = ?");
        return $stmt->execute([trim($data['name'] ?? ''), $data['description'] ?? '', (int)$id]);
    }

    public function delete($id){
        $stmt = $this->pdo->prepare("DELETE FROM tours WHERE id = ?");
        return $stmt->execute([(int)$id]);
    }
}