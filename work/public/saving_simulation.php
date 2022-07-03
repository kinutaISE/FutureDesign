<?php

require_once('../app/config.php') ;

include_once('_parts/_header.php') ;

$pdo = Database::getInstance() ;

$costs = SavingSimulator::get_costs($pdo) ;

?>

<body>
  <ul>
    <?php foreach ($costs as $year => $cost):?>
      <li><?= $year . ':' . number_format($cost) . '円' ;?></li>
    <?php endforeach ; ?>
  </ul>
  <p><a href="mypage.php">戻る</a></p>
</body>

<?php

include_once('_parts/_footer.php') ;
