<?php

require_once('../app/config.php') ;

include_once('_parts/_header.php') ;

$pdo = Database::getInstance() ;

$incomes = IncomeSimulator::get_incomes($pdo) ;
$costs = SavingSimulator::get_costs($pdo) ;
$savings = SavingSimulator::get_savings($pdo) ;

// フロント側に配列を渡す
$json_incomes = json_encode( array_step($incomes) ) ;
$json_costs = json_encode( array_step($costs) ) ;
$json_savings = json_encode( array_step($savings) ) ;

?>

<script
  src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.2.0/chart.min.js"
  integrity="sha512-VMsZqo0ar06BMtg0tPsdgRADvl0kDHpTbugCBBrL55KmucH6hP9zWdLIWY//OTfMnzz6xWQRxQqsUFefwHuHyg=="
  crossorigin="anonymous"></script>
<script
  src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@next/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
</head>

<body>
  <div class="chart">
    <canvas id="mychart-bar"></canvas>
  </div>
  <script>
    // PHP から配列を受け取る
    var incomes = <?= $json_incomes ;?> ;
    var costs = <?= $json_costs ;?> ;
    var savings = <?= $json_savings ;?> ;
    var years = Object.keys(savings) ;

    // 描画する
    var ctx = document.getElementById('mychart-bar');
    var myChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: years,
        datasets: [
          {
            label: '収入',
            data: incomes,
            backgroundColor: '#f88',
          },
          {
            label: '支出',
            data: costs,
            backgroundColor: '#484',
          },
          {
            label: '貯蓄',
            data: savings,
            backgroundColor: '#48f',
          },
        ],
      },
    }) ;
  </script>

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
