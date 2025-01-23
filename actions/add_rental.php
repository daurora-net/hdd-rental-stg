<?php
include '../common/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = $_POST['rentalTitle'];
  $manager = $_POST['rentalManager'];
  $start = $_POST['rentalStart'];
  $end = $_POST['rentalEnd'];
  $hddId = $_POST['rentalHdd'];
  $location = $_POST['rentalLocation'];
  $notes = $_POST['rentalNotes'];
  $returnDate = $_POST['returnDate'];
  $actualStart = $_POST['actualStart'];
  $created_by = $_SESSION['username'];

  // 日数計算
  $diffDays = null;
  if (!empty($returnDate) && !empty($actualStart)) {
    $startDate = new DateTime($actualStart);
    $endDate = new DateTime($returnDate);
    $interval = $startDate->diff($endDate);
    $diffDays = $interval->days + 1;  // 開始日を含める
  }

  // レンタルデータをhdd_rentalsテーブルに挿入（duration列を追加）
  $stmt = $conn->prepare("INSERT INTO hdd_rentals 
          (title, manager, start, end, resource_id, location, notes, return_date, actual_start, duration, created_by) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->execute([$title, $manager, $start, $end, $hddId, $location, $notes, $returnDate, $actualStart, $diffDays, $created_by]);

  // 挿入されたレンタルのIDを取得（必要なら使用）
  $rentalId = $conn->lastInsertId();

  header("Location: ../index");
  exit();
}
?>