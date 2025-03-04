<?php
// ---------------------------------------------
// billing_listページ
// ---------------------------------------------
include '../common/db.php';

$stmtRole = $conn->prepare("SELECT role FROM users WHERE username = ?");
$stmtRole->execute([$_SESSION['username']]);
$currentUserRole = $stmtRole->fetchColumn();

// role=3のみアクセス可能
// if ($currentUserRole != 3) {
//   header("Location: /hdd-rental/");
//   exit();
// }

// ▼ 「year-month」パラメータ (例: "2024-12", "2025-01") を取得
$selectedYm = isset($_GET['ym']) ? $_GET['ym'] : '';

// ▼ まず「返却年-月」の一覧を取得 (null以外)
$sqlMonths = "
  SELECT DISTINCT DATE_FORMAT(return_date, '%Y-%m') AS ym
  FROM hdd_rentals
  WHERE return_date IS NOT NULL AND deleted_at IS NULL
  ORDER BY ym ASC
";
$stmtMonths = $conn->prepare($sqlMonths);
$stmtMonths->execute();
$monthList = $stmtMonths->fetchAll(PDO::FETCH_COLUMN);

// ▼ メインの SELECT (hdd_rentals + hdd_resources JOIN)
$sqlMain = "
  SELECT
    hr.id,
    hr.title,
    hr.manager,
    hr.start AS start_date,
    r.name AS hdd_name,
    r.capacity AS hdd_capacity,
    hr.location AS location,
    hr.return_date,
    hr.duration
  FROM hdd_rentals hr
  JOIN hdd_resources r ON hr.resource_id = r.id
  WHERE hr.return_date IS NOT NULL
";

// ▼ 選択があれば、YEAR() と MONTH() で絞り込み
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
?>

<!DOCTYPE html>
<html>

<?php
$pageTitle = '料金';
include '../parts/head.php';
?>

<body>
  <?php
  $activePage = 'billing_list';
  include '../parts/nav_menu.php';
  ?>
  <main>
    <?php
    include '../parts/nav_header.php';
    ?>

    <div class="container print">
      <h2 class="sp no-print">BILLING</h2>
      <div class="header-container">
        <!-- ソートボックス -->
        <!-- ▼ 年月セレクト (実際にある返却日のみ) -->
        <form method="get" action="" class="flex">
          <div class="custom-select-wrapper w-150px">
            <select name="ym"
              onchange="if(this.value==''){ window.location.href=window.location.pathname; } else { this.form.submit(); }">
              <!-- すべて(未選択)用オプション -->
              <option value="">すべて</option>
              <?php foreach ($monthList as $ym): ?>
                <?php
                // 例 "2024-12" -> [2024, 12]
                list($y, $m) = explode('-', $ym);
                // 月先頭の0を外して整数に
                $mInt = (int) $m;
                // ラベル例: "2024年12月"
                $label = $y . '年' . $mInt . '月';
                ?>
                <option value="<?php echo htmlspecialchars($ym); ?>" <?php if ($ym === $selectedYm)
                     echo 'selected'; ?>>
                  <?php echo htmlspecialchars($label); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="no-print pc flex ml-20">
            <button onclick="window.print(); return false;" class="print-btn">印刷</button>
            <button type="button"
              onclick="window.location.href='pages/export_billing_csv.php?ym=<?php echo urlencode($selectedYm); ?>'"
              class="csv-btn">CSV出力</button>
            <!-- <a href="pages/export_billing_csv.php?ym=<?php echo urlencode($selectedYm); ?>" class="csv-btn">CSV出力</a> -->
          </div>
        </form>
      </div>

      <!-- ▼ テーブル表示 -->
      <div class="billing-list table-container table-scroll">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>番組名</th>
              <th>担当者</th>
              <th>HDD No.</th>
              <th>容量</th>
              <th>使用場所</th>
              <th>開始日</th>
              <th>返却日</th>
              <th>使用日数</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($billingList as $row): ?>
              <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td><?php echo htmlspecialchars($row['manager']); ?></td>
                <td><?php echo htmlspecialchars($row['hdd_name']); ?></td>
                <td><?php echo htmlspecialchars($row['hdd_capacity']); ?></td>
                <td><?php echo htmlspecialchars($row['location']); ?></td>
                <td><?php echo htmlspecialchars($row['start_date']); ?></td>
                <td><?php echo htmlspecialchars($row['return_date']); ?></td>
                <td class="text-right"><?php echo htmlspecialchars($row['duration']); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

</body>

</html>