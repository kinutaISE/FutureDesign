<?php

require_once('../app/config.php') ;

include_once('_parts/_header.php') ;

$pdo = Database::getInstance() ;

$user_id = filter_input(INPUT_POST, 'user_id') ;
$password = filter_input(INPUT_POST, 'password') ;
$password_confirmed = filter_input(INPUT_POST, 'password_confirmed') ;
$stmt = $pdo->prepare("
  SELECT * FROM users WHERE id = :user_id
") ;
$stmt->bindValue('user_id', $user_id) ;
$stmt->execute() ;
$user = $stmt->fetch() ;
if ( $user !== false ) {
  $message = '既に同じ ID のユーザーが存在します' ;
  $link = '<a href="signup.php">戻る</a>' ;
} else if ($password !== $password_confirmed) {
  $message = '再入力されたパスワードが間違っています' ;
  $link = '<a href="signup.php">戻る</a>' ;
} else {
  $stmt = $pdo->prepare(
    "INSERT INTO users (id, password) VALUES (:user_id, :password)"
  ) ;
  $stmt->bindValue('user_id', $user_id) ;
  $stmt->bindValue('password', $password) ;
  $stmt->execute() ;
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
