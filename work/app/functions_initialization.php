<?php
/*
以下 3 つの関数はほとんど処理が同じであるため、後々以下の関数を実装することを検討：
function set_file_info($file_name)
  - 引数：
    - $file_name：csv ファイル名
  - 処理：
    - 属性名（ファイルの先頭行の各項目）をキーとした配列を要素としてもつ配列を返す
    - 出力する配列を $array とする
    - $array の i 番目の要素は以下のようになる想定：
      $array[レコード i + 1 の'属性名1'] = [
        '属性名1' => レコード i + 1 の'属性名1',
        '属性名2' => レコード i + 1 の'属性名2',
        ...
      ]
*/
// 都道府県情報が書いてあるファイルを読み込み、配列として返す関数
function get_prefectures_info($file_name = PREFECTURES_FILENAME)
{
  // $lines：行を各要素とした配列
  $lines = file($file_name, FILE_IGNORE_NEW_LINES) ;
  // 属性名を取り出す
  $attributes = explode(',', $lines[0]) ;
  // 各行をカンマ区切りで分割し、各項目を取り出す
  /*
  prefectures_info['01'] = [
    '都道府県ID' => '01',
    '都道府県名' => '北海道',
    '健康保険料率' => '0.1039'
  ] ;
  */
  $prefectures_info = [] ;
  for ($i = 1 ; $i < count($lines) ; $i++) {
    $data = explode(',', $lines[$i]) ;
    $prefectures_info[ $data[0] ] = array_combine($attributes, $data) ;
  }
  return $prefectures_info ;
}
// 事業種情報が書いてあるファイルを読み込み、配列として返す関数
function get_business_types_info($file_name = BUSINESS_TYPES_FILENAME)
{
  // $lines：行を各要素とした配列
  $lines = file($file_name, FILE_IGNORE_NEW_LINES) ;
  // 属性名を取り出す
  $attributes = explode(',', $lines[0]) ;
  // 各行をカンマ区切りで分割し、各項目を取り出す
  /*
  business_types_info['02'] = [
    '事業種ID' => '02',
    '事業種名' => '林業',
    '雇用保険料率（労働者負担）' => 000.4,
    '雇用保険料率（事業主負担）' => 000.7,
    '労災保険料率（労働者負担）' => 0,
    '労災保険料率（事業主負担）' => 0.06,
  ] ;
  */
  $business_types_info = [] ;
  for ($i = 1 ; $i < count($lines) ; $i++) {
    $data = explode(',', $lines[$i]) ;
    $business_types_info[ $data[0] ] = array_combine($attributes, $data) ;
  }
  return $business_types_info ;
}
// 所得税率が書いてあるファイルを読み込み、配列として返す関数
function get_earning_tax_info($file_name = EARNING_TAX_FILENAME)
{
  // $lines：行を各要素とした配列
  $lines = file($file_name, FILE_IGNORE_NEW_LINES) ;
  // 属性名を取り出す
  $attributes = explode(',', $lines[0]) ;
  // 各行をカンマ区切りで分割し、各項目を取り出す
  /*
  earning_tax_info['range_1'] = [
    '年収価格帯' => 'range_1',
    '下界' => '0',
    '上界' => '19500000',
    '税率' => '0.05',
    '税額控除額' => 0,
  ] ;
  */
  $earning_tax_info = [] ;
  for ($i = 1 ; $i < count($lines) ; $i++) {
    $data = explode(',', $lines[$i]) ;
    $earning_tax_info[ $data[0] ] = array_combine($attributes, $data) ;
  }
  return $earning_tax_info ;
}
