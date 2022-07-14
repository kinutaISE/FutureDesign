<?php
require_once('app/config.php') ;
include_once('_parts/_header.php') ;

// メールアドレスを取得
$email = trim(filter_input(INPUT_POST, 'email')) ;
// 件名の設定
$subject = '仮登録のご案内' ;
// メール内容を取得（work/app/texts/pre_user_registration.txt）
$message = file_get_contents(__DIR__ . '/../app/texts/pre_user_registration.txt') ;
// メール内容のリンクをサインアップページに置き換え
$message = str_replace($message, '*signup_url*', SITE_URL . '/../signup.php') ;
// ヘッダの設定
$header = 'From: nknopt.ise@gmail.com' ;

// メールの送信
$is_successful = mb_send_mail($email, $subject, $message) ;

?>

<body>
  <p><?= $email ;?></p>
  <p><?= $is_successful ;?></p>
  <p>本登録URLを記載したメールを送信しました</p>
<body>

<?php
include_once('_parts/_footer.php') ;
