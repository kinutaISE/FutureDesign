<?php

session_start() ;

define('DNS', 'mysql:host=db;dbname=myapp;charset=utf8mb4') ;
define('DB_USER', 'myappuser') ;
define('DB_PASS', 'myapppass') ;
define('SITE_URL', 'http://' . $_SERVER['HTTP_HOST']) ;

$prefecture_names = [
  '北海道',
  '青森県',
  '岩手県',
  '宮城県',
  '秋田県',
  '山形県',
  '福島県',
  '茨城県',
  '栃木県',
  '群馬県',
  '埼玉県',
  '千葉県',
  '東京都',
  '神奈川県',
  '新潟県',
  '富山県',
  '石川県',
  '福井県',
  '山梨県',
  '長野県',
  '岐阜県',
  '静岡県',
  '愛知県',
  '三重県',
  '滋賀県',
  '京都府',
  '大阪府',
  '兵庫県',
  '奈良県',
  '和歌山県',
  '鳥取県',
  '島根県',
  '岡山県',
  '広島県',
  '山口県',
  '徳島県',
  '香川県',
  '愛媛県',
  '高知県',
  '福岡県',
  '佐賀県',
  '長崎県',
  '熊本県',
  '大分県',
  '宮崎県',
  '鹿児島県',
  '沖縄県'
] ;

$anual_income_type_names = [
  '195万円以下',
  '195万円超、330万円以下',
  '330万円超、695万円以下',
  '695万円超、900万円以下',
  '900万円超、1,800万円以下',
  '1,800万円越、4,000万円以下',
  '4,000万円越'
] ;

require_once(__DIR__ . '/Database.php') ;
require_once(__DIR__ . '/User.php') ;
require_once(__DIR__ . '/IncomeSimulator.php') ;
require_once(__DIR__ . '/functions.php') ;
