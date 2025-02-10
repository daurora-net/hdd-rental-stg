<?php
include '../common/db.php';

$stmtRole = $conn->prepare("SELECT role FROM users WHERE username = ?");
$stmtRole->execute([$_SESSION['username']]);
$currentUserRole = $stmtRole->fetchColumn();

// role=1のみアクセス可能
if ($currentUserRole != 1) {
  header("Location: /hdd-rental/");
  exit();
}
?>

<!DOCTYPE html>
<html>

<?php
$pageTitle = 'USER';
include '../parts/head.php';
?>

<body>
  <?php
  $activePage = 'user_list';
  include '../parts/nav.php';
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

    <div class="hdd-list list-container">
      USER_LIST
    </div>
  </main>

</body>

</html>