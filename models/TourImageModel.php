<?php

class TourImageModel extends BaseModel
{
    protected $table_name = 'tour_images';

    public function __construct()
    {
        parent::__construct();
    }

    public function getByTour($tourId)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM {$this->table_name} WHERE tour_id = ? ORDER BY id ASC");
            $stmt->execute([(int)$tourId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), "doesn't exist") !== false) {
                return [];
            }
            throw $e;
        }
    }

    public function addGallery($tourId, $paths = [])
    {
        if (empty($paths)) {
            return;
        }
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table_name} (tour_id, image_path) VALUES (?, ?)");
        foreach ($paths as $path) {
            $stmt->execute([(int)$tourId, $path]);
        }
    }

    public function removeByIds($ids = [])
    {
        if (empty($ids)) {
            return;
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $idParams = array_map('intval', $ids);

        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table_name} WHERE id IN ($placeholders)");
        $stmt->execute($idParams);
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($records)) {
            return;
        }

        $stmtDelete = $this->pdo->prepare("DELETE FROM {$this->table_name} WHERE id IN ($placeholders)");
        $stmtDelete->execute($idParams);

        foreach ($records as $record) {
            $filePath = PATH_ASSETS_UPLOADS . $record['image_path'];
            if (is_file($filePath)) {
                unlink($filePath);
            }
        }
    }

    public function deleteByTour($tourId)
    {
        $images = $this->getByTour($tourId);
        foreach ($images as $image) {
            $filePath = PATH_ASSETS_UPLOADS . $image['image_path'];
            if (is_file($filePath)) {
                unlink($filePath);
            }
        }
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table_name} WHERE tour_id = ?");
        $stmt->execute([(int)$tourId]);
    }
}

