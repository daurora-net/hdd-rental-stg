<?php
include '../common/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // hdd_list のモーダルフォームから送られる値
  $hddId = $_POST['hddId'];
  $hddName = $_POST['hddName'];

  // HDDリソース名を更新する
  $stmt = $conn->prepare("UPDATE hdd_resources SET name = ? WHERE id = ?");
  $stmt->execute([$hddName, $hddId]);

  header("Location: " . $_SERVER['HTTP_REFERER']);
  exit();
}