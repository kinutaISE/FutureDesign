<?php

require_once('../app/config.php') ;

include_once('_parts/_header.php') ;

$result = NULL ;
if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
  $pdo = Database::getInstance() ;
  $result = regist_user($pdo) ;
}

?>

<body>
  <h1>新規登録</h1>
  <?php if ($result !== NULL):?>
    <?php
    if ( $result['is_successful'] ) {
      $_SESSION['signup_success'] = "登録が完了しました。以下よりマイページにログインしてください。" ;
      header('Location: ' . SITE_URL . '/../index.php') ;
      exit ;
    }
    ?>
    <p>
      <span
        style="
        background-color: rgba(255, 0, 0, 0.1) ;
        border: solid ;
        border-color: rgb(255, 0, 0) ;
        "
      >
        <?= $result['message'] ;?>
      </span>
    </p>
  <?php endif;?>
  <form method="post" action="">
    <div>
      <label>メールアドレス</label>
      <input type="email" name="email" action="register.php">
    </div>
    <div>
      <label>ユーザーID（5文字以上16文字以内/半角数字、半角英小文字、半角英大文字）</label>
      <input type="text" name="user_id" minlength="5" maxlength="16" pattern="^[a-zA-Z0-9]+$" required>
    </div>
    <div>
      <label>パスワード（8文字以上20文字以下/半角数字、半角英小文字、半角英大文字）</label>
      <input type="password" name="password" minlength="8" maxlength="20" pattern="^[a-zA-Z0-9]+$" required>
    </div>
    <div>
      <label>パスワード（再入力してください）</label>
      <input type="password" name="password_confirmed" minlength="8" maxlength="20" pattern="^[a-zA-Z0-9]+$">
    </div>
    <button>登録</button>
  </form>

  <p>既に登録済みの方は<a href="index.php">こちら</a></p>
</body>

<?php

include_once('_parts/_footer.php') ;
