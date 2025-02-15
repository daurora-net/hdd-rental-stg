<?php
include '../common/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // フォームから送信された値を取得
  $hddId = isset($_POST['hddId']) ? $_POST['hddId'] : null;
  $hddName = isset($_POST['hddName']) ? $_POST['hddName'] : null;
  $hddCapacity = isset($_POST['hddCapacity']) ? $_POST['hddCapacity'] : null;
  $hddNotes = isset($_POST['hddNotes']) ? $_POST['hddNotes'] : null;

  if ($hddId && $hddName !== null && $hddNotes !== null) {
    try {
      // HDDリソース名とメモを更新する
      $stmt = $conn->prepare("UPDATE hdd_resources SET name = ?, capacity = ?, notes = ? WHERE id = ?");
      $stmt->execute([$hddName, $hddCapacity, $hddNotes, $hddId]);

      // セッションフラグを設定（必要に応じて）
      $_SESSION['reloaded'] = true;

      header("Location: " . $_SERVER['HTTP_REFERER']);
      exit();
    } catch (PDOException $e) {
      // エラーメッセージをログに記録
      error_log("HDD編集エラー: " . $e->getMessage());
      // エラーメッセージをユーザーに表示（開発環境のみ推奨）
      echo "エラーが発生しました。管理者に連絡してください。";
    }
  } else {
    echo "必要なデータが送信されていません。";
  }
}
?>