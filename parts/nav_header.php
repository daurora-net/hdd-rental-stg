<?php
// parts/nav_header.php
// ※ このファイルは、セッション開始済みかつユーザー認証済みである前提です

// DB接続（nav_header.php の位置に合わせたパスにしてください）
include_once __DIR__ . '/../common/db.php';

// ユーザーごとの背景色の初期値（デフォルト）
$roleColor = 'cadetblue';

// ログインユーザーが存在する場合、role に応じた色を設定
if (isset($_SESSION['username'])) {
  $stmt = $conn->prepare("SELECT role FROM users WHERE username = ?");
  $stmt->execute([$_SESSION['username']]);
  $role = $stmt->fetchColumn();

  // ユーザーの role に応じた背景色の設定例
  switch ($role) {
    case 1:
      // 管理者の場合
      $roleColor = '#caab4e';
      break;
    case 2:
      // 一般の場合
      $roleColor = 'cadetblue';
      break;
    case 3:
      // 料金の場合
      $roleColor = '#ab8cb6';
      break;
    default:
      $roleColor = 'cadetblue';
      break;
  }
}
?>
<div class="header-nav no-print" style="background-color: <?php echo htmlspecialchars($roleColor); ?>;">
  <div id="menu-toggle" class="open-icon">
    <i class="fa-solid fa-bars-staggered"></i>
  </div>
  <div class="header-nav-info">
    <p class="pc">id: <?php echo htmlspecialchars($_SESSION['username']); ?></p>
    <button class="logout pc">
      <a href="logout.php">LOGOUT</a>
    </button>
  </div>
</div>