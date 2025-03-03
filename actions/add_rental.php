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

  // 返却日が未入力ならNULLをセット
  if (empty($returnDate)) {
    $returnDate = null;
  }

  // 必須項目のバリデーション
  if ($title && $manager && $hddId) {
    try {
      // ▼ 日時重複チェック
      //    overlap条件: NOT (既存.end < 新規.start OR 既存.start > 新規.end)
      //    →「(既存.start <= 新規.end) AND (既存.end >= 新規.start)」で重複
      //    deleted_at IS NULL が対象
      $overlapSql = "
        SELECT COUNT(*) 
        FROM hdd_rentals
        WHERE deleted_at IS NULL
          AND resource_id = ?
          AND NOT (end < ? OR start > ?)
      ";
      $stmtOverlap = $conn->prepare($overlapSql);
      $stmtOverlap->execute([
        $hddId,
        $start,
        $end
      ]);
      $countOverlap = $stmtOverlap->fetchColumn();

      if ($countOverlap > 0) {
        // 重複あり → エラーを出して中断
        echo "登録できません：指定された期間が既存レンタルと重複しています。";
        exit;
      }

      // ▼ 返却日があるかどうかで is_returned を設定
      $isReturned = !empty($returnDate) ? 1 : 0;

      // ▼ レンタルデータをINSERT
      $stmt = $conn->prepare("
        INSERT INTO hdd_rentals
          (title, manager, start, end, resource_id,
           location, cable, duration, notes, return_date,
           is_returned, created_by)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
      ");
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

      // 挿入されたレンタルIDが必要なら取得
      $rentalId = $conn->lastInsertId();

      // ▼ 正常終了の場合は「OK」と返す
      echo "OK";
      exit;

    } catch (PDOException $e) {
      // エラーログに記録
      error_log("レンタル追加エラー: " . $e->getMessage());
      echo "エラーが発生しました。管理者に連絡してください。";
      exit();
    }
  } else {
    // 必須項目が不足している場合
    echo "必要な項目が入力されていません。";
    exit();
  }
}
?>