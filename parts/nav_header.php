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
      // 一般ユーザーの場合
      $roleColor = 'cadetblue';
      break;
    case 3:
      // 精算ユーザーの場合
      $roleColor = '#ab8cb6';
      break;
    default:
      $roleColor = 'cadetblue';
      break;
  }
}
?>
<div class="header-nav" style="background-color: <?php echo htmlspecialchars($roleColor); ?>;">
  <h1></h1>
  <div class="header-nav-info">
    <p>id: <?php echo htmlspecialchars($_SESSION['username']); ?></p>
    <button class="logout">
      <a href="logout.php">LOGOUT</a>
    </button>
  </div>
</div>