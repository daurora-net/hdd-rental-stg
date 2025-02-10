<?php
include 'common/db.php';

$stmtRole = $conn->prepare("SELECT role FROM users WHERE username = ?");
$stmtRole->execute([$_SESSION['username']]);
$currentUserRole = $stmtRole->fetchColumn();

// role=1,2のみアクセス可能
if (!in_array($currentUserRole, [1, 2])) {
  header("Location: /hdd-rental/billing_list");
  exit();
}
?>
<?php
session_start();
?>
<!DOCTYPE html>
<html>

<?php
$isIndex = true;
include 'parts/head.php';
?>

<body>
  <?php
  $activePage = 'index';
  include 'parts/nav.php';
  ?>
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
    <div id="calendar" class="container">
      <!-- レンタル詳細追加ボタン -->
      <button id="addRentalBtn" class="add-btn">+</button>
    </div>
  </main>

  <?php
  // レンタル詳細追加用ポップアップモーダル
  include 'modals/add_rental_modal.php';

  // イベント編集用ポップアップモーダル
  include 'modals/edit_event_modal.php';
  ?>

</body>

</html>