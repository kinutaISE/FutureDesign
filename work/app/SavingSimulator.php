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
}
