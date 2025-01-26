<?php
include '../common/db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data['start']) && !empty($data['end'])) {
  $datetimeStart = new DateTime($data['start']);
  $datetimeEnd = new DateTime($data['end']);
  $interval = $datetimeStart->diff($datetimeEnd);
  // （同日を 1 日と数える場合）
  $duration = $interval->days + 1;
} else {
  $duration = null;
}

$stmt = $conn->prepare("UPDATE hdd_rentals SET start = ?, end = ? WHERE id = ?");
$stmt->execute([$data['start'], $data['end'], $data['id']]);

http_response_code(200);