<?php

define('RATIO_HEALTH', 0.1) ; // 健康保険の保険料率（うち 1/2 個人負担）
define('RATIO_WALFARE_PENSION', 0.183) ; // 厚生年金の保険料率（うち 1/2 個人負担）
define('RATIO_EMPLOYEE', 0.009) ; // 雇用保険の保険料率（うち 1/3 個人負担）
define('RATIO_ACCIDENT', 0.003) ; // 労災保険の保険料率（会社が全額負担）

class IncomeCalculator
{  
  // 所得税の計算 //////////////////////////////////////////////////
  public static function calc_income_tax($income, $anual_income_type)
  {
    // 所得税率
    $income_tax_rate = [
      '年収価格帯A' => 0.05,
      '年収価格帯B' => 0.1,
      '年収価格帯C' => 0.2,
    ] ;
    // 控除額
    $deducation = [
      '年収価格帯A' => 0,
      '年収価格帯B' => 97500,
      '年収価格帯C' => 427500,
    ] ;
    return ($income - $deducation[$anual_income_type]) * $income_tax_rate[$anual_income_type] ;
  }

  // 住民税税の計算 //////////////////////////////////////////////////
  public static function calc_resident_tax($income, $anual_income_type)
  {
    return $income * ( ($anual_income_type === '年収価格帯A') ? 0 : 0.1 ) ;
  }

  // 社会保険料の計算 //////////////////////////////////////////////////
  public static function calc_personal_burden_insurance($income)
  {
    $insurance_fee = [
      'health' => $income * RATIO_HEALTH,
      'walfare_pension' => $income * RATIO_WALFARE_PENSION,
      'employee' => $income * RATIO_EMPLOYEE,
      'accident' => $income * RATIO_ACCIDENT
    ] ;
    return $insurance_fee['health'] / 2 +
      $insurance_fee['walfare_pension'] / 2 +
      $insurance_fee['employee'] / 3 +
      $insurance_fee['accident'] * 0 ;
  }

  // 手取りの計算 /////////////////////////////////////////////////////////////////
  public static function calc_residual($income, $anual_income_type)
  {
    return $income - (
      IncomeCalculator::calc_income_tax($income, $anual_income_type) +
      IncomeCalculator::calc_resident_tax($income, $anual_income_type) +
      IncomeCalculator::calc_personal_burden_insurance($income)
    ) ;
  }
}
