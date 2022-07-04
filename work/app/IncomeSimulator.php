<?php
class IncomeSimulator
{
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
    $total_earning = $user->get_total_earning($pdo) ;
    // ユーザーの課税給与合計の価格帯を獲得
    $anual_earning_type = $user->get_anual_earning_type($pdo) ;
    // 所得税の情報をもつグローバル変数を使用する宣言
    global $earning_tax_info ;
    /*
      所得税を返す
      所得税 = 課税所得金額 * 税率 - (税額控除額 / 12)
    */
    $earning_tax =
      $total_earning['課税'] * $earning_tax_info[$anual_earning_type]['税率']
      - ($earning_tax_info[$anual_earning_type]['税額控除額'] / 12) ;
    return $earning_tax ;
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
    $total_earning = $user->get_total_earning($pdo) ;
    // ユーザーの課税給与合計の価格帯を獲得
    $anual_earning_type = $user->get_anual_earning_type($pdo) ;
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
    $total_earning = $user->get_total_earning($pdo) ;
    // ユーザーの給与合計（課税・非課税問わない）を獲得
    $total_earning_all = $total_earning['課税'] + $total_earning['非課税'] ;
    // ユーザーの課税給与合計の価格帯を獲得
    $anual_earning_type = $user->get_anual_earning_type($pdo) ;

    // ユーザーの在住都道府県の情報を抽出
    $stmt = $pdo->prepare("SELECT * FROM prefectures WHERE id = :prefecture_id") ;
    $stmt->bindValue('prefecture_id', $user->get_prefecture_id()) ;
    $stmt->execute() ;
    $prefecture = $stmt->fetch() ;
    /*
    各種社会保険料を計算
     - 健康保険 = 課税所得金額 * 健康保険料率
      - 健康保険料率は都道府県ごとに決まっている（毎年変動）
     - 厚生年金 = 標準報酬月額 * 厚生年金料率
      - 現状、標準報酬月額 = 課税所得金額 + 非課税所得金額 と仮定する（要勉強）
     - 雇用保険 = 標準報酬月額 * 雇用保険料率
      - 雇用保険料率は事業の種類（一般 / 農林水産・清酒製造 / 建設）で異なる
     - 労災保険 = 標準報酬月額 * 労災保険料率
      - 雇用保険料率は事業の種類（卸売、林業などなど）で異なる（労災保険率表にて公開されている）
    */
    global $prefectures_info ;
    global $business_types_info ;
    $insurance_fee = [
      '健康保険' => $total_earning_all * $prefectures_info[$user->get_prefecture_id()]['健康保険料率'],
      '厚生年金' => $total_earning_all * RATIO_WALFARE_PENSION,
      '雇用保険' => $total_earning_all * ($business_types_info[$user->get_business_type_id()]['雇用保険料率（労働者負担）'] + $business_types_info[$user->get_business_type_id()]['雇用保険料率（事業主負担）']),
      '労災保険' => $total_earning_all * ($business_types_info[$user->get_business_type_id()]['労災保険料率（労働者負担）'] + $business_types_info[$user->get_business_type_id()]['労災保険料率（事業主負担）']),
    ] ;
    return $insurance_fee ;
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
    $insurance_fee_personal = [] ;
    $insurance_fee_personal['健康保険（個人負担）'] = $insurance_fee['健康保険'] / 2 ;
    $insurance_fee_personal['厚生年金（個人負担）'] = $insurance_fee['厚生年金'] / 2 ;
    $insurance_fee_personal['雇用保険（個人負担）'] = $insurance_fee['雇用保険'] / 3 ;
    $insurance_fee_personal['労災保険（個人負担）'] = 0 ;
    return $insurance_fee_personal ;
  }

  // 手取りの計算 /////////////////////////////////////////////////////////////////
  public static function calc_residual($pdo)
  {
    // ユーザーIDの取得
    $user_id = $_SESSION['user_id'] ;
    // ユーザー情報を抽出
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id") ;
    $stmt->bindValue('user_id', $user_id) ;
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'User') ;
    $stmt->execute() ;
    $user = $stmt->fetch() ;
    // ユーザーの給与合計（課税・非課税問わない）を獲得
    $total_earning_all = $user->get_total_earning_all($pdo) ;
    /*
    ユーザーの手取りを計算
      手取り = 給与額 - (所得税 + 住民税 + 社会保険料)
    */
    // 各種税金・社会保険料（個人負担）の合計
    $total_deducations_personal = array_sum( IncomeSimulator::get_all_deducations_personal($pdo) ) ;
    $net_income = $total_earning_all - $total_deducations_personal ;
    return $net_income ;
  }

  // 各種税金・社会保険料（個人負担）をまとめる /////////////////////////////////////////////////
  public static function get_all_deducations_personal($pdo)
  {
    /*
    $all_deducations['所得税']：所得税（整数値）
    $all_deducations['住民税']：住民税（整数値）
    $all_deducations['社会保険（個人負担）']：社会保険料（配列）
      - $all_deducations['社会保険（個人負担）']['健康保険']：健康保険料（うち個人負担分）（整数値）
      - $all_deducations['社会保険（個人負担）']['厚生年金']：厚生年金（うち個人負担分）（整数値）
      - $all_deducations['社会保険（個人負担）']['雇用保険']：雇用保険（うち個人負担分）（整数値）
      - $all_deducations['社会保険（個人負担）']['労災保険']：労災保険（うち個人負担分）（整数値）
    */
    $insurance_fee_personal = IncomeSimulator::calc_personal_burden_insurance($pdo) ;
    $all_deducations = [
      '所得税' => IncomeSimulator::calc_earning_tax($pdo),
      '住民税' => IncomeSimulator::calc_resident_tax($pdo),
    ] ;
    $all_deducations = array_merge($all_deducations, $insurance_fee_personal) ;
    return $all_deducations ;
  }
  // 年別の収入を返す関数
  public static function get_incomes($pdo)
  {
    // ユーザー情報を抽出
    $user_id = $_SESSION['user_id'] ;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id") ;
    $stmt->bindValue('user_id', $user_id) ;
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'User') ;
    $stmt->execute() ;
    $user = $stmt->fetch() ;
    // ユーザーの月当たりの手取りを求める
    $residual_monthly = IncomeSimulator::calc_residual($pdo) ;
    // 年別の手取り合計を求める
    $retirement_year = $user->get_retirement_year() ;
    $age = $user->get_age() ;
    $residuals = array_combine(
      range(date('Y'), $retirement_year),
      array_fill($age, 65 - $age + 1, 0)
    ) ;
    $current = new DateTime() ;
    $retirement_ym = new DateTime( $retirement_year . '-12' ) ;
    while ( $current <= $retirement_ym ) {
      $residuals[ $current->format('Y') ] += $residual_monthly ;
      $current->modify('+ 1 months') ;
    }
    return $residuals ;
  }
}
