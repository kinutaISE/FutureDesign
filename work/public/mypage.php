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
      break ;
    case 'delete_cost_item':
      delete_cost_item($pdo) ;
      break ;
    case 'update_user_info':
      update_user_info($pdo) ;
      break ;
    case 'add_earning_item':
      add_earning_item($pdo) ;
      break ;
    case 'delete_earning_item':
      delete_earning_item($pdo) ;
      break ;
    case 'applicate_partner':
      send_partner_application($pdo) ;
      break ;
    case 'allow_application':
      allow_application($pdo) ;
      break ;
    case 'delete_application':
      delete_application($pdo) ;
      break ;
    default:
      exit('Invalid post request!!') ;
  }
  header('Location: ' . SITE_URL . '/../mypage.php') ;
  exit ;
}

// ユーザーIDの獲得
$user_id = $_SESSION['user_id'] ;
// ユーザー情報の獲得
$user = get_user_info($pdo) ;
// ログインしているユーザーの給与項目の獲得
$earning_items = get_earning_items($pdo) ;
// ログインしているユーザーの支出項目の獲得
$cost_items = get_cost_items($pdo) ;
// パートナー申請の獲得
$partner_applications = get_all_partner_applications($pdo) ;
?>


<body>

  <h1>将来設計</h1>

  <p><?= $user_id ;?> さんのマイページ</p>

  <!-- パートナー登録申請送信の結果 -->
  <?php
    if (
      in_array( 'partner_application_result', array_keys($_SESSION), true )
      && $_SESSION['partner_application_result'] !== NULL
    ):
  ?>
    <div id="partner_application_result">
      <p>
        <span
          style= "
            background-color:
            <?= ($_SESSION['partner_application_result']) ?
              "rgba(0, 255, 0, 0.1)" : "rgba(255, 0, 0, 0.1)" ;
            ?> ;
            border: solid ;
            border-color:
            <?= ($_SESSION['partner_application_result']) ?
              "rgb(0, 255, 0)" :"rgb(255, 0, 0)" ;
            ?> ;
            "
        >
          <?= $_SESSION['partner_application_text'] ;?>
        </span>
      </p>
      <?php
        $_SESSION['partner_application_result'] = NULL ;
        $_SESSION['partner_application_text'] = NULL ;
      ?>
    </div>
  <?php endif ;?>
  <form method="post" action="?action=update_user_info">
    <!-- 基本情報 -->
    <h2>基本情報</h2>
    生年月日：
    <?php $now = new DateTime() ;?>
    <input
      type = "date"
      name = "date_of_birth"
      min = "<?= $now->modify('- 100 years')->format('Y-m-d') ;?>"
      max = "<?= $now->modify('+ 95 years')->format('Y-m-d') ;?>"
      value = "<?= empty($user->get_date_of_birth()) ? '' : $user->get_date_of_birth() ?>"
    />
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
      </div>
      <div>
        <!-- 期間の開始 -->
        <?php $now = new DateTime() ;?>
        <input
          type="date"
          name="term_start"
          min="<?= $now->format('Y-m-d') ;?>"
          max="<?= $now->modify('+ 100 years')->format('Y-m-d') ;?>"
        />
        〜
        <!-- 期間の終了 -->
        <?php $now = new DateTime() ;?>
        <input
          type="date"
          name="term_finish"
          min="<?= $now->format('Y-m-d') ;?>"
          max="<?= $now->modify('+ 100 years')->format('Y-m-d') ;?>"
        />
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
  <!-- 貯蓄シミュレーションへのリンク -->
  <p><a href="saving_simulation.php">貯蓄のシミュレーションを行う</a></p>

  <h2>パートナー</h2>
  <!-- パートナー登録申請フォーム -->
  <?php if ( empty( $user->get_partner_id() ) ):?>
    <div>
      <h3>パートナー申請フォーム</h3>
      <form method="post" action="?action=applicate_partner">
        パートナーID：
        <input type="text" name="to_id"></input>
        <button>送信</button>
      </form>
    </div>
  <?php else:?>
    <p>登録済パートナー：<?= $user->get_partner_id() ;?></p>
  <?php endif ;?>
  <!-- パートナー申請一覧 -->
  <div>
    <h3>パートナー申請一覧</h3>
    <ul>
      <?php foreach ($partner_applications as $partner_application):?>
          <li><?= $partner_application['from_id'] ;?></li>
          <form method="post" action="?action=allow_application">
            <input type="hidden" name="from_id" value="<?= $partner_application['from_id'] ;?>">
            <button>許可</button>
          </form>
          <form method="post" action="?action=delete_application">
            <input type="hidden" name="from_id" value="<?= $partner_application['from_id'] ;?>">
            <button>削除</button>
          </form>
      <?php endforeach ;?>
    </ul>
  </div>
</body>

<?php

include_once('_parts/_footer.php') ;
