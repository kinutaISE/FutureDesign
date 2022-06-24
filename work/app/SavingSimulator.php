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
    $user_id = filter_input(INPUT_POST, 'user_id') ;
    // ユーザー情報を抽出
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id") ;
    $stmt->bindValue('user_id', $user_id) ;
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'User') ;
    $stmt->execute() ;
    $user = $stmt->fetch() ;
    //

  }
}
