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
    case 'add_earning_item':
      add_earning_item($pdo) ;
      header('Location: ' . SITE_URL . '/../mypage.php') ;
      exit ;
      break ;
    case 'delete_earning_item':
      delete_earning_item($pdo) ;
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
// ログインしているユーザーの給与項目の獲得
$earning_items = get_earning_items($pdo) ;
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
    <button>保存</button>
  </form>

  <!-- 収入 -->
  <div>
    <h2>収入に関する情報</h2>
    <h3>給与に関する情報</h3>
    <!-- 給与の入力フォーム -->
    <form method="post" action="?action=add_earning_item">
      <!-- 給与項目名 -->
      <input type="text" name="earning_item_name" placeholder="給与項目名を記入してください">
      <!-- 給与額 -->
      <input type="number" name="earning_item_amount" placeholder="給与額を記入してください">
      <!-- 課税 or 非課税 -->
      <label>
        <input type="radio" name="earning_item_is_taxation" value="1">課税
      </label>
      <label>
        <input type="radio" name="earning_item_is_taxation" value="0">非課税
      </label>
      <button>追加</button>
    </form>
    <!-- 給与の一覧 -->
    <ul>
    <?php foreach ($earning_items as $earning_item) :?>
      <li>
        <?= $earning_item->get_info() ; ?>
        <form method="post" action="?action=delete_earning_item">
          <input type="hidden" name="earning_item_id" value="<?= $earning_item->get_id() ;?>">
          <button>削除</button>
        </form>
      </li>
    <?php endforeach ; ?>
    </ul>
  </div>

  <!-- 支出 -->
  <div>
    <h2>支出に関する情報</h2>
    <!-- 支出項目の入力フォーム -->
    <form method="post" action="?action=add_cost_item">
      <input type="text" name="cost_item_name" placeholder="支出の項目名を記入してください">
      <input type="text" name="cost_item_value" placeholder="支出額を記入してください">
      <button>追加</button>
    </form>
    <!-- 支出項目の一覧 -->
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
  </div>
  <!-- 収入シミュレーションへのリンク -->
  <p><a href="income_simulation.php">収入のシミュレーションを行う</a></p>
</body>

<?php

include_once('_parts/_footer.php') ;
