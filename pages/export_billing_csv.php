<?php
include '../common/db.php';

// ▼ GET パラメータから年月フィルターを取得
$selectedYm = isset($_GET['ym']) ? $_GET['ym'] : '';

// ▼ メインの SELECT (billing_list と同様)
$sqlMain = "
  SELECT
    hr.id,
    hr.title,
    hr.manager,
    r.name AS hdd_name,
    r.capacity AS hdd_capacity,
    hr.return_date,
    hr.duration
  FROM hdd_rentals hr
  JOIN hdd_resources r ON hr.resource_id = r.id
  WHERE hr.return_date IS NOT NULL
";

if (!empty($selectedYm)) {
  list($year, $month) = explode('-', $selectedYm);
  $sqlMain .= "
    AND YEAR(hr.return_date)  = :year
    AND MONTH(hr.return_date) = :month
  ";
}
$sqlMain .= " ORDER BY hr.return_date ASC";

$stmtMain = $conn->prepare($sqlMain);
if (!empty($selectedYm)) {
  $stmtMain->bindValue(':year', (int) $year, PDO::PARAM_INT);
  $stmtMain->bindValue(':month', (int) $month, PDO::PARAM_INT);
}
$stmtMain->execute();
$billingList = $stmtMain->fetchAll(PDO::FETCH_ASSOC);

// CSV 出力用ヘッダー設定
header('Content-Type: text/csv; charset=UTF-8');
$filename = 'billing_list';
if (!empty($selectedYm)) {
  $filename .= '_' . str_replace('-', '_', $selectedYm);
}
header('Content-Disposition: attachment; filename="' . $filename . '.csv"');

// 出力ストリームをオープン
$output = fopen('php://output', 'w');

// ヘッダ行の書き出し
fputcsv($output, ['番組名', '担当者', 'HDD No.', 'HDD容量', '返却日', '使用日数']);

// 各行の書き出し
foreach ($billingList as $row) {
  fputcsv($output, [
    $row['title'],
    $row['manager'],
    $row['hdd_name'],
    $row['hdd_capacity'],
    $row['return_date'],
    $row['duration']
  ]);
}

fclose($output);
exit();
?>