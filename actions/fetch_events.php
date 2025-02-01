<?php
// fetch_events.php
// -------------------------------------------------------
include '../common/db.php';

// JSON以外の出力をしないよう、echoやHTMLタグを入れない

header('Content-Type: application/json; charset=utf-8'); // JSONとして返す

try {
  // データベースから情報を取得
  $stmt = $conn->prepare("
        SELECT
          hr.id, hr.title, hr.manager,
          hr.start, hr.end,
          hr.location, hr.cable,
          hr.is_returned, hr.return_date,
          hr.duration, hr.notes,
          hr.resource_id
        FROM hdd_rentals hr
    ");
  $stmt->execute();
  $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $formatted_events = [];

  foreach ($events as $event) {
    // DB上の終了予定日
    $dbEnd = $event['end'];
    // 2) DBに保存されている返却日
    $dbReturn = $event['return_date'];

    // ▼ 修正点: 「返却日が入力されていれば返却日優先、返却日が無ければ終了予定日」
    $displayEnd = !empty($dbReturn) ? $dbReturn : $dbEnd;
    
    // FullCalendarはall-dayイベントを「endの前日まで」塗りつぶすため+1日する
    $calendarEnd = null;
    if (!empty($displayEnd)) {
      $calendarEnd = date('Y-m-d', strtotime($displayEnd . ' +1 day'));
    }

    $formatted_events[] = [
      'id' => $event['id'],
      'title' => $event['title'],
      'start' => $event['start'],
      'end' => $calendarEnd,  // 「返却日 or 終了予定日」のどちらか +1日
      'resourceId' => $event['resource_id'],

      'extendedProps' => [
        'manager' => $event['manager'],
        'is_returned' => (int) $event['is_returned'],
        'return_date' => $dbReturn,
        'location' => $event['location'],
        'cable' => $event['cable'],
        'notes' => $event['notes'],
        'duration' => $event['duration'],

        // フォーム編集時に「本来の終了予定日」を表示したいなら保管しておく
        'real_end' => $dbEnd
      ]
    ];
  }

  // JSONとして出力
  echo json_encode($formatted_events);

} catch (PDOException $e) {
  // 万が一エラーが出た場合はエラー内容をログに出し、空配列を返す
  error_log("fetch_events エラー: " . $e->getMessage());
  echo json_encode([]);
}