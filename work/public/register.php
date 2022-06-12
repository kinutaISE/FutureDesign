<?php

require_once('../app/config.php') ;

include_once('_parts/_header.php') ;

$pdo = Database::getInstance() ;
$password = filter_input(INPUT_POST, 'password') ;
$password_confirmed = filter_input(INPUT_POST, 'password_confirmed') ;

if ( find_user_id($pdo) !== false ) {
  // 同じIDのユーザーが存在する場合
  $message = '既に同じ ID のユーザーが存在します' ;
  $link = '<a href="signup.php">戻る</a>' ;
} else if ( find_user_email($pdo) !== false ) {
  // 同じメールアドレスのユーザーが存在する場合
  $message = '既に同じメールアドレスで登録されているユーザーが存在します' ;
  $link = '<a href="signup.php">戻る</a>' ;
} else if ($password !== $password_confirmed) {
  // パスワードと再入力パスワードが異なった場合
  $message = '再入力されたパスワードが間違っています' ;
  $link = '<a href="signup.php">戻る</a>' ;
} else {
  regist_user($pdo) ;
  $message = '登録が完了しました' ;
  $link = '<a href="login_form.php">ログイン</a>' ;
}
?>

<body>
  <p><?= $message; ?></p>
  <p><?= $link ;?></p>
</body>

<?php

include_once('_parts/_footer.php') ;
