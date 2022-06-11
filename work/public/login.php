<?php

require_once('../app/config.php') ;

$pdo = Database::getInstance() ;

$user_id = filter_input(INPUT_POST, 'user_id') ;
$password = filter_input(INPUT_POST, 'password') ;
$stmt = $pdo->prepare("
  SELECT * FROM users WHERE id = :user_id
") ;
$stmt->bindValue('user_id', $user_id) ;
$stmt->execute() ;
$user = $stmt->fetch() ;
?>

<?php
if ( isset($user) && $user->password === $password ) :?>
  <?php
    $_SESSION['user_id'] = $user->id ;
    header('Location: ' . SITE_URL . '/../mypage.php') ;
    exit ;
  ?>
<!-- ユーザーID、または、パスワードが異なる場合 -->
<?php else :?>
  <?php include_once('_parts/_header.php') ;?>
  <p>ユーザーIDまたはパスワードが間違っています</p>
  <p><a href="login_form.php">戻る</a><p>
<?php endif ;?>

<?php

include_once('_parts/_footer.php') ;
