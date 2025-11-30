<!DOCTYPE html>
<html>
<head><title>TEST</title></head>
<body>
<h1>TRANG STAFF TEST</h1>
<p>Nếu bạn thấy trang này thì routing hoạt động</p>
<?php
require_once __DIR__ . '/../../assets/configs/env.php';
echo "BASE_URL: " . BASE_URL . "<br>";
echo "DB_HOST: " . DB_HOST . "<br>";

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USERNAME, DB_PASSWORD);
    $stmt = $pdo->query('SELECT COUNT(*) as total FROM users');
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "Total users: " . $count . "<br>";
} catch(Exception $e) {
    echo "DB Error: " . $e->getMessage() . "<br>";
}
?>
</body>
</html>
