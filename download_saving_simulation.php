<?php

require_once('../app/config.php') ;
$pdo = Database::getInstance() ;

// csv ファイルのダウンロード
download_savings($pdo) ;
