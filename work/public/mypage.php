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
/*
// 都道府県のテーブルを獲得
$prefectures = get_prefectures_info($pdo) ;
*/
?>


<body>

  <h1>将来設計</h1>

  <p><?= $user_id ;?> さんのマイページ</p>

  <form method="post" action="?action=update_user_info">
    <!-- 基本情報 -->
    <h2>基本情報</h2>
    生年月日：
    <!-- 年 -->
    <select name="birth_year">
      <?php for ($year = 1965 ; $year <= date('Y') ; $year++):?>
        <option value="<?= $year ;?>" <?= ($user->get_birth_year() == $year) ? 'selected' : '' ;?>>
          <?= $year ;?>
        </option>
      <?php endfor ;?>
    </select>
    年
    <!-- 月 -->
    <select name="birth_month">
      <?php for ($month = 1 ; $month <= 12 ; $month++):?>
        <option value="<?= $month ;?>" <?= ($user->get_birth_month() == $month) ? 'selected' : '' ; ?>>
          <?= $month ;?>
        </option>
      <?php endfor ;?>
    </select>
    月
    <!-- 日 -->
    <select name="birth_date">
      <?php for ($date = 1 ; $date <= 31 ; $date++):?>
        <option value="<?= $date ;?>" <?= ($user->get_birth_date() == $date) ? 'selected' : '' ;?>>
          <?= $date ;?>
        </option>
      <?php endfor ;?>
    </select>
    日

    <br>
    事業種：
    <select name="business_type_id">
      <?php foreach ($business_types_info as $business_type_info):?>
        <option value="<?= $business_type_info['事業種ID'] ;?>" <?= ($user->get_business_type_id() === $business_type_info['事業種ID']) ? 'selected' : '' ; ?> >
          <?= $business_type_info['事業種名'] ;?>
        </option>
      <?php endforeach ;?>
    </select>
    <br>
    勤務地（都道府県）：
    <select name="prefecture_id">
      <?php foreach ($prefectures_info as $prefecture_info):?>
        <option value="<?= $prefecture_info['都道府県ID'] ;?>" <?= ($user->get_prefecture_id() === $prefecture_info['都道府県ID']) ? 'selected' : '' ; ?> >
          <?= $prefecture_info['都道府県名'] ;?>
        </option>
      <?php endforeach ;?>
    </select>
    <br>
    扶養人数：
    <select name="dependents_num">
      <?php for ($i = 0 ; $i <= 10 ; $i++) :?>
        <option value="<?= $i ;?>" <?= ($user->get_dependents_num() === $i) ? 'selected' : '' ?>>
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
      <!-- 頻度（日、週、月、年） -->
      <div class="frequency_form">
        <input type="number" name="frequency_number"> <!-- 頻度の数値 -->
        <select name="frequency_unit"> <!-- 頻度の単位 -->
          <option value="days">日</option>
          <option value="weeks">週間</option>
          <option value="months">ヶ月</option>
          <option value="years">年</option>
        </select>
        に一度発生
      </div>
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
      <!-- 発生する期間 -->
      <div>
        <!-- 常に必要となる支出かどうか -->
        <input type="checkbox" name="is_constant" value="constant">常にかかる支出
        <br>
        <!-- 期間の選択 -->
        <select name="term_start_year">
          <?php for ($year = date('Y') ; $year <= date('Y') + 50 ; $year++):?>
            <option value="<?= $year ;?>"> <?= $year ;?> </option>
          <?php endfor ;?>
        </select>
        年
        <select name="term_start_month">
          <?php for ($month = 1 ; $month <= 12 ; $month++):?>
            <option value="<?= $month ;?>"> <?= $month ;?> </option>
          <?php endfor ;?>
        </select>
        月〜
        <select name="term_finish_year">
          <?php for ($year = date('Y') ; $year <= date('Y') + 50 ; $year++):?>
            <option value="<?= $year ;?>"> <?= $year ;?> </option>
          <?php endfor ;?>
        </select>
        年
        <select name="term_finish_month">
          <?php for ($month = 1 ; $month <= 12 ; $month++):?>
            <option value="<?= $month ;?>"> <?= $month ;?> </option>
          <?php endfor ;?>
        </select>
        月
      </div>
      <!-- 頻度（日、週、月、年） -->
      <div class="frequency_form">
        <input type="number" name="frequency_number"> <!-- 頻度の数値 -->
        <select name="frequency_unit"> <!-- 頻度の単位 -->
          <option value="days">日</option>
          <option value="weeks">週間</option>
          <option value="months">ヶ月</option>
          <option value="years">年</option>
        </select>
        に一度発生
      </div>
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
