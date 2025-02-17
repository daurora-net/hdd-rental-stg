<?php
include '../common/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $userId = $_POST['userId'] ?? null;
  $username = trim($_POST['username'] ?? '');

  if ($userId && $username !== null) {
    try {
      $stmt = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
      $stmt->execute([$username, $userId]);

      $_SESSION['username'] = $username;
      $_SESSION['reloaded'] = true;

      header("Location: " . $_SERVER['HTTP_REFERER']);
      exit();
    } catch (PDOException $e) {
      error_log("ユーザー編集エラー: " . $e->getMessage());
      echo "エラーが発生しました。管理者に連絡してください。";
    }
  } else {
    echo "必要なデータが送信されていません。";
  }
}
?>