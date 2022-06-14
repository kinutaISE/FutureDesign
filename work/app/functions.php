<?php
// パスワードと確認用のパスワードが合致するか（合致する場合はtrueを返す）
function check_password_matching()
{
  $password = filter_input(INPUT_POST, 'password') ;
  $password_confirmed = filter_input(INPUT_POST, 'password_confirmed') ;
  return ($password === $password_confirmed) ;
}
// users テーブルの操作 ////////////////////////////////////////////////////////////
// 入力されたIDのユーザーの抽出
function find_user_id($pdo)
{
  // 操作：入力されたIDをもつユーザーを抽出する
  // 対象：users テーブル
  $user_id = filter_input(INPUT_POST, 'user_id') ;
  $stmt = $pdo->prepare("
    SELECT * FROM users WHERE id = :user_id
  ") ;
  $stmt->bindValue('user_id', $user_id) ;
  $stmt->execute() ;
  $user = $stmt->fetch() ;
  return $user ;
}
// 入力されたメールアドレスをもつユーザーの抽出
function find_user_email($pdo)
{
  // 操作：入力されたメールアドレスをもつユーザーを抽出する
  // 対象：users テーブル
  $email = filter_input(INPUT_POST, 'email') ;
  $stmt = $pdo->prepare("
    SELECT * FROM users WHERE email = :email
  ") ;
  $stmt->bindValue('email', $email) ;
  $stmt->execute() ;
  $user = $stmt->fetch() ;
  return $user ;
}
// ユーザーの登録
function regist_user($pdo)
{
  // 操作：入力されたID、パスワード、メールアドレスをもつユーザーを users へ追加する
  // 対象：users テーブル

  // 成功したかどうか、メッセージ、リンクをセットにして返す
  $result = [
    'is_successful' => true,
    'message' => '',
    'link' => ''
  ] ;
  // 追加するべきユーザーかどうかのチェック
  if ( find_user_id($pdo) !== false ) {
    // 同じIDのユーザーが存在する場合
    $result['message'] = '既に同じ ID のユーザーが存在します' ;
    $result['link'] = '<a href="signup.php">戻る</a>' ;
  } else if ( find_user_email($pdo) !== false ) {
    // 同じメールアドレスのユーザーが存在する場合
    $result['message'] = '既に同じメールアドレスで登録されているユーザーが存在します' ;
    $result['link'] = '<a href="signup.php">戻る</a>' ;
  } else if ( !check_password_matching() ) {
    // パスワードと再入力パスワードが異なった場合
    $result['message'] = '再入力されたパスワードが間違っています' ;
    $result['link'] = '<a href="signup.php">戻る</a>' ;
  } else {
    // ユーザーの追加
    $user_id = filter_input(INPUT_POST, 'user_id') ;
    $password = filter_input(INPUT_POST, 'password') ;
    $email = filter_input(INPUT_POST, 'email') ;
    $stmt = $pdo->prepare(
      "INSERT INTO users (id, password, email) VALUES (:user_id, :password, :email)"
    ) ;
    $stmt->bindValue('user_id', $user_id) ;
    $stmt->bindValue('password', $password) ;
    $stmt->bindValue('email', $email) ;
    $stmt->execute() ;
    $result['message'] = '登録が完了しました' ;
    $result['link'] = '<a href="login_form.php">ログイン</a>' ;
  }
  return $result ;
}

// ユーザー情報の獲得
function get_user_info($pdo)
{
  // 操作：ユーザー情報の抽出
  // 対象：users
  $user_id = $_SESSION['user_id'] ;
  $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id") ;
  $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR) ;
  $stmt->execute() ;
  return $stmt->fetch() ;
}

// ユーザー情報の更新
function update_user_info($pdo)
{
  // 操作：ユーザー情報の更新
  // 対象：users
  $user_id = $_SESSION['user_id'] ;
  $age = trim( filter_input(INPUT_POST, 'age') ) ;
  $age = ($age === '') ? NULL : $age ;
  $prefecture_id = trim( filter_input(INPUT_POST, 'prefecture_id') ) ;
  $prefecture_id = ($prefecture_id === '') ? NULL : $prefecture_id ;
  $dependents_num = filter_input(INPUT_POST, 'dependents_num') ;
  $dependents_num = ($dependents_num === '') ? NULL : $dependents_num ;
  $stmt = $pdo->prepare("
    UPDATE
      users
    SET
      age = :age,
      prefecture_id = :prefecture_id,
      dependents_num = :dependents_num
    WHERE
      id = :user_id
  ") ;
  $stmt->bindValue('age', $age, PDO::PARAM_INT) ;
  $stmt->bindValue('prefecture_id', $prefecture_id, PDO::PARAM_INT) ;
  $stmt->bindValue('dependents_num', $dependents_num, PDO::PARAM_INT) ;
  $stmt->bindValue('user_id', $user_id, PDO::PARAM_STR) ;
  $stmt->execute() ;
}


////////////////////////////////////////////////////////////////////////////////

// earnings テーブルの操作/////////////////////////////////////////////////////////
// 給与項目の抽出
function get_earning_items($pdo)
{
  // 操作：給与項目の抽出
  // 対象：earnings テーブル
  $user_id = $_SESSION['user_id'] ;
  $stmt = $pdo->prepare("SELECT * FROM earnings WHERE user_id = :user_id") ;
  $stmt->bindValue('user_id', $user_id) ;
  $stmt->execute() ;
  return $stmt->fetchAll(PDO::FETCH_CLASS, 'EarningItem') ;
}


// 給与項目の追加
function add_earning_item($pdo)
{
  // 操作：給与項目の追加
  // 対象：earnings テーブル
  $user_id = $_SESSION['user_id'] ;
  $name = trim(filter_input(INPUT_POST, 'earning_item_name')) ;
  $amount = trim(filter_input(INPUT_POST, 'earning_item_amount')) ;
  $is_taxation = trim(filter_input(INPUT_POST, 'earning_item_is_taxation')) ;
  if ($name === '' || $amount === '' || $is_taxation === '')
    return ;
  $stmt = $pdo->prepare("
    INSERT INTO earnings (name, amount, is_taxation, user_id)
    VALUES (:name, :amount, :is_taxation, :user_id)
  ") ;
  $stmt->bindValue('name', $name) ;
  $stmt->bindValue('amount', $amount) ;
  $stmt->bindValue('is_taxation', $is_taxation) ;
  $stmt->bindValue('user_id', $user_id) ;
  $stmt->execute() ;
}

////////////////////////////////////////////////////////////////////////////////

// prefectures テーブルの操作//////////////////////////////////////////////////////
// 全ての都道府県情報を抽出する
function get_prefectures_info($pdo)
{
  $stmt = $pdo->query("SELECT * FROM prefectures") ;
  return $stmt->fetchAll() ;
}


////////////////////////////////////////////////////////////////////////////////

// cost_items テーブルの操作 //////////////////////////////////////////////////////

// 支出項目の抽出
function get_cost_items($pdo)
{
  // 操作：ユーザーが登録している支出項目を抽出する
  // 対象：cost_items
  $user_id = $_SESSION['user_id'] ;
  $stmt = $pdo->prepare("SELECT * FROM cost_items WHERE user_id = :user_id") ;
  $stmt->bindValue(':user_id', $user_id) ;
  $stmt->execute() ;
  return $stmt->fetchAll() ;
}

// 支出項目の追加
function add_cost_item($pdo)
{
  // 操作：新しい支出項目の追加
  // 対象：cost_items
  $name = trim( filter_input(INPUT_POST, 'cost_item_name') ) ;
  $value = trim( filter_input(INPUT_POST, 'cost_item_value') ) ;
  $user_id = $_SESSION['user_id'] ;
  if ($name === '' || $value === '')
    return ;
  $stmt = $pdo->prepare("
    INSERT INTO cost_items (name, value, user_id)
    VALUES (:name, :value, :user_id)
  ") ;
  $stmt->bindValue('name', $name, PDO::PARAM_STR) ;
  $stmt->bindValue('value', $value, PDO::PARAM_INT) ;
  $stmt->bindValue('user_id', $user_id, PDO::PARAM_STR) ;
  $stmt->execute() ;
}

// 支出項目の削除
function delete_cost_item($pdo)
{
  // 操作：選択した支出項目をテーブルから削除する
  // 対象：cost_items
  $stmt = $pdo->prepare("
    DELETE FROM cost_items WHERE id = :id
  ") ;
  $id = filter_input(INPUT_POST, 'cost_item_id') ;
  $stmt->bindParam('id', $id);
  $stmt->execute() ;
}

////////////////////////////////////////////////////////////////////////////////
