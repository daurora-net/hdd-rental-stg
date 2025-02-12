<?php
include '../common/db.php';
session_start(); // セッションを開始

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = trim($_POST['rentalTitle'] ?? '');
  $manager = trim($_POST['rentalManager'] ?? '');
  $start = $_POST['rentalStart'] ?? null;
  $end = $_POST['rentalEnd'] ?? null;
  $hddId = $_POST['rentalHdd'] ?? null;
  $location = $_POST['rentalLocation'] ?? null;
  $cable = $_POST['rentalCable'] ?? null;
  $duration = $_POST['rentalDuration'] ?? null;
  $notes = trim($_POST['rentalNotes'] ?? '');
  $returnDate = $_POST['returnDate'] ?? null;
  $created_by = $_SESSION['username'] ?? 'unknown';

  if (empty($returnDate)) {
    $returnDate = null;
  }

  // 必須項目のバリデーション
  if ($title && $manager && $hddId) {
    try {
      // レンタルデータをhdd_rentalsテーブルに挿入
      // 返却日が入力されているか確認し、is_returnedを設定
      $isReturned = !empty($returnDate) ? 1 : 0;

      // 修正後のINSERT文にis_returnedを追加
      // 返却日が入力されているか確認し、is_returnedを設定
      $isReturned = !empty($returnDate) ? 1 : 0;

      // 修正後のINSERT文にis_returnedを追加
      $stmt = $conn->prepare("INSERT INTO hdd_rentals 
      (title, manager, start, end, resource_id, location, cable, duration, notes, return_date, is_returned, created_by) 
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
      $stmt->execute([
        $title,
        $manager,
        $start,
        $end,
        $hddId,
        $location,
        $cable,
        $duration,
        $notes,
        $returnDate,
        $isReturned,
        $created_by
      ]);

      // 挿入されたレンタルのIDを取得（必要に応じて使用）
      $rentalId = $conn->lastInsertId();

      header("Location: ../index");
      exit();
    } catch (PDOException $e) {
      // エラーログに記録
      error_log("レンタル追加エラー: " . $e->getMessage());
      // エラーメッセージをユーザーに表示（開発環境のみ推奨）
      echo "エラーが発生しました。管理者に連絡してください。";
      exit();
    }
  } else {
    // 必須項目が不足している場合の処理
    echo "必要な項目が入力されていません。";
    exit();
  }
}
?>