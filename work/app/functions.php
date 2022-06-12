<?php
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
  $income = trim( filter_input(INPUT_POST, 'income') ) ;
  $income = ($income === '') ? NULL : $income ;
  $prefecture_id = trim( filter_input(INPUT_POST, 'prefecture_id') ) ;
  $prefecture_id = ($prefecture_id === '') ? NULL : $prefecture_id ;
  $stmt = $pdo->prepare("
    UPDATE
      users
    SET
      age = :age,
      income = :income,
      prefecture_id = :prefecture_id
    WHERE
      id = :user_id
  ") ;
  $stmt->bindValue('age', $age, PDO::PARAM_INT) ;
  $stmt->bindValue('income', $income, PDO::PARAM_INT) ;
  $stmt->bindValue('user_id', $user_id, PDO::PARAM_STR) ;
  $stmt->bindValue('prefecture_id', $prefecture_id, PDO::PARAM_INT) ;
  $stmt->execute() ;
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
