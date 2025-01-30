<?php
include '../common/db.php';

// レンタル情報を取得
$stmt = $conn->prepare("SELECT hr.id, hr.title, hr.manager, hr.start, hr.end, hr.location, hr.cable, hr.is_returned, hr.return_date, hr.duration, hr.notes, hr.resource_id 
                        FROM hdd_rentals hr");
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

$formatted_events = [];
foreach ($events as $event) {
  $formatted_events[] = [
    'id' => $event['id'],
    'title' => $event['title'],
    'start' => $event['start'],
    'end' => $event['end'],
    'resourceId' => $event['resource_id'],
    'extendedProps' => [
      'manager' => $event['manager'],
      'is_returned' => (int) $event['is_returned'],
      'return_date' => $event['return_date'],
      'location' => $event['location'],
      'cable' => $event['cable'],
      'notes' => $event['notes'],
      'duration' => $event['duration']
    ]
  ];
}

header('Content-Type: application/json');
echo json_encode($formatted_events);
