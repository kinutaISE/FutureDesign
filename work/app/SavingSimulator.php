<?php

class SavingSimulator
{
  // 月当たりの貯蓄額を計算する関数
  public static function calc_monthly_saving($pdo)
  {
    // ユーザーの手取りを取得
    $residual_earnings = IncomeSimulator::calc_residual($pdo) ;
    // ユーザーの支出額合計を取得
    $total_cost = SavingSimulator::calc_total_cost($pdo) ;
    // 月当たりの貯蓄額（= 手取り - 支出）を計算し返す
    $saving = $residual_earnings - $total_cost ;
    return $saving ;
  }
  // 月当たりの支出合計を求る
  public static function calc_total_cost($pdo)
  {
    // ユーザーIDを取得する
    $user_id = $_SESSION['user_id'] ;
    // ユーザー情報を抽出
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id") ;
    $stmt->bindValue('user_id', $user_id) ;
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'User') ;
    $stmt->execute() ;
    $user = $stmt->fetch() ;
    // ユーザーの支出項目を全て取得する
    $cost_items = $user->get_cost_items($pdo) ;
    // 合計金額を求め、返す
    $total_cost = 0 ;
    foreach ($cost_items as $cost_item)
      $total_cost += $cost_item['value'] ;
    return $total_cost ;
  }
  // 各年の貯蓄額を計算し配列で返す関数
  public static function get_savings($pdo)
  {
    // ユーザーIDを取得する
    $user_id = $_SESSION['user_id'] ;
    // ユーザー情報を取得する
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id") ;
    $stmt->bindValue('user_id', $user_id) ;
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'User') ;
    $stmt->execute() ;
    $user = $stmt->fetch() ;
    // ユーザーが定年を迎える西暦を求める
    $retirement_year = $user->get_retirment_year() ;
    // 各年の支出額をもつ配列の取得
    $costs = SavingSimulator::get_costs($pdo) ;
    /*
    各年の貯蓄額を作成する配列の作成
      - キーの始まり：来年の西暦
      - キーの終わり：ユーザーが定年を迎える西暦
    */
    $savings = array_combine(
      array_fill(1, 65, 0),
      range(date('Y'), $retirement_year)
    ) ;

  }
  public static function get_costs($pdo)
  {
    // ユーザーIDを取得する
    $user_id = $_SESSION['user_id'] ;
    // ユーザー情報を取得する
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id") ;
    $stmt->bindValue('user_id', $user_id) ;
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'User') ;
    $stmt->execute() ;
    $user = $stmt->fetch() ;
    // ユーザーが定年を迎える西暦を求める
    $retirement_year = $user->get_retirement_year() ;

    /*
    各年の支出額を作成する配列の作成
      - キーの始まり：来年の西暦
      - キーの終わり：ユーザーが定年を迎える西暦
      - 初期値：常にかかる支出の合計額（$total_cost）
    */
    $age = $user->get_age() ;
    $costs = array_combine(
      range(date('Y'), $retirement_year),
      array_fill($age, 65 - $age + 1, 0)
    ) ;

    // 常に発生するものから処理する
    // ユーザーの支出項目を取得する（単位は「日」「週」「月」）
    $stmt = $pdo->prepare("
      SELECT
        *
      FROM
        cost_items
      WHERE
        term = 'constant' AND
        user_id = :user_id
    ") ;
    $stmt->bindValue('user_id', $user_id) ;
    $stmt->execute() ;
    $cost_items_const = $stmt->fetchAll(PDO::FETCH_ASSOC) ;

    // 毎年かかる支出の合計を求める
    foreach ($cost_items_const as $cost_item) {
      $frequency_splited = explode(' ', $cost_item['frequency']) ;
      $current = new DateTime() ;
      $retirement_ym = new DateTime($retirement_year . '-12') ;
      $modifier = sprintf('+ %d %s', $frequency_splited[0], $frequency_splited[1]) ;
      while ( $current <= $retirement_ym ) {
        $costs[ $current->format('Y') ] += $cost_item['value'] ;
        $current->modify($modifier) ;
      }
    }

    // 特定の期間にのみ発生する支出を加算する
    // ユーザーの支出項目を取得する
    $stmt = $pdo->prepare("
      SELECT
        *
      FROM
        cost_items
      WHERE
        user_id = :user_id AND
        term != 'constant'
    ") ;
    $stmt->bindValue(':user_id', $user_id) ;
    $stmt->execute() ;
    $cost_items_const = $stmt->fetchAll(PDO::FETCH_ASSOC) ;
    // 毎年でない支出項目を考慮する
    // ユーザーの支出項目を取得する（単位は「*年」(* != 1)）
    $stmt = $pdo->prepare("
      SELECT
        *
      FROM
        cost_items
      WHERE
        user_id = :user_id AND
        term = 'constant' AND
        frequency != '1 years' AND frequency LIKE '%years'
    ") ;
    $stmt->bindValue(':user_id', $user_id) ;
    $stmt->execute() ;
    $cost_items = $stmt->fetchAll(PDO::FETCH_ASSOC) ;
    foreach ($cost_items as $cost_item) {
      $splited_cost_item = explode(' ', $cost_item) ;
      $span = $splited_cost_item[0] ;
      for ($year = date('Y') ; $year <= $retirement_year ; $year += $span)
        $costs[$year] += $cost_item['value'] ;
    }

    // 発生する期間が指定されているものを考慮する
    // ユーザーの支出項目を取得する（単位は「日」「週」「月」）
    $stmt = $pdo->prepare("
      SELECT
        *
      FROM
        cost_items
      WHERE
        user_id = :user_id AND
        term != 'constant'
    ") ;
    $stmt->bindValue(':user_id', $user_id) ;
    $stmt->execute() ;
    $cost_items = $stmt->fetchAll(PDO::FETCH_ASSOC) ;
    foreach ($cost_items_const as $cost_item) {
      // 支出が発生する期間の開始年月、終了年月を取得する
      $term_splited = explode('~', $cost_item['term']) ;
      $term_start = new DateTime($term_splited[0]) ;
      $term_finish = new DateTime($term_splited[1]) ;
      /*
      支出が発生する頻度を取り出す
        - $frequency_splited[0]：数値
        - $frequency_splited[1]：単位
          - 例）3 months ならば
            - $frequency_splited[0] = 3
            - $frequency_splited[1] = months
      */
      $frequency_splited = explode(' ', $cost_item['frequency']) ;
      // 追加する日数・月数・年数の文字列を取得
      $modifier = sprintf('+ %d %s', $frequency_splited[0], $frequency_splited[1]) ;
      // 終了年月まで該当する年の支出に加算する
      $current_ym = $term_start ;
      while ($current_ym <= $term_finish) {
        $costs[ $current_ym->format('Y') ] += $cost_item['value'] ;
        $current_ym->modify($modifier) ;
      }
    }
    // 各年の支出額をもつ配列を返す
    return $costs ;
  }
}
