<?php
include '../common/db.php';

// レンタル情報を取得（resource_id を含める）
$stmt = $conn->prepare("SELECT hr.id, hr.title, hr.manager, hr.start, hr.end, hr.location, hr.cable, hr.is_returned, hr.return_date, hr.actual_start, hr.duration, hr.notes, hr.resource_id, r.name as hdd_name 
                        FROM hdd_rentals hr 
                        JOIN hdd_resources r ON hr.resource_id = r.id");
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
?>

<!DOCTYPE html>
<html>

<?php
$pageTitle = 'SCHEDULE';
include '../parts/head.php';
?>

<body>
  <aside>
    <nav>
      <ul>
        <li class="nav_home"><a href="/hdd-rental/"><i class="fa-solid fa-house"></i></a></li>
      </ul>
    </nav>
    <div class="navigation">
      <ul>
        <li class="list">
          <a href="hdd_list">
            <span class="icon">HDD</span>
          </a>
        </li>
        <li class="list active">
          <a href="rental_list">
            <span class="icon">SCHEDULE</span>
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
      <!-- レンタル追加ボタン -->
      <button id="addRentalBtn" class="add-btn">+</button>
    </div>

    <?php
    // レンタル詳細追加用ポップアップモーダル
    include '../modals/add_rental_modal.php';
    ?>

    <!-- レンタル一覧表示 -->
    <div class="rental-list list-container">
      <table>
        <thead>
          <tr>
            <th></th>
            <th>ID</th>
            <th>番組名</th>
            <th>担当者</th>
            <th>HDD No</th>
            <th>開始予定日</th>
            <th>終了予定日</th>
            <th>使用場所</th>
            <th>ケーブル</th>
            <th>返却済</th>
            <th>返却日</th>
            <th>実際の開始日</th>
            <th>使用日数</th>
            <th>メモ</th>
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
                  data-actual-start="<?php echo htmlspecialchars($rental['actual_start']); ?>"
                  data-notes="<?php echo htmlspecialchars($rental['notes']); ?>">
                  <i class="fa-solid fa-pen-to-square"></i>
                </button>
              </td>
              <td><?php echo htmlspecialchars($rental['id']); ?></td>
              <td><?php echo htmlspecialchars($rental['title']); ?></td>
              <td><?php echo htmlspecialchars($rental['manager']); ?></td>
              <td><?php echo htmlspecialchars($rental['hdd_name']); ?></td>
              <td><?php echo htmlspecialchars($rental['start']); ?></td>
              <td><?php echo htmlspecialchars($rental['end']); ?></td>
              <td><?php echo htmlspecialchars($rental['location']); ?></td>
              <td><?php echo htmlspecialchars($rental['cable']); ?></td>
              <td class="text-center"><?php echo $rental['is_returned'] ? '✔︎' : ''; ?></td>
              <td><?php echo htmlspecialchars($rental['return_date']); ?></td>
              <td><?php echo htmlspecialchars($rental['actual_start']); ?></td>
              <td><?php echo htmlspecialchars($rental['duration']); ?></td>
              <td><?php echo htmlspecialchars($rental['notes']); ?></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>

    <?php
    // イベント編集用ポップアップモーダル
    include '../modals/edit_event_modal.php';
    ?>
  </main>

  <!-- modal.js を読み込む -->
  <script src="../assets/js/modal.js"></script>
</body>

</html>