<?php
/*
以下 3 つの関数はほとんど処理が同じであるため、後々以下の関数を実装することを検討：
function set_file_info($file_name)
  - 引数：
    - $file_name：csv ファイル名
  - 処理：
    - 属性名（ファイルの先頭行の各項目）をキーとした配列を要素としてもつ配列を返す
    - 出力する配列を $array とする
    - $array の i 番目の要素は以下のようになる想定：
      $array[レコード i + 1 の'属性名1'] = [
        '属性名1' => レコード i + 1 の'属性名1',
        '属性名2' => レコード i + 1 の'属性名2',
        ...
      ]
*/
function set_file_info($filename)
{
  //行を各要素とした配列
  $lines = file($filename, FILE_IGNORE_NEW_LINES) ;
  //コンマ区切りで分割し，属性名を取り出す
  $attributes = explode(",", $lines[0]);

  $data_info = [] ;
  for($i = 1; $i < count($lines); $i++) {
    $data = explode(',', $lines[$i]) ;
    $data_info[ $data[0] ] = array_combine($attributes, $data) ;
  }
  return $data_info ;
}
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
    'is_successful' => false,
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
    $result['is_successful'] = true ;
    $result['message'] = '登録が完了しました' ;
    $result['link'] = '<a href="index.php">ログイン</a>' ;
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
  $stmt->setFetchMode(PDO::FETCH_CLASS, 'User') ;
  $stmt->execute() ;
  return $stmt->fetch() ;
}

// ユーザー情報の更新
function update_user_info($pdo)
{
  // 操作：ユーザー情報の更新
  // 対象：users
  $user_id = $_SESSION['user_id'] ;
  $date_of_birth = trim( filter_input(INPUT_POST, 'date_of_birth') ) ;
  $date_of_birth = ($date_of_birth === '') ? NULL : $date_of_birth ;
  $business_type_id = trim( filter_input(INPUT_POST, 'business_type_id') ) ;
  $business_type_id = ($business_type_id === '') ? NULL : $business_type_id ;
  $prefecture_id = trim( filter_input(INPUT_POST, 'prefecture_id') ) ;
  $prefecture_id = ($prefecture_id === '') ? NULL : $prefecture_id ;
  $dependents_num = filter_input(INPUT_POST, 'dependents_num') ;
  $dependents_num = ($dependents_num === '') ? NULL : $dependents_num ;
  $stmt = $pdo->prepare("
    UPDATE
      users
    SET
      date_of_birth = :date_of_birth,
      business_type_id = :business_type_id,
      prefecture_id = :prefecture_id,
      dependents_num = :dependents_num
    WHERE
      id = :user_id
  ") ;
  $stmt->bindValue('date_of_birth', $date_of_birth, PDO::PARAM_STR) ;
  $stmt->bindValue('business_type_id', $business_type_id, PDO::PARAM_STR) ;
  $stmt->bindValue('prefecture_id', $prefecture_id, PDO::PARAM_STR) ;
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
  $frequency_number = trim(filter_input(INPUT_POST, 'frequency_number')) ;
  $frequency =
    $frequency_number . ' ' . filter_input(INPUT_POST, 'frequency_unit') ;
  if ($name === '' || $amount === '' || $is_taxation === '')
    return ;
  $stmt = $pdo->prepare("
    INSERT INTO earnings (name, amount, is_taxation, frequency, user_id)
    VALUES (:name, :amount, :is_taxation, :frequency, :user_id)
  ") ;
  $stmt->bindValue('name', $name) ;
  $stmt->bindValue('amount', $amount) ;
  $stmt->bindValue('is_taxation', $is_taxation) ;
  $stmt->bindValue('frequency', $frequency) ;
  $stmt->bindValue('user_id', $user_id) ;
  $stmt->execute() ;
}
// 給与項目の削除
function delete_earning_item($pdo)
{
  // 操作：給与項目の削除
  // 対象：earnings テーブル
  $id = filter_input(INPUT_POST, 'earning_item_id') ;
  $stmt = $pdo->prepare("DELETE FROM earnings WHERE id = :id") ;
  $stmt->bindValue('id', $id) ;
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
  $is_constant = filter_input(INPUT_POST, 'is_constant') ;
  $term_start = filter_input(INPUT_POST, 'term_start') ;
  $term_finish = filter_input(INPUT_POST, 'term_finish') ;
  $term = ($is_constant) ?
    'constant' : $term_start . '~' . $term_finish ;
  $frequency_number = trim( filter_input(INPUT_POST, 'frequency_number') ) ;
  $frequency =
    $frequency_number . ' ' . filter_input(INPUT_POST, 'frequency_unit') ;
  $user_id = $_SESSION['user_id'] ;
  if ($name === '' || $value === '')
    return ;
  $stmt = $pdo->prepare("
    INSERT INTO cost_items (name, value, user_id, term, frequency)
    VALUES (:name, :value, :user_id, :term, :frequency)
  ") ;
  $stmt->bindValue('name', $name, PDO::PARAM_STR) ;
  $stmt->bindValue('value', $value, PDO::PARAM_INT) ;
  $stmt->bindValue('term', $term, PDO::PARAM_STR) ;
  $stmt->bindValue('frequency', $frequency, PDO::PARAM_STR) ;
  $stmt->bindValue('user_id', $user_id, PDO::PARAM_STR) ;
  $stmt->execute() ;
}

// 支出項目の削除
function delete_cost_item($pdo)
{
  // 操作：選択した支出項目をテーブルから削除する
  // 対象：cost_items
  $stmt = $pdo->prepare("DELETE FROM cost_items WHERE id = :id") ;
  $id = filter_input(INPUT_POST, 'cost_item_id') ;
  $stmt->bindParam('id', $id);
  $stmt->execute() ;
}

////////////////////////////////////////////////////////////////////////////////

// partner_applications テーブルの操作 ////////////////////////////////////////////
// テーブルへの挿入
function send_partner_application($pdo)
{
  // 申請者ID・申請先IDを取得
  $from_id = $_SESSION['user_id'] ;
  $to_id = filter_input(INPUT_POST, 'to_id') ;
  // 自身の ID の場合→失敗
  if ($from_id === $to_id) {
    $_SESSION['partner_application_result'] = false ;
    $_SESSION['partner_application_text'] = 'パートナー登録申請の送信に失敗しました' ;
    return ;
  }
  // 申請先IDをもつユーザーを検索
  $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :to_id") ;
  $stmt->bindValue('to_id', $to_id) ;
  $stmt->setFetchMode(PDO::FETCH_CLASS, 'User') ;
  $stmt->execute() ;
  $user = $stmt->fetch() ;
  // 存在しない場合 → 失敗
  if ($user === false) {
    $_SESSION['partner_application_result'] = false ;
    $_SESSION['partner_application_text'] = 'パートナー登録申請の送信に失敗しました' ;
    return ;
  }
  // 既にパートナーがいる場合 → 失敗
  if ( $user->get_partner_id() !== NULL ) {
    $_SESSION['partner_application_result'] = false ;
    $_SESSION['partner_application_text'] = 'パートナー登録申請の送信に失敗しました' ;
    return ;
  }
  // テーブルに申請を挿入
  $stmt = $pdo->prepare("
    INSERT INTO partner_applications (from_id, to_id)
    values (:from_id, :to_id)
  ") ;
  $stmt->bindValue('from_id', $from_id) ;
  $stmt->bindValue('to_id', $to_id) ;
  $stmt->execute() ;
  $_SESSION['partner_application_result'] = true ;
  $_SESSION['partner_application_text'] = 'パートナー登録申請の送信に成功しました' ;
}

// 自身への申請を抽出する
function get_all_partner_applications($pdo)
{
  // 自身のIDを取得
  $to_id = $_SESSION['user_id'] ;
  // 自身宛のパートナー申請を抽出する
  $stmt = $pdo->prepare('
    SELECT * FROM partner_applications WHERE to_id = :to_id
  ') ;
  $stmt->bindValue('to_id', $to_id) ;
  $stmt->execute() ;
  $applications = $stmt->fetchAll(PDO::FETCH_ASSOC) ;
  return $applications ;
}
// 自身が申請者となる申請を取り出す
function get_my_partner_application($pdo)
{
  // 申請者IDを取得する
  $from_id = $_SESSION['user_id'] ;
  // 申請者IDが合致する申請を抽出する
  $stmt = $pdo->prepare('SELECT * FROM partner_applications WHERE from_id = :from_id') ;
  $stmt->bindValue(':from_id', $from_id) ;
  $stmt->setFetchMode(PDO::FETCH_ASSOC) ;
  $stmt->execute() ;
  $my_application = $stmt->fetch() ;
  return $my_application ;
}
// 申請を許可する
function allow_application($pdo)
{
  // 申請先IDを取得する
  $to_id = $_SESSION['user_id'] ;
  // 許可するユーザーの申請者IDを取得する
  $from_id = filter_input(INPUT_POST, 'from_id') ;
  // 双方のパートナーIDに相手のIDを設定する
  $stmt = $pdo->prepare('
    UPDATE
      users
    SET
      partner_id = :partner_id
    WHERE
      id = :id
  ') ;
  $stmt->bindValue(':id', $to_id) ;
  $stmt->bindValue(':partner_id', $from_id) ;
  $stmt->setFetchMode(PDO::FETCH_CLASS, 'User') ;
  $stmt->execute() ;
  $stmt->bindValue(':id', $from_id) ;
  $stmt->bindValue(':partner_id', $to_id) ;
  $stmt->execute() ;
  // 双方に関係するレコードを全て削除
  $stmt = $pdo->prepare('
    DELETE FROM
      partner_applications
    WHERE
      from_id = :from_id
      OR from_id = :to_id
      OR to_id = :to_id_2
      OR to_id = :from_id_2
  ') ;
  $stmt->bindValue(':to_id', $to_id) ;
  $stmt->bindValue(':from_id', $from_id) ;
  $stmt->bindValue(':to_id_2', $to_id) ;
  $stmt->bindValue(':from_id_2', $from_id) ;
  $stmt->execute() ;
  // 申請先ユーザーへの申請を全て削除する
  sweep_applications($pdo) ;
}
// 申請を削除する
function delete_application($pdo)
{
  // 申請先IDを取得する
  $to_id = $_SESSION['user_id'] ;
  // 許可するユーザーの申請者IDを取得する
  $from_id = filter_input(INPUT_POST, 'from_id') ;
  // 申請先IDと申請者IDが合致する申請を削除する
  $stmt = $pdo->prepare('
    DELETE FROM
      partner_applications
    WHERE
      from_id = :from_id
      AND to_id = :to_id
  ') ;
  $stmt->bindValue('from_id', $from_id) ;
  $stmt->bindValue('to_id', $to_id) ;
  $stmt->execute() ;
}
function lift_partner($pdo)
{
  // 解除を選択したユーザーのID
  $user_id = $_SESSION['user_id'] ;
  // 解除されるユーザーのID
  $partner_id = filter_input(INPUT_POST, 'partner_id') ;
  // 双方のパートナーIDをNULLに更新する
  $stmt = $pdo->prepare('
    UPDATE
      users
    SET
      partner_id = NULL
    WHERE
      id = :id
  ') ;
  $stmt->bindValue(':id', $user_id) ;
  $stmt->execute() ;
  $stmt->bindValue(':id', $partner_id) ;
  $stmt->execute() ;
}
// ユーザーへの申請を全て一掃する（申請許可を出した時に呼び出す）
function sweep_applications($pdo)
{
  // 申請先IDを取得する（利用しているユーザーのID）
  $to_id = $_SESSION['user_id'] ;
  // 自身への申請を全て削除する
  $stmt = $pdo->prepare('
    DELETE FROM
      partner_applications
    WHERE
      to_id = :to_id
  ') ;
  $stmt->bindValue('to_id', $to_id) ;
  $stmt->execute() ;
}

////////////////////////////////////////////////////////////////////////////////

// ダウンロード //////////////////////////////////////////////////////////////////
// 毎年の貯蓄額を保持する csv ファイルのダウンロード
function download_savings($pdo)
{
  // ファイル名の設定
  $filename = sprintf(
    'saving_simulation_%s_%s.csv',
    $_SESSION['user_id'],
    date('Y-m-d')
  ) ;
  // ファイルパスの指定
  $filepath = __DIR__ . '/data_download/' . $filename ;
  // ファイルの作成
  make_savings_csv($pdo, $filepath) ;
  // ダウンロードの実行（ HTTP ヘッダの指定）
  header( 'Content-Type: application/csv' ) ; // ファイルのタイプ指定
  header( 'Content-Length: ' . filesize($filepath) ) ; // ファイルサイズの指定
  header( 'Content-Disposition: attachment; filename=' . $filename ) ; // ファイルの処理方法の指定（ファイル名の指定）

  readfile($filepath) ;

  // ファイルの削除
  unlink($filepath) ;

  exit ; // これを絶対忘れない！！！！
}
// 各年の貯蓄額を保持する csv ファイルの出力
function make_savings_csv($pdo, $filepath)
{
  // ファイルポインタの取得
  $fp = fopen($filepath, 'w') ;

  // 年別収入額の取得
  $incomes = IncomeSimulator::get_incomes($pdo) ;
  // 年別支出の取得
  $costs = SavingSimulator::get_costs($pdo) ;
  // 年別貯蓄額の取得
  $savings = SavingSimulator::get_savings($pdo) ;
  // 年別累計貯蓄の取得
  $cumulative_savings = SavingSimulator::get_cumulative_savings($pdo) ;

  // キー（年）と値（貯蓄額）をカンマで繋げた文字列を保持する配列
  $lines = array() ;
  $lines[] = '年,収入（控除後）[円],支出[円],貯蓄[円],累計貯蓄[円]' ;
  foreach ( array_keys($savings) as $year ) {
    $lines[] = sprintf(
      '%d,%d,%d,%d,%d',
      $year,
      $incomes[ $year ],
      $costs[ $year ],
      $savings[ $year ],
      $cumulative_savings[ $year ]
    ) ;
  }

  // $lines の各要素を改行で繋いだ文字列を取得
  $lines_combined = implode(PHP_EOL, $lines) ;

  // ファイルへ書き込む
  fwrite($fp, $lines_combined) ;

  // ファイルのクローズ
  fclose($fp) ;
}

////////////////////////////////////////////////////////////////////////////////

// 配列操作関数 //////////////////////////////////////////////////////////////////
function array_step($array, $step = 5)
{
  $array_step = array() ;
  $arrays_chunk = array_chunk($array, $step, true) ;
  foreach ($arrays_chunk as $array_chunk) {
    foreach ($array_chunk as $key => $value) {
      $array_step[ $key ] = $value ;
      break ;
    }
  }
  return $array_step ;
}
