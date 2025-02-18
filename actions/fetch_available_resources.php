<?php
include '../common/db.php';

// GETパラメータから現在編集中のレンタルIDを取得（無ければ0）
$currentRentalId = isset($_GET['current_rental_id']) ? intval($_GET['current_rental_id']) : 0;

// 現在のレンタルに割り当てられているリソースIDを取得 (deleted_at IS NULL のみ対象)
$currentResourceId = 0;
if ($currentRentalId > 0) {
  $stmt = $conn->prepare("
    SELECT resource_id 
    FROM hdd_rentals 
    WHERE id = ? 
      AND deleted_at IS NULL
  ");
  $stmt->execute([$currentRentalId]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($row) {
    $currentResourceId = intval($row['resource_id']);
  }
}

// ▼「各リソースの最新(最大 updated_at) レンタル」サブクエリ
//   ・deleted_at IS NULL の中で updated_at が最大のものを取得し、その is_returned を調べる
//   ・存在しなければ“未使用”とみなす
//   ・存在すれば、is_returned=1 なら“未使用”、0 なら“使用中”
$query = "
  SELECT 
    r.id,
    r.name
  FROM hdd_resources r
  -- 最新の active レンタル情報を LEFT JOIN
  LEFT JOIN (
    SELECT hr.resource_id, hr.is_returned
    FROM hdd_rentals hr
    INNER JOIN (
      SELECT resource_id, MAX(updated_at) AS max_updated
      FROM hdd_rentals
      WHERE deleted_at IS NULL
      GROUP BY resource_id
    ) m ON hr.resource_id = m.resource_id
        AND hr.updated_at = m.max_updated
    WHERE hr.deleted_at IS NULL
  ) latest ON r.id = latest.resource_id
  
  WHERE r.deleted_at IS NULL
    AND 
    (
      -- 現在のリソースなら常に表示
      r.id = :currentResourceId
      -- それ以外は“未使用”のもの
      OR (
        latest.resource_id IS NULL  -- レンタル履歴が無い
        OR latest.is_returned = 1   -- 最新が is_returned=1
      )
    )
";

$stmt = $conn->prepare($query);
$stmt->bindValue(':currentResourceId', $currentResourceId, PDO::PARAM_INT);
$stmt->execute();
$resources = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 結果を JSON で返す
echo json_encode($resources);