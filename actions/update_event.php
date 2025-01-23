<?php
include '../common/db.php';

$data = json_decode(file_get_contents('php://input'), true);

$stmt = $conn->prepare("UPDATE hdd_rentals SET start = ?, end = ? WHERE id = ?");
$stmt->execute([$data['start'], $data['end'], $data['id']]);

http_response_code(200);