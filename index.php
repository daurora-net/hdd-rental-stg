<?php
include 'common/db.php';
?>

<!DOCTYPE html>
<html>

<?php
$isIndex = true;
include 'parts/head.php';
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
        <li class="list active">
          <a href="/hdd-rental/">
            <!-- <span class="icon"><i class="fa-solid fa-bars-staggered"></i></span> -->
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
        <li class="list">
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