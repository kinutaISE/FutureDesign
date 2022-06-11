<?php

require_once('../app/config.php') ;

include_once('_parts/_header.php') ;

?>

<body>
  <h1>新規登録</h1>
  <form method="post" action="register.php">
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

  <p>既に登録済みの方は<a href="login_form.php">こちら</a></p>
</body>

<?php

include_once('_parts/_footer.php') ;
