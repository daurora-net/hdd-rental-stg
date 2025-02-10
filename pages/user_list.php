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

// 【データ取得】users テーブルから全件取得
$stmt = $conn->prepare("SELECT * FROM users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    <div class="user-list list-container">
      <table border="1" cellpadding="8" cellspacing="0">
        <thead>
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>認証</th>
            <th>Role</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $user): ?>
            <tr>
              <td><?php echo htmlspecialchars($user['id']); ?></td>
              <td><?php echo htmlspecialchars($user['username']); ?></td>
              <td><?php echo htmlspecialchars($user['email']); ?></td>
              <td><?php echo $user['is_verified'] ? '✔︎' : ''; ?></td>
              <td>
                <?php
                $roles = [
                  1 => '管理者',
                  2 => '一般ユーザー',
                  3 => '精算ユーザー'
                ];
                echo htmlspecialchars($roles[$user['role']]);
                ?>
              </td>

            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </main>

</body>

</html>