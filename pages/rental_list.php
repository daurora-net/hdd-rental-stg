<?php
include '../common/db.php';

// レンタル情報を取得（resource_id を含める）
// 返却フィルターの取得
$filter_returned = isset($_GET['filter_returned']) ? $_GET['filter_returned'] : '';

// WHERE句を可変にする
$sql = "SELECT hr.id, hr.title, hr.manager, hr.start, hr.end, hr.location, hr.cable, hr.is_returned, hr.return_date, hr.duration, hr.notes, hr.resource_id, r.name as hdd_name 
        FROM hdd_rentals hr
        JOIN hdd_resources r ON hr.resource_id = r.id
        WHERE 1=1"; // ★最初に常に成り立つ条件を置いておく

// もし「未返却」を指定されたら is_returned=0 だけを抽出
if ($filter_returned === '0') {
  $sql .= " AND hr.is_returned = 0";
}

$stmt = $conn->prepare($sql);
$stmt->execute();
$rentals = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_SESSION['reloaded'])) {
  unset($_SESSION['reloaded']);
  // 必要な場合はアラート
  // echo "<script>
  //           history.replaceState(null, null, 'rental_list');
  //           alert('更新しました。');
  //           window.location.reload();
  //         </script>";
}

$stmtRole = $conn->prepare("SELECT role FROM users WHERE username = ?");
$stmtRole->execute([$_SESSION['username']]);
$currentUserRole = $stmtRole->fetchColumn();

// role=1,2のみアクセス可能
if (!in_array($currentUserRole, [1, 2])) {
  header("Location: /hdd-rental/billing_list");
  exit();
}
?>

<!DOCTYPE html>
<html>

<?php
$pageTitle = 'SCHEDULE';
include '../parts/head.php';
?>

<body>
  <?php
  $activePage = 'rental_list';
  include '../parts/nav_menu.php';
  ?>
  <main>
    <?php
    include '../parts/nav_header.php';
    ?>

    <div class="container">
      <div class="header-container">
        <div class="flex">
          <!-- レンタル追加ボタン -->
          <button id="addRentalBtn" class="add-btn">+</button>
          <!-- ソートボックス -->
          <form method="get" action="">
            <div class="custom-select-wrapper w-100px">
              <select id="filter_returned" name="filter_returned" onchange="this.form.submit()">
                <option value="">すべて</option>
                <option value="0" <?php if (isset($filter_returned) && $filter_returned === '0')
                  echo 'selected'; ?>>
                  未返却
                </option>
              </select>
            </div>
          </form>
        </div>
      </div>
      <!-- レンタル一覧表示 -->
      <div class="rental-list list-container table-scroll">
        <table class="table-sort">
          <thead>
            <tr>
              <th></th>
              <th onclick="sortTable(this, 1)">
                ID <i class="fa-solid fa-sort"></i>
              </th>
              <th onclick="sortTable(this, 2)">
                番組名 <i class="fa-solid fa-sort"></i>
              </th>
              <th onclick="sortTable(this, 3)">
                担当者 <i class="fa-solid fa-sort"></i>
              </th>
              <th onclick="sortTable(this, 4)">
                HDD No <i class="fa-solid fa-sort"></i>
              </th>
              <th onclick="sortTable(this, 5)">
                開始日 <i class="fa-solid fa-sort"></i>
              </th>
              <th onclick="sortTable(this, 6)">
                終了予定日 <i class="fa-solid fa-sort"></i>
              </th>
              <th onclick="sortTable(this, 7)">
                使用場所 <i class="fa-solid fa-sort"></i>
              </th>
              <th onclick="sortTable(this, 8)">
                ケーブル <i class="fa-solid fa-sort"></i>
              </th>
              <th onclick="sortTable(this, 9)">
                返却済 <i class="fa-solid fa-sort"></i>
              </th>
              <th onclick="sortTable(this, 10)">
                返却日 <i class="fa-solid fa-sort"></i>
              </th>
              <th onclick="sortTable(this, 11)">
                使用日数 <i class="fa-solid fa-sort"></i>
              </th>
              <th onclick="sortTable(this, 12)">
                メモ <i class="fa-solid fa-sort"></i>
              </th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rentals as $rental) { ?>
              <tr>
                <td>
                  <button class="edit-btn edit-event-btn" data-bs-toggle="modal" data-bs-target="#editEventModal"
                    data-id="<?php echo htmlspecialchars($rental['id']); ?>"
                    data-title="<?php echo htmlspecialchars($rental['title']); ?>"
                    data-manager="<?php echo htmlspecialchars($rental['manager']); ?>"
                    data-start="<?php echo htmlspecialchars($rental['start']); ?>"
                    data-end="<?php echo htmlspecialchars($rental['end']); ?>"
                    data-resource-id="<?php echo htmlspecialchars($rental['resource_id']); ?>"
                    data-location="<?php echo htmlspecialchars($rental['location']); ?>"
                    data-cable="<?php echo htmlspecialchars($rental['cable']); ?>"
                    data-is-returned="<?php echo htmlspecialchars($rental['is_returned']); ?>"
                    data-return-date="<?php echo htmlspecialchars($rental['return_date']); ?>"
                    data-notes="<?php echo htmlspecialchars($rental['notes']); ?>">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </button>
                </td>
                <td class="text-center"><?php echo htmlspecialchars($rental['id']); ?></td>
                <td><?php echo htmlspecialchars($rental['title']); ?></td>
                <td><?php echo htmlspecialchars($rental['manager']); ?></td>
                <td><?php echo htmlspecialchars($rental['hdd_name']); ?></td>
                <td><?php echo htmlspecialchars($rental['start']); ?></td>
                <td><?php echo htmlspecialchars($rental['end']); ?></td>
                <td><?php echo htmlspecialchars($rental['location']); ?></td>
                <td><?php echo htmlspecialchars($rental['cable']); ?></td>
                <td class="text-center"><?php echo $rental['is_returned'] ? '✔︎' : ''; ?></td>
                <td><?php echo htmlspecialchars($rental['return_date']) ?: ''; ?></td>
                <td class="text-right"><?php echo htmlspecialchars($rental['duration']); ?></td>
                <td><?php echo htmlspecialchars($rental['notes']); ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>

    <?php
    // 追加モーダル
    include '../modals/add_rental_modal.php';
    ?>

    <?php
    // 編集モーダル
    include '../modals/edit_event_modal.php';
    ?>
  </main>

  <!-- modal.js を読み込む -->
  <script src="../assets/js/modal.js"></script>
  <script src="../assets/js/table.js"></script>
</body>

</html>