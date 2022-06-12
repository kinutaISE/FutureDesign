<?php

require_once('../app/config.php') ;

include('_parts/_header.php') ;

$user_name = filter_input(INPUT_POST, 'user_name') ;
$user_age = filter_input(INPUT_POST, 'user_age') ;
$prefecture_id = filter_input(INPUT_POST, 'prefecture_id') ;
$dependents_num = filter_input(INPUT_POST, 'dependents_num') ;
$anual_income_type = filter_input(INPUT_POST, 'anual_income_type') ;
$income = filter_input(INPUT_POST, 'income') ;

?>

<body>
  <p>手取り：<?= number_format( IncomeCalculator::calc_residual($income, $anual_income_type) ) ;?>円 </p>

  <p><a href="form.php">戻る</a></p>
</body>

<?php

include('_parts/_footer.php') ;
