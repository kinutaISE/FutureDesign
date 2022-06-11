<?php

require_once('../app/config.php') ;

include_once('_parts/_header.php') ;

?>

<body>
  <h1>ログイン</h1>
  <form method="post" action="login.php">
    <div>
      <label>ユーザーID</label>
      <input type="text" name="user_id" minlength="5" maxlength="16" pattern="^[a-zA-Z0-9]+$" required>
    </div>
    <div>
      <label>パスワード</label>
      <input type="password" name="password" minlength="8" maxlength="20" pattern="^[a-zA-Z0-9]+$" required>
    </div>
    <button>ログイン</button>
  </form>
  <p>未登録の方は<a href="signup.php">こちら</a></p>
</body>

<?php

include_once('_parts/_footer.php') ;
