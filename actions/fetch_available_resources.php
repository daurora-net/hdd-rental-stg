<?php
include '../common/db.php';

// 現在編集中のレンタルIDを取得
$currentRentalId = isset($_GET['current_rental_id']) ? intval($_GET['current_rental_id']) : 0;

// 現在のレンタルに割り当てられているリソースIDを取得
if ($currentRentalId > 0) {
  $stmt = $conn->prepare("SELECT resource_id FROM hdd_rentals WHERE id = ?");
  $stmt->execute([$currentRentalId]);
  $currentResource = $stmt->fetch(PDO::FETCH_ASSOC);
  $currentResourceId = $currentResource ? intval($currentResource['resource_id']) : 0;
} else {
  $currentResourceId = 0;
}

// 利用可能なHDDリソースを取得（現在のリソースを含む）
if ($currentResourceId > 0) {
  $stmt = $conn->prepare("
        SELECT r.id, r.name 
        FROM hdd_resources r
        WHERE r.id = ? OR r.id NOT IN (
            SELECT resource_id 
            FROM hdd_rentals 
            WHERE is_returned = FALSE
        )
    ");
  $stmt->execute([$currentResourceId]);
} else {
  $stmt = $conn->prepare("
        SELECT r.id, r.name 
        FROM hdd_resources r
        WHERE r.id NOT IN (
            SELECT resource_id 
            FROM hdd_rentals 
            WHERE is_returned = FALSE
        )
    ");
  $stmt->execute();
}

$resources = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($resources);
?>