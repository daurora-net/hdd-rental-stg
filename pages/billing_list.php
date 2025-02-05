<?php
// ---------------------------------------------
// billing_listページ
// ---------------------------------------------
include '../common/db.php';

// ▼ 「year-month」パラメータ (例: "2024-12", "2025-01") を取得
$selectedYm = isset($_GET['ym']) ? $_GET['ym'] : '';

// ▼ まず「返却年-月」の一覧を取得 (null以外)
$sqlMonths = "
  SELECT DISTINCT DATE_FORMAT(return_date, '%Y-%m') AS ym
  FROM hdd_rentals
  WHERE return_date IS NOT NULL
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
    r.name AS hdd_name,
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
$pageTitle = 'BILLING';
include '../parts/head.php';
?>

<body>
  <aside>
    <nav>
      <ul>
        <li class="nav_home"></li>
      </ul>
    </nav>
    <div class="navigation">
      <ul>
        <li class="list">
          <a href="/hdd-rental/">
            <span class="icon"><i class="fa-solid fa-house"></i></span>
          </a>
        </li>
        <li class="list">
          <a href="hdd_list">
            <span class="icon">HDD</span>
          </a>
        </li>
        <li class="list">
          <a href="rental_list">
            <span class="icon">SCHEDULE</span>
          </a>
        </li>
        <li class="list active">
          <a href="billing_list">
            <span class="icon">BILLING</span>
          </a>
        </li>
      </ul>
    </div>
  </aside>
  <main>
    <div class="header-nav">
      <h1></h1>
      <div class="header-nav-info">
        <p>id: <?php echo htmlspecialchars($_SESSION['username']); ?></p>
        <button class="logout">
          <a href="logout.php">LOGOUT</a>
        </button>
      </div>
    </div>

    <div class="container">
      <!-- ▼ 年月セレクト (実際にある返却日のみ) -->
      <div class="form-content w-200px custom-select billing-select">
        <form method="get" action="">
          <label>返却月:</label>
          <select name="ym" onchange="this.form.submit()">
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
        </form>
      </div>
    </div>

    <!-- ▼ テーブル表示 -->
    <div class="billing-list list-container">
      <table>
        <thead>
          <tr>
            <th>番組名</th>
            <th>担当者</th>
            <th>HDD名</th>
            <th>返却日</th>
            <th>使用日数</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($billingList as $row): ?>
            <tr>
              <td><?php echo htmlspecialchars($row['title']); ?></td>
              <td><?php echo htmlspecialchars($row['manager']); ?></td>
              <td><?php echo htmlspecialchars($row['hdd_name']); ?></td>
              <td><?php echo htmlspecialchars($row['return_date']); ?></td>
              <td><?php echo htmlspecialchars($row['duration']); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </main>

</body>

</html>