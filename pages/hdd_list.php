<?php
include '../common/db.php';

$stmtRole = $conn->prepare("SELECT role FROM users WHERE username = ?");
$stmtRole->execute([$_SESSION['username']]);
$currentUserRole = $stmtRole->fetchColumn();

// role=1,2のみアクセス可能
if (!in_array($currentUserRole, [1, 2])) {
  header("Location: /hdd-rental/billing_list");
  exit();
}

// HDDリソース情報を取得
$stmt = $conn->prepare("SELECT id, name, notes FROM hdd_resources");
$stmt->execute();
$hddResources = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_SESSION['reloaded'])) {
  unset($_SESSION['reloaded']);
}

$sql = "SELECT * FROM hdd_resources";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>

<?php
$pageTitle = 'HDD';
include '../parts/head.php';
?>

<body>
  <?php
  $activePage = 'hdd_list';
  include '../parts/nav_menu.php';
  ?>
  <main>
    <?php
    include '../parts/nav_header.php';
    ?>

    <div class="container">
      <h2 class="sp">HDD</h2>
      <div class="header-container">
        <!-- HDD追加ボタン -->
        <button id="addHddBtn" class="add-btn"><i class="fa-solid fa-plus"></i></button>
      </div>
      <!-- HDD一覧表示 -->
      <div class="hdd-list list-container table-scroll">
        <table>
          <thead>
            <tr>
              <th></th>
              <th>ID</th>
              <th>HDD No.</th>
              <th>メモ</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($hddResources as $hddResource) { ?>
              <tr>
                <td>
                  <button class="edit-btn edit-hdd-btn" data-bs-toggle="modal" data-bs-target="#editHddModal"
                    data-id="<?php echo htmlspecialchars($hddResource['id']); ?>"
                    data-name="<?php echo htmlspecialchars($hddResource['name']); ?>"
                    data-notes="<?php echo htmlspecialchars($hddResource['notes']); ?>">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </button>
                </td>
                <td class="text-center"><?php echo htmlspecialchars($hddResource['id']); ?></td>
                <td><?php echo htmlspecialchars($hddResource['name']); ?></td>
                <td><?php echo htmlspecialchars($hddResource['notes']); ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>

    <?php
    // HDD追加モーダル
    include '../modals/add_hdd_modal.php';
    ?>

    <?php
    // HDD編集モーダル
    include '../modals/edit_hdd_modal.php';
    ?>
  </main>

</body>

</html>