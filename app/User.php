<?php

class User
{
  private $id ; // ユーザーID
  private $password ; // パスワード
  private $email ; // メールアドレス
  private $date_of_birth ; // 生年月日
  private $business_type_id ; // 事業種ID
  private $prefecture_id ; // 都道府県ID
  private $dependents_num ; // 扶養人数
  private $partner_id ; // パートナーID
  // ユーザーIDを返す関数
  public function get_id()
  {
    return $this->id ;
  }
  // ユーザーのパスワードを返す関数
  public function get_password()
  {
    return $this->password ;
  }
  // ユーザーの生年月日を返す関数
  public function get_date_of_birth()
  {
    return $this->date_of_birth ;
  }
  // ユーザーのパートナーIDを返す関数
  public function get_partner_id()
  {
    return $this->partner_id ;
  }
  // ユーザーの年齢を返す関数
  public function get_age()
  {
    $date = new DateTime($this->date_of_birth) ;
    $now = new DateTime() ;
    return $now->diff($date)->y ;
  }
  // ユーザーが定年を迎える西暦を返す関数
  public function get_retirement_year()
  {
    $date = new DateTime($this->date_of_birth) ;
    $now = new DateTime() ;
    $diff = $now->diff($date) ;
    $retirement_year = date('Y') + (65 - $diff->y) ;
    return $retirement_year ;
  }
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
  // ユーザーの扶養人数を返す関数
  public function get_dependents_num()
  {
    return $this->dependents_num ;
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
  // 月の給与から求めたユーザーの年収を返す関数
  public function get_anual_earning($pdo)
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
    return $assumed_anual_earning ;
  }
  // ユーザーの年収価格帯を返す関数
  public function get_anual_earning_type($pdo)
  {
    // 月の給与から求めたユーザーの年収を獲得
    $assumed_anual_earning = $this->get_anual_earning($pdo);
    // 所得税の情報をもつグローバル変数の使用を宣言
    global $earning_tax_info ;
    // 課税年収がどの年収価格帯かを返す
    foreach ($earning_tax_info as $earning_tax_data) {
      if (
        $assumed_anual_earning['課税'] >= $earning_tax_data['下限'] &&
        $assumed_anual_earning['課税'] <= $earning_tax_data['上限']
      )
        return $earning_tax_data['年収価格帯'] ;
    }
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
  // ユーザーの支出項目を抽出する
  public function get_cost_items($pdo)
  {
    // ユーザーの支出項目を抽出する
    $stmt = $pdo->prepare("SELECT * FROM cost_items WHERE user_id = :user_id") ;
    $stmt->bindValue('user_id', $this->id) ;
    $stmt->execute() ;
    $cost_items = $stmt->fetchAll(PDO::FETCH_ASSOC) ;
    return $cost_items ;
  }
}
