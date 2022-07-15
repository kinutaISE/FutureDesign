<?php

session_start() ;

define('DNS', 'mysql:host=us-cdbr-east-06.cleardb.net;dbname=heroku_c4ded9396eaa1f3;charset=utf8mb4') ;
define('DB_USER', 'bf0d085a774c6d') ;
define('DB_PASS', '33ccb7df') ;
define('SITE_URL', 'http://' . $_SERVER['HTTP_HOST']) ;
define('PREFECTURES_FILENAME', __DIR__ . '/data/prefectures.csv') ;
define('BUSINESS_TYPES_FILENAME', __DIR__ . '/data/business_types.csv') ;
define('EARNING_TAX_FILENAME', __DIR__ . '/data/earning_tax.csv') ;
define('RETIREMENT_OLD', 65) ; // 定年

$anual_income_type_names = [
  '195万円以下',
  '195万円超、330万円以下',
  '330万円超、695万円以下',
  '695万円超、900万円以下',
  '900万円超、1,800万円以下',
  '1,800万円超、4,000万円以下',
  '4,000万円超',
] ;

spl_autoload_register( function ($class) {
  // 前提：クラス定義ファイルは、同じフォルダ階層に存在し、「クラス名.php」となっている
  // ファイル名の取得
  $filename = sprintf(__DIR__ . '/%s.php', $class) ;
  // ファイルが存在しない場合はエラー出力して終了
  if ( file_exists($filename) )
    require($filename) ;
  else {
    echo 'File not found: ' . $filename ;
    exit ;
  }
}) ;

require_once(__DIR__ . '/functions.php') ;

$prefectures_info = set_file_info( PREFECTURES_FILENAME ) ; // 都道府県の情報（都道府県ID, ）
$business_types_info = set_file_info( BUSINESS_TYPES_FILENAME ) ; // 事業種の情報
$earning_tax_info = set_file_info( EARNING_TAX_FILENAME ) ; // 所得税の情報
define('RATIO_WALFARE_PENSION', 0.183) ; // 厚生年金の保険料率
define('RATIO_EMPLOYEE', 0.009) ; // 雇用保険の保険料率
define('RATIO_ACCIDENT', 0.03) ; // 労災保険の保険料率
define('LAST_UPDATE', date()) ; // 最終更新日
