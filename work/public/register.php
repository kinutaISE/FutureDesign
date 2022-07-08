<?php

require_once('../app/config.php') ;

include_once('_parts/_header.php') ;

$pdo = Database::getInstance() ;

?>

<body>
  <p><?= $result['message'] ;?></p>
  <p><?= $result['link'] ;?></p>
</body>

<?php

include_once('_parts/_footer.php') ;
