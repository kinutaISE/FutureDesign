<?php

session_start() ;

define('DNS', 'mysql:host=db;dbname=myapp;charset=utf8mb4') ;
define('DB_USER', 'myappuser') ;
define('DB_PASS', 'myapppass') ;
define('SITE_URL', 'http://' . $_SERVER['HTTP_HOST']) ;
define('PREFECTURES_FILENAME', __DIR__ . '/data/prefectures.csv') ;
define('BUSINESS_TYPES_FILENAME', __DIR__ . '/data/business_types.csv') ;
define('EARNING_TAX_FILENAME', __DIR__ . '/data/earning_tax.csv') ;

$anual_income_type_names = [
  '195万円以下',
  '195万円超、330万円以下',
  '330万円超、695万円以下',
  '695万円超、900万円以下',
  '900万円超、1,800万円以下',
  '1,800万円超、4,000万円以下',
  '4,000万円超',
] ;

require_once(__DIR__ . '/functions_initialization.php') ;
$prefectures_info = get_prefectures_info() ; // 都道府県の情報（都道府県ID, ）
$business_types_info = get_business_types_info() ; // 事業種の情報
$earning_tax_info = get_earning_tax_info() ; // 所得税の情報
define('RATIO_WALFARE_PENSION', 0.183) ; // 厚生年金の保険料率
define('RATIO_EMPLOYEE', 0.009) ; // 雇用保険の保険料率
define('RATIO_ACCIDENT', 0.03) ; // 労災保険の保険料率

require_once(__DIR__ . '/Database.php') ;
require_once(__DIR__ . '/User.php') ;
require_once(__DIR__ . '/EarningItem.php') ;
require_once(__DIR__ . '/IncomeSimulator.php') ;
require_once(__DIR__ . '/functions.php') ;
