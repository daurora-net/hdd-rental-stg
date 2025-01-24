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
  $location = $_POST['rentalLocation'] ?? null; // 必須ではない
  $isReturned = isset($_POST['isReturned']) ? 1 : 0; // チェックボックスはissetで十分
  $returnDate = $_POST['returnDate'] ?? null;
  $actualStart = $_POST['actualStart'] ?? null;
  $notes = trim($_POST['eventNotes'] ?? '');
  $updated_by = $_SESSION['username'] ?? 'unknown';

  // 必須項目のバリデーション（必要に応じて）
  if ($eventId && $title && $manager && $resource_id) {
    try {
      // データベースの更新
      $stmt = $conn->prepare("UPDATE hdd_rentals 
                SET title = ?, manager = ?, start = ?, end = ?, resource_id = ?, location = ?, is_returned = ?, return_date = ?, actual_start = ?, notes = ?, updated_by = ? 
                WHERE id = ?");
      $stmt->execute([$title, $manager, $start, $end, $resource_id, $location, $isReturned, $returnDate, $actualStart, $notes, $updated_by, $eventId]);

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