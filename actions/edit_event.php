<?php
include '../common/db.php';
session_start(); // セッションを開始

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // フォームデータの取得
  $eventId = $_POST['eventId'];
  $title = $_POST['eventTitle'];
  $manager = $_POST['eventManager'];
  $start = $_POST['eventStart'];
  $end = $_POST['eventEnd'];
  $resource_id = $_POST['rentalHdd']; // HDD No
  $location = $_POST['rentalLocation']; // 使用場所
  $isReturned = isset($_POST['isReturned']) ? 1 : 0;
  $returnDate = $_POST['returnDate'];
  $actualStart = $_POST['actualStart'];
  $notes = $_POST['eventNotes'];
  $updated_by = $_SESSION['username'];

  // 日数計算
  $diffDays = null;
  if (!empty($returnDate) && !empty($actualStart)) {
    $startDate = new DateTime($actualStart);
    $endDate = new DateTime($returnDate);
    $interval = $startDate->diff($endDate);
    $diffDays = $interval->days + 1;  // 開始日を含める
  }

  // データベースの更新（duration列を追加）
  $stmt = $conn->prepare("UPDATE hdd_rentals 
      SET title = ?, manager = ?, start = ?, end = ?, resource_id = ?, location = ?, is_returned = ?, return_date = ?, actual_start = ?, duration = ?, notes = ?, updated_by = ? 
      WHERE id = ?");
  $stmt->execute([$title, $manager, $start, $end, $resource_id, $location, $isReturned, $returnDate, $actualStart, $diffDays, $notes, $updated_by, $eventId]);

  // セッションフラグを設定
  $_SESSION['reloaded'] = true;

  // 現在のページにリダイレクト
  header("Location: " . $_SERVER['HTTP_REFERER']);
  exit();
}
?>