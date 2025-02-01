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
    // 1) DB上の終了日をそのまま保持
    $originalEnd = $event['end'];

    // 2) FullCalendar へ渡す終了日は +1日して排他的表示を回避
    //    例：DBに「2025-01-02」⇒ FC用は「2025-01-03」
    $calendarEnd = null;
    if (!empty($originalEnd)) {
      $calendarEnd = date('Y-m-d', strtotime($originalEnd . ' +1 day'));
    }

    $formatted_events[] = [
      'id' => $event['id'],
      'title' => $event['title'],
      'start' => $event['start'],      // DBの値をそのまま
      'end' => $calendarEnd,         // +1日後の値をFullCalendarに渡す
      'resourceId' => $event['resource_id'],

      'extendedProps' => [
        'manager' => $event['manager'],
        'is_returned' => (int) $event['is_returned'],
        'return_date' => $event['return_date'],
        'location' => $event['location'],
        'cable' => $event['cable'],
        'notes' => $event['notes'],
        'duration' => $event['duration'],

        // 編集フォーム用に、本来の終了日も保持
        'real_end' => $originalEnd
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