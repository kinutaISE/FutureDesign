<?php

class User
{
  private $id ; // ユーザーID
  private $password ; // パスワード
  private $email ; // メールアドレス
  private $age ; // 年齢
  private $dependents_num ; // 扶養人数
  private $prefecture_id ; // 都道府県ID
  private $income ; // 給与（額面）
  private $partner_id ; // パートナーID
  public function get_income()
  {
    return $this->income ;
  }
  public function get_anual_income_type()
  {
    $assumed_anual_income = $this->income * 12 ;
    if ($assumed_anual_income <= 1950000)
      return 'range_1' ;
    else if ($assumed_anual_income <= 3300000)
      return 'range_2' ;
    else if ($assumed_anual_income <= 6950000)
      return 'range_3' ;
    else if ($assumed_anual_income <= 9000000)
      return 'range_4' ;
    else if ($assumed_anual_income <= 18000000)
      return 'range_5' ;
    else if ($assumed_anual_income <= 40000000)
      return 'range_6' ;
    return 'range_7' ;
  }
}
