<?php

require_once('../app/config.php') ;

include_once('_parts/_header.php') ;

// 不適切なログインID・パスワードが入力された場合に表示するテキスト
$message = '' ;
// 適切なユーザーIDとパスワードの組み合わせであるか
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $pdo = Database::getInstance() ;
  $user = find_user_id($pdo) ;
  $password = filter_input(INPUT_POST, 'password') ;
  if ( ($user !== false) && ($user->password == $password) ) {
    // 照合が取れた場合、マイページへ
    $_SESSION['user_id'] = $user->id ;
    header('Location: ' . SITE_URL . '/../mypage.php') ;
    exit ;
  }
  else {
    // そうでない場合は以下のメッセージを表示する
    $message = 'ログインIDまたはパスワードが間違っています' ;
  }
}

?>

<body>
  <h1>将来設計</h1>
  <p>
    <?php
      echo empty($_SESSION['signup_success']) ? '' : $_SESSION['signup_success'] ;
      $_SESSION['signup_success'] = '' ;
    ?>
  </p>
  <p>
    <span style="background-color: rgba(255, 0, 0, 0.75)">
      <?= $message ;?>
    </span>
  </p>
  <form method="post" action="">
    <div>
      <label>ユーザーID（半角英数字）</label>
      <input type="text" name="user_id" minlength="5" maxlength="16" pattern="^[a-zA-Z0-9]+$" required>
    </div>
    <div>
      <label>パスワード（半角英数字）</label>
      <input type="password" name="password" minlength="8" maxlength="20" pattern="^[a-zA-Z0-9]+$" required>
    </div>
    <button>ログイン</button>
  </form>
  <p>未登録の方は<a href="signup.php">こちら</a></p>
</body>

<?php

include_once('_parts/_footer.php') ;
