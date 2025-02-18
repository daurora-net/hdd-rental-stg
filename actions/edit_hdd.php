<?php
include '../common/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $hddId = $_POST['hddId'] ?? null;
  $hddName = $_POST['hddName'] ?? null;
  $hddCapacity = $_POST['hddCapacity'] ?? null;
  $hddNotes = $_POST['hddNotes'] ?? null;

  // ▼ 「削除ボタン」が押されたかチェック
  if (isset($_POST['delete']) && $_POST['delete'] == '1') {
    // 【論理削除】deleted_at に現在日時をセット
    try {
      $stmt = $conn->prepare("
        UPDATE hdd_resources
        SET deleted_at = NOW()
        WHERE id = ?
      ");
      $stmt->execute([$hddId]);

      // 削除後はリダイレクト
      header("Location: " . $_SERVER['HTTP_REFERER']);
      exit();

    } catch (PDOException $e) {
      error_log("HDD削除エラー: " . $e->getMessage());
      echo "エラーが発生しました。";
      exit();
    }

  } else {
    // ▼ 通常の更新処理
    if ($hddId && $hddName !== null && $hddNotes !== null) {
      try {
        $stmt = $conn->prepare("
          UPDATE hdd_resources
          SET name = ?, capacity = ?, notes = ?
          WHERE id = ?
        ");
        $stmt->execute([$hddName, $hddCapacity, $hddNotes, $hddId]);

        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();

      } catch (PDOException $e) {
        error_log("HDD編集エラー: " . $e->getMessage());
        echo "エラーが発生しました。";
      }
    } else {
      echo "必要なデータが送信されていません。";
    }
  }
}