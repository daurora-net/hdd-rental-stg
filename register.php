<?php
include 'common/config.php';
require __DIR__ . '/vendor/autoload.php'; // PHPMailerのオートローダーをインクルード

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST['username'];
  $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
  $email = $_POST['email'];
  $verification_code = bin2hex(random_bytes(16)); // ランダムな認証コードを生成
  $verification_expiry = date('Y-m-d H:i:s', strtotime('+1 hour')); // 現在時刻から1時間後を設定

  // ユーザー情報をデータベースに挿入
  $sql = "INSERT INTO users (username, password, email, verification_code, verification_expiry) VALUES (?, ?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->execute([$username, $password, $email, $verification_code, $verification_expiry]);

  // 認証メールを送信
  $mail = new PHPMailer(true);
  try {
    // Server settings
    $mail->isSMTP();
    $mail->Host = 'sv12068.xserver.jp'; // SMTPサーバー
    $mail->Username = 'dev@daurora.xsrv.jp'; // SMTPユーザー名
    $mail->Password = 'wen0606110483';   // SMTPパスワード
    $mail->Port = 587;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->SMTPAuth = true;

    // Recipients
    $mail->setFrom('dev@daurora.xsrv.jp', 'HDD Rental');
    $mail->addAddress($email, $username);

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'メールアドレスを確認してください';
    $mail->Body = "以下のリンクをクリックして、認証を完了してください<br><br><a href='https://daurora.xsrv.jp/hdd-rental/verify.php?code=$verification_code'>確認</a>";

    $mail->send();
    $_SESSION['verification_message'] = 'メール認証用リンクを送信しました。メールを確認し、リンクをクリックしてアカウントを有効化してください。';
    header("Location: /hdd-rental/message.php");
    exit();
  } catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}
?>

<!DOCTYPE html>
<html>

<?php
$pageTitle = '新規登録';
include 'parts/head.php';
?>

<body class="page-login">
  <div class="login-wrap">
    <h1 class="text-center">株式会社いちまるよん<br><span>編集部 HDD管理</span></h1>
    <!-- <h2>Register</h2> -->
    <form method="post">
      ログイン名<input type="text" name="username" required><br>
      メールアドレス<input type="email" name="email" required><br>
      パスワード<input type="password" name="password" required><br>
      <button type="submit" class="login">Register</button>
    </form>
    <div class="register-info">
      <a href="login">ログインはこちら</a>
    </div>
  </div>
</body>

</html>