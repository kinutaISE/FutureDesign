<?php

require_once('app/config.php') ;

include_once('_parts/_header.php') ;

?>

<body>
  <h1>新規登録（仮登録）</h1>
  <form method="post" action="mail_sending.php">
    メールアドレス：
    <input type="email" name="email" placeholder="メールアドレスを入力してください">
    <button>送信</button>
  </form>
</body>

<?php

include_once('_parts/_footer.php') ;
