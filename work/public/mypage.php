<?php

require('../app/config.php') ;
include_once('_parts/_header.php') ;

// セッションタイムアウト時の処理
if ( !isset($_SESSION['user_id']) ) {
  header('Location: ' . SITE_URL . '/../session_timeout.php') ;
  exit ;
}

// PDOオブジェクトの獲得
$pdo = Database::getInstance() ;

// テーブルのの更新
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = filter_input(INPUT_GET, 'action') ;
  switch ($action) {
    case 'add_cost_item':
      add_cost_item($pdo) ;
      header('Location: ' . SITE_URL . '/../mypage.php') ;
      exit ;
      break ;
    case 'delete_cost_item':
      delete_cost_item($pdo) ;
      header('Location: ' . SITE_URL . '/../mypage.php') ;
      exit ;
      break ;
    case 'update_user_info':
      update_user_info($pdo) ;
      header('Location: ' . SITE_URL . '/../mypage.php') ;
      exit ;
      break ;
    default:
      exit('Invalid post request!!') ;
  }
}

// ユーザーIDの獲得
$user_id = $_SESSION['user_id'] ;
// ユーザー情報の獲得
$user = get_user_info($pdo) ;
// ログインしているユーザーの支出項目の獲得
$cost_items = get_cost_items($pdo) ;
// 都道府県のテーブルを獲得
$prefectures = get_prefectures_info($pdo) ;
?>


<body>

  <h1>将来設計</h1>

  <p><?= $user_id ;?> さんのマイページ</p>

  <form method="post" action="?action=update_user_info">
    <!-- 基本情報 -->
    <h2>基本情報</h2>
    年齢：<input type="number" name="age" value="<?= $user->age ;?>" placeholder="年齢を記入してください">
    <br>
    勤務地（都道府県）：
    <select name="prefecture_id">
      <?php foreach ($prefectures as $prefecture):?>
        <option value="<?= $prefecture->id ?>" <?= ($user->prefecture_id === $prefecture->id) ? 'selected' : '' ; ?> >
          <?= ($prefecture->name === '') ? $prefecture_names[$prefecture->id - 1] : $prefecture_name ;?>
        </option>
      <?php endforeach ;?>
    </select>
    <br>
    扶養人数：
    <select name="dependents_num">
      <?php for ($i = 0 ; $i <= 10 ; $i++) :?>
        <option value="<?= $i ;?>" <?= ($user->dependents_num === $i) ? 'selected' : '' ?>>
          <?= $i ;?>人
        </option>
      <?php endfor ;?>
    </select>

    <!-- 収入 -->
    <h2>収入に関する情報</h2>
    給与（額面）：<input type="number" name="income" value="<?= $user->income ;?>" placeholder="額面の給与を記入してください">円
    <br>
    <button>保存</button>
  </form>

  <!-- 支出 -->
  <h2>支出に関する情報</h2>
  <form method="post" action="?action=add_cost_item">
    <input type="text" name="cost_item_name" placeholder="支出の項目名を記入してください">
    <input type="text" name="cost_item_value" placeholder="支出額を記入してください">
    <button>Add</button>
  </form>
  <ul>
  <?php foreach ($cost_items as $cost_item):?>
    <li>
      <?= $cost_item->name . ' : ' . number_format($cost_item->value) . '円' ; ?>
      <form method="post" action="?action=delete_cost_item">
        <input type="hidden" name="cost_item_id" value="<?= $cost_item->id ; ?>">
        <button>削除</button>
      </form>
    </li>
  <?php endforeach ; ?>
  </ul>
  <!-- 収入シミュレーションへのリンク -->
  <p><a href="income_simulation.php">収入のシミュレーションを行う</a></p>
</body>

<?php

include_once('_parts/_footer.php') ;
