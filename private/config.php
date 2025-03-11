
<?php
require_once __DIR__ . '/../src/Database.php';


use App\Database;

$dbInstance = new Database();
$conn = $dbInstance->getConnection();
$dbInstance = new Database();
$conn = $dbInstance->getConnection();
$dbInstance->createTables();

?>
