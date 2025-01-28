<?php
include 'common/config.php';
session_start();

$error_message = ''; // エラー文言を格納する変数

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_POST['email'];
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->execute([$email]);
  $user = $stmt->fetch();

  if ($user && password_verify($password, $user['password'])) {
    if ($user['is_verified']) {
      session_regenerate_id(true); // セッション固定攻撃を防ぐ
      $_SESSION['username'] = $user['username'];
      header("Location: /hdd-rental/");
      exit();
    } else {
      $error_message = "メールアドレスが認証されていません。メールをご確認ください。";
    }
  } else {
    $error_message = "メールアドレスまたはパスワードが違います。";
  }
}
?>
<!DOCTYPE html>
<html>

<?php
$pageTitle = 'ログイン';
include 'parts/head.php';
?>

<body class="page-login">
  <div class="login-wrap">
    <h1>株式会社いちまるよん<br><span>編集部 HDD管理</span></h1>
    <form method="post" class="form">
      <div class="form-group">
        <label for="email">メールアドレス</label>
        <input type="email" id="email" name="email" required>
      </div>
      <div class="form-group">
        <label for="password">パスワード</label>
        <input type="password" id="password" name="password" required>
      </div>
      <?php if (!empty($error_message)): ?>
        <div class="error-message">
          <?php echo htmlspecialchars($error_message); ?>
        </div>
      <?php endif; ?>
      <button type="submit" class="login">Login</button>
    </form>
    <div class="register-info">
      <a href="register">新規登録はこちら</a>
    </div>
  </div>
</body>

</html>