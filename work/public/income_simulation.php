<?php

require_once('../app/config.php') ;
include('_parts/_header.php') ;

$pdo = Database::getInstance() ;

?>

<body>
  <p>手取り：<?= number_format( IncomeSimulator::calc_residual($pdo) ) ;?>円 </p>

  <p><a href="mypage.php">戻る</a></p>
</body>

<?php

include('_parts/_footer.php') ;
