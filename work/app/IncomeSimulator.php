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
  public static function calc_earning_tax($pdo)
  {
    // ユーザーIDの取得
    $user_id = $_SESSION['user_id'] ;
    // ユーザー情報を抽出
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id") ;
    $stmt->bindValue('user_id', $user_id) ;
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'User') ;
    $stmt->execute() ;
    $user = $stmt->fetch() ;
    // ユーザーの給与合計（課税・非課税別）を獲得
    $total_earning = $user->get_anual_earning() ;
    // ユーザーの課税給与合計の価格帯を獲得
    $anual_earning_type = $user->get_anual_earning_type() ;
    /*
      所得税を返す
      所得税 = 課税所得金額 * 税率 - (税額控除額 / 12)
    */
    $earnings_tax =
      $total_earning['課税'] * IncomeSimulator::::$income_tax_rate[$anual_earning_type]
      - IncomeSimulator::$deducation[$anual_earning_type] / 12 ;
    return $earnings_tax ;
  }

  // 住民税の計算 //////////////////////////////////////////////////
  public static function calc_resident_tax($pdo)
  {
    // ユーザーIDの取得
    $user_id = $_SESSION['user_id'] ;
    // ユーザー情報の抽出
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id") ;
    $stmt->bindValue('user_id', $user_id) ;
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'User') ;
    $stmt->execute() ;
    $user = $stmt->fetch() ;
    // ユーザーの給与合計（課税・非課税別）を獲得
    $total_earning = $user->get_anual_earning() ;
    // ユーザーの課税給与合計の価格帯を獲得
    $anual_earning_type = $user->get_anual_earning_type() ;
    /*
      住民税を返す
      住民税 = 課税所得金額 * 住民税率
      - ただし、住民税率は課税所得金額（1年分）が、
       - 195 万円以下の場合は 0 %
       - それより高い場合は 10 %
    */
    $resident_tax =
      $total_earning['課税'] * ( ($anual_earning_type === 'range_1') ? 0 : 0.1 ) ;
    return $resident_tax ;
  }
  // 社会保険料の計算 ////////////////////////////////////////////////////////////
  public static function calc_insurance($pdo)
  {
    // ユーザーIDの取得
    $user_id = $_SESSION['user_id'] ;
    // ユーザー情報を抽出
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id") ;
    $stmt->bindValue('user_id', $user_id) ;
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'User') ;
    $stmt->execute() ;
    $user = $stmt->fetch() ;
    // ユーザーの給与合計（課税・非課税別）を獲得
    $total_earning = $user->get_anual_earning() ;
    // ユーザーの課税給与合計の価格帯を獲得
    $anual_earning_type = $user->get_anual_earning_type() ;

    // ユーザーの在住都道府県の情報を抽出
    $stmt = $pdo->prepare("SELECT * FROM prefectures WHERE id = :prefecture_id") ;
    $stmt->bindValue('prefecture_id', $user->prefecture_id) ;
    $stmt->execute() ;
    $prefecture = $stmt->fetch() ;
    /*
    各種社会保険料を計算
     - 健康保険 = 課税所得金額 * 健康保険料率
      - 健康保険料率は都道府県ごとに決まっている（毎年変動）
     - 厚生年金 = 標準報酬月額 * 厚生年金の保険料率
      - 現状、標準報酬月額 = 課税所得金額 + 非課税所得金額 と仮定する（要勉強）
     - 雇用保険 = 標準報酬月額 * 雇用保険の保険料率
      - 雇用保険の保険料率は事業の種類（一般 / 農林水産・清酒製造 / 建設）で異なる
      - 現状、一般の事業として計算をする（ユーザーの事業形態のオプションも未設定）
     - 労災保険 = 
    */
    $insurance_fee = [
      'health' => $user->income * $prefecture->health_insurance_rate,
      'walfare_pension' => $user->income * RATIO_WALFARE_PENSION,
      'employee' => $user->income * RATIO_EMPLOYEE,
      'accident' => $user->income * RATIO_ACCIDENT
    ] ;
  }

  // 社会保険料（個人負担）の計算 //////////////////////////////////////////////////
  public static function calc_personal_burden_insurance($pdo)
  {
    // 各種社会保険料の算出
    $insurance_fee = IncomeSimulator::calc_insurance($pdo) ;
    /*
    うち個人負担を計算（以下は個人負担の割合）
      - 健康保険：1/2
      - 厚生年金：1/2
      - 雇用保険：1/3
      - 労災保険：0
    */

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
      IncomeSimulator::calc_earning_tax($pdo) +
      IncomeSimulator::calc_resident_tax($pdo) +
      IncomeSimulator::calc_personal_burden_insurance($pdo)
    ) ;
  }
}
