<?php

require_once('../app/config.php') ;

include_once('_parts/_header.php') ;

$pdo = Database::getInstance() ;

$incomes = IncomeSimulator::get_incomes($pdo) ;
$costs = SavingSimulator::get_costs($pdo) ;
$savings = SavingSimulator::get_savings($pdo) ;

?>

<body>
<!--
  年別支出額：
  <ul>
    <?php foreach ($costs as $year => $cost):?>
      <li><?= $year . ':' . number_format($cost) . '円' ;?></li>
    <?php endforeach ; ?>
  </ul>
  年別収入額：
  <ul>
    <?php foreach ($incomes as $year => $income):?>
      <li><?= $year . ':' . number_format($income) . '円' ;?></li>
    <?php endforeach ; ?>
  </ul>
  年別貯蓄額：
  <ul>
    <?php foreach ($savings as $year => $saving):?>
      <li><?= $year . ':' . number_format($saving) . '円' ;?></li>
    <?php endforeach ; ?>
  </ul>
-->
  <p><a href="download_saving_simulation.php">[ダウンロード]年別の貯蓄額（csvファイル形式）</a></p>
  <p><a href="mypage.php">戻る</a></p>
</body>

<?php

include_once('_parts/_footer.php') ;
