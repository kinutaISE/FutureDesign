<?php

require_once('../app/config.php') ;
include('_parts/_header.php') ;

$pdo = Database::getInstance() ;
// 各種税金・社会保険の内訳
$all_deducations_personal = IncomeSimulator::get_all_deducations_personal($pdo) ;
?>

<body>
  <p>手取り：<?= number_format( IncomeSimulator::calc_residual($pdo) ) ;?>円</p>

  <!-- 各種税金・社会保険の内訳 -->
  <ul>
  <?php foreach ($all_deducations_personal as $key => $amount):?>
    <li><?= $key . '：' . number_format($amount) . '円' ;?></li>
  <?php endforeach ; ?>
  </ul>

  <p>支出総額：<?= number_format( SavingSimulator::calc_total_cost($pdo) ) . '円' ;?></p>

  <p>貯蓄額：<?= number_format( SavingSimulator::calc_monthly_saving($pdo) ) . '円' ;?></p>

  <p><a href="mypage.php">戻る</a></p>
</body>

<?php

include('_parts/_footer.php') ;
