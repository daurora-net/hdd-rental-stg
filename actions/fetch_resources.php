<?php
include '../common/db.php';

$stmt = $conn->prepare("SELECT CAST(id AS UNSIGNED) as id, name FROM hdd_resources");
$stmt->execute();
$resources = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($resources);