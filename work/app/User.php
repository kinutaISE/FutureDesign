<?php

class User
{
  private $id ; // ユーザーID
  private $password ; // パスワード
  private $email ; // メールアドレス
  private $age ; // 年齢
  private $business_type_id ; // 事業種ID
  private $prefecture_id ; // 都道府県ID
  private $dependents_num ; // 扶養人数
  private $partner_id ; // パートナーID
  // ユーザーの事業種IDを返す関数
  public function get_business_type_id()
  {
    return $this->business_type_id ;
  }
  // ユーザーの都道府県IDを返す関数
  public function get_prefecture_id()
  {
    return $this->prefecture_id ;
  }
  // ユーザーの給与項目を抽出する関数
  public function get_earning_items($pdo)
  {
    $stmt = $pdo->prepare("SELECT * FROM earnings WHERE user_id = :id") ;
    $stmt->bindValue('id', $this->id) ;
    $stmt->execute() ;
    return $stmt->fetchAll() ;
  }
  // ユーザーの給与項目の合計を課税・非課税別に返す関数
  public function get_total_earning($pdo)
  {
    // ユーザーの給与項目を抽出
    $earning_items = $this->get_earning_items($pdo) ;
    // $total['課税'] は課税項目の合計値、$total['非課税'] は非課税項目の合計値
    $total = ['課税' => 0, '非課税' => 0] ;
    // 課税・非課税別に合計を算出
    foreach ($earning_items as $earning_item) {
      $key = ($earning_item->is_taxation) ? '課税' : '非課税' ;
      $total[$key] += $earning_item->amount ;
    }
    return $total ;
  }
  // ユーザーの年収価格帯を返す関数
  public function get_anual_earning_type($pdo)
  {
    /*
    get_earning_items($pdo) で返ってくる配列の各要素に対して、
    第一引数で指定した無名関数の処理を実行した値をもつ配列を返す
    （キーの値（課税・非課税）は保持される）
    */
    $assumed_anual_earning = array_map(
      function ($amount) {
        return $amount * 12 ;
      } ,
      $this->get_total_earning($pdo)
    ) ;
    if ($assumed_anual_earning['課税'] <= 1950000)
      return 'range_1' ;
    else if ($assumed_anual_earning['課税'] <= 3300000)
      return 'range_2' ;
    else if ($assumed_anual_earning['課税'] <= 6950000)
      return 'range_3' ;
    else if ($assumed_anual_earning['課税'] <= 9000000)
      return 'range_4' ;
    else if ($assumed_anual_earning['課税'] <= 18000000)
      return 'range_5' ;
    else if ($assumed_anual_earning['課税'] <= 40000000)
      return 'range_6' ;
    return 'range_7' ;
  }
  // 課税・非課税を問わない、ユーザーの給与合計を返す
  public function get_total_earning_all($pdo)
  {
    // ユーザーの給与項目を抽出
    $earning_items = $this->get_earning_items($pdo) ;
    // $totalは合計値
    $total = 0 ;
    // 課税・非課税別に合計を算出
    foreach ($earning_items as $earning_item)
      $total += $earning_item->amount ;
    return $total ;
  }
}
