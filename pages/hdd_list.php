<?php
include '../common/db.php';

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
  <aside>
    <nav>
      <ul>
        <!-- <li class="nav_home"><a href="/hdd-rental/"><i class="fa-solid fa-house"></i></a></li> -->
        <li class="nav_home"></li>
      </ul>
    </nav>
    <div class="navigation">
      <ul>
        <li class="list">
          <a href="/hdd-rental/">
            <!-- <span class="icon"><i class="fa-solid fa-bars-staggered"></i></span> -->
            <span class="icon"><i class="fa-solid fa-house"></i></span>
          </a>
        </li>
        <li class="list active">
          <a href="hdd_list">
            <span class="icon">HDD</span>
          </a>
        </li>
        <li class="list">
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
      <!-- HDD追加ボタン -->
      <button id="addHddBtn" class="add-btn">+</button>
    </div>
    <?php
    // HDD追加用ポップアップモーダル
    include '../modals/add_hdd_modal.php';
    ?>

    <!-- HDD一覧表示 -->
    <div class="hdd-list list-container">
      <table>
        <thead>
          <tr>
            <th></th>
            <th>ID</th>
            <th>HDD名</th>
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
              <td><?php echo htmlspecialchars($hddResource['id']); ?></td>
              <td><?php echo htmlspecialchars($hddResource['name']); ?></td>
              <td><?php echo htmlspecialchars($hddResource['notes']); ?></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>

    <?php
    // HDD編集用ポップアップモーダル
    include '../modals/edit_hdd_modal.php';
    ?>
  </main>

</body>

</html>