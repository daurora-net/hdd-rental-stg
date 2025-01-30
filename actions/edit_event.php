<?php
include '../common/db.php';
session_start(); // セッションを開始

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // null合体演算子を使用して簡素化
  $eventId = $_POST['eventId'] ?? null;
  $title = trim($_POST['eventTitle'] ?? '');
  $manager = trim($_POST['eventManager'] ?? '');
  $start = $_POST['eventStart'] ?? null;
  $end = $_POST['eventEnd'] ?? null;
  $resource_id = $_POST['rentalHdd'] ?? null;
  $location = $_POST['rentalLocation'] ?? null;
  $cable = $_POST['rentalCable'] ?? null;
  $returnDate = $_POST['returnDate'] ?? null;
  $isReturned = !empty($returnDate) ? 1 : 0;
  $duration = $_POST['rentalDuration'] ?? null;
  $notes = trim($_POST['eventNotes'] ?? '');
  $updated_by = $_SESSION['username'] ?? 'unknown';

  if (empty($returnDate)) {
    $returnDate = null;
  }

  // 必須項目のバリデーション（必要に応じて）
  if ($eventId && $title && $manager && $resource_id) {
    try {
      // データベースの更新
      // 修正後のUPDATE文にis_returnedを追加
      $stmt = $conn->prepare("UPDATE hdd_rentals 
                  SET title = ?, manager = ?, start = ?, end = ?, resource_id = ?, location = ?, cable = ?, is_returned = ?, return_date = ?, duration = ?, notes = ?, updated_by = ? 
                  WHERE id = ?");
      $stmt->execute([$title, $manager, $start, $end, $resource_id, $location, $cable, $isReturned, $returnDate, $duration, $notes, $updated_by, $eventId]);

      // セッションフラグを設定
      $_SESSION['reloaded'] = true;

      // 現在のページにリダイレクト
      header("Location: " . $_SERVER['HTTP_REFERER']);
      exit();
    } catch (PDOException $e) {
      // エラーログに記録
      error_log("レンタル編集エラー: " . $e->getMessage());
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