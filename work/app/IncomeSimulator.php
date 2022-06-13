<?php

define('RATIO_WALFARE_PENSION', 0.183) ; // 厚生年金の保険料率（うち 1/2 個人負担）
define('RATIO_EMPLOYEE', 0.009) ; // 雇用保険の保険料率（うち 1/3 個人負担）
define('RATIO_ACCIDENT', 0.003) ; // 労災保険の保険料率（会社が全額負担）

class IncomeSimulator
{
  // 所得税率
  private static $income_tax_rate = [
    'range_1' => 0.05,
    'range_2' => 0.1,
    'range_3' => 0.2,
    'range_4' => 0.23,
    'range_5' => 0.33,
    'range_6' => 0.4,
    'range_7' => 0.44
  ] ;
  // 控除額
  private static $deducation = [
    'range_1' => 0,
    'range_2' => 97500,
    'range_3' => 427500,
    'range_4' => 636000,
    'range_5' => 1536000,
    'range_6' => 2796000,
    'range_7' => 4796000
  ] ;
  // 所得税の計算 //////////////////////////////////////////////////
  public static function calc_income_tax($pdo)
  {
    $user_id = $_SESSION['user_id'] ;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id") ;
    $stmt->bindValue('user_id', $user_id) ;
    $stmt->execute() ;
    $user = $stmt->fetch() ;
    // 所得税率が考慮されていない！！！！！
    return ($user->income - IncomeSimulator::$deducation[$user->anual_income_type]) ;
  }

  // 住民税の計算 ////////////////////////////////////////////////////
  public static function calc_resident_tax($pdo)
  {
    $user_id = $_SESSION['user_id'] ;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id") ;
    $stmt->bindValue('user_id', $user_id) ;
    $stmt->execute() ;
    $user = $stmt->fetch() ;
    // 年収が考慮されていない！！！
    return 0 ;
  }

  // 社会保険料の計算 //////////////////////////////////////////////////
  public static function calc_personal_burden_insurance($pdo)
  {
    $user_id = $_SESSION['user_id'] ;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id") ;
    $stmt->bindValue('user_id', $user_id) ;
    $stmt->execute() ;
    $user = $stmt->fetch() ;

    $stmt = $pdo->prepare("SELECT * FROM prefectures WHERE id = :prefecture_id") ;
    $stmt->bindValue('prefecture_id', $user->prefecture_id) ;
    $stmt->execute() ;
    $prefecture = $stmt->fetch() ;
    $insurance_fee = [
      'health' => $user->income * $prefecture->health_insurance_rate,
      'walfare_pension' => $user->income * RATIO_WALFARE_PENSION,
      'employee' => $user->income * RATIO_EMPLOYEE,
      'accident' => $user->income * RATIO_ACCIDENT
    ] ;
    return $insurance_fee['health'] / 2 +
      $insurance_fee['walfare_pension'] / 2 +
      $insurance_fee['employee'] / 3 +
      $insurance_fee['accident'] * 0 ;
  }

  // 手取りの計算 /////////////////////////////////////////////////////////////////
  public static function calc_residual($pdo)
  {
    $user_id = $_SESSION['user_id'] ;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id") ;
    $stmt->bindValue('user_id', $user_id) ;
    $stmt->execute() ;
    $user = $stmt->fetch() ;
    return $user->income - (
      IncomeSimulator::calc_income_tax($pdo) +
      IncomeSimulator::calc_resident_tax($pdo) +
      IncomeSimulator::calc_personal_burden_insurance($pdo)
    ) ;
  }
}
