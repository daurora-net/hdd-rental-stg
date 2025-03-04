<?php
// parts/nav_menu.php

// 例えば、nav_menu.php は以下のように記述します。
// 必要に応じて DB 接続など（ユーザーの role 判定）が行われます。
// このファイルは、各ページで事前に $activePage を設定してからインクルードしてください。

include_once __DIR__ . '/../common/db.php';

// 現在のユーザーの role を取得
$stmt = $conn->prepare("SELECT role FROM users WHERE username = ?");
$stmt->execute([$_SESSION['username']]);
$userRole = $stmt->fetchColumn();
?>
<aside id="side-menu" class="no-print">
  <div class="header-nav bg-brown">
    <div id="close-menu" class="close-icon sp">
      <i class="fa-solid fa-xmark"></i>
    </div>
  </div>
  <nav class="nav">
    <ul>
      <?php
      // 管理者 (role=1) および一般 (role=2) の場合はガントチャート、HDD管理、SCHEDULE を表示
      if (in_array($userRole, [1, 2])) { ?>
        <li class="pc list <?php echo (isset($activePage) && $activePage == 'index') ? 'active' : ''; ?>">
          <a href="/hdd-rental/">
            <span class="icon"><i class="fa-solid fa-house"></i></span>
          </a>
        </li>
        <li class="list <?php echo (isset($activePage) && $activePage == 'rental_list') ? 'active' : ''; ?>">
          <a href="rental_list">
            <span class="icon">スケジュール</span>
          </a>
        </li>
        <li class="list <?php echo (isset($activePage) && $activePage == 'hdd_list') ? 'active' : ''; ?>">
          <a href="hdd_list">
            <span class="icon">HDD</span>
          </a>
        </li>

        <!-- 後で削除 -->
        <li class="list <?php echo (isset($activePage) && $activePage == 'user_list') ? 'active' : ''; ?>">
          <a href="user_list">
            <span class="icon">ユーザー</span>
          </a>
        </li>
        <li class="list <?php echo (isset($activePage) && $activePage == 'billing_list') ? 'active' : ''; ?>">
          <a href="billing_list">
            <span class="icon">料金</span>
          </a>
        </li>
        <!-- 後で削除ここまで -->

      <?php }


      // 管理者 (role=1) のみ表示
      if ($userRole == 1) { ?>
        <!-- <li class="list <?php echo (isset($activePage) && $activePage == 'user_list') ? 'active' : ''; ?>">
          <a href="user_list">
            <span class="icon">USER</span>
          </a>
        </li> -->
      <?php }

      // 料金 (role=3) のみ表示（billing_list ページ）
      if ($userRole == 3) { ?>
        <!-- <li class="list <?php echo (isset($activePage) && $activePage == 'billing_list') ? 'active' : ''; ?>">
          <a href="billing_list">
            <span class="icon">BILLING</span>
          </a>
        </li> -->
      <?php } ?>
    </ul>
    <div class="nav-logout sp">
      <a href="/hdd-rental/logout.php">LOGOUT
        <span class="icon"><i class="fa-solid fa-sign-out"></i></span>
      </a>
      <div class="user-box">
        <p class>id: <?php echo htmlspecialchars($_SESSION['username']); ?></p>
      </div>
    </div>
  </nav>
</aside>