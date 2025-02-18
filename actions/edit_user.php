<?php
include '../common/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // ▼ POSTデータを受け取る
  $userId = $_POST['userId'] ?? null;
  $username = trim($_POST['username'] ?? '');
  // 以下は、もしフォーム側で email / role を後日対応したい場合の受け取り例です
  // （現状の edit_user_modal.php には email / role 入力欄が無いため空扱い）
  $email = trim($_POST['email'] ?? '');
  $role = isset($_POST['role']) ? (int) $_POST['role'] : 2; // デフォルトを 2(一般ユーザー) とする例

  try {
    // 「削除ボタン」が押下された場合
    if (isset($_POST['delete']) && $_POST['delete'] == '1') {
      // ▼ 論理削除：deleted_at に現在日時をセット
      $stmt = $conn->prepare("UPDATE users SET deleted_at = NOW() WHERE id = ?");
      $stmt->execute([$userId]);

      // ▼【追記】ログイン中のユーザーが削除対象なら即ログアウト処理
      $stmt2 = $conn->prepare("SELECT id FROM users WHERE username = ?");
      $stmt2->execute([$_SESSION['username']]);
      $currentUser = $stmt2->fetch(PDO::FETCH_ASSOC);

      if ($currentUser && $currentUser['id'] == $userId) {
        // セッション破棄
        session_unset();
        session_destroy();
        // remember_tokenクッキーも削除（必要に応じて）
        setcookie('remember_token', '', time() - 3600, '/');

        // ログイン画面へリダイレクト
        header("Location: /hdd-rental/login");
        exit();
      }

    } else {
      // ▼ 通常の「編集保存」
      // （email / role を更新したい場合があるため残しておく）
      $stmt = $conn->prepare("
        UPDATE users
        SET username = ?, email = ?, role = ?
        WHERE id = ?
      ");
      $stmt->execute([$username, $email, $role, $userId]);
    }

    // 処理後は元のページに戻る
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();

  } catch (PDOException $e) {
    error_log("ユーザー更新エラー: " . $e->getMessage());
    echo "エラーが発生しました。";
  }
}