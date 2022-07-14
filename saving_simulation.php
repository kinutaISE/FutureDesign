<?php

require_once('app/config.php') ;

include_once('_parts/_header.php') ;

$pdo = Database::getInstance() ;

$incomes = IncomeSimulator::get_incomes($pdo) ;
$costs = SavingSimulator::get_costs($pdo) ;
$savings = SavingSimulator::get_savings($pdo) ;

// フロント側に配列を渡す
$json_incomes = json_encode( array_step($incomes, 3) ) ;
$json_costs = json_encode( array_step($costs, 3) ) ;
$json_savings = json_encode( array_step($savings, 3) ) ;

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
    <canvas id="saving-bar"></canvas>
  </div>
  <script>
    // PHP から配列を受け取る
    const incomes_obj = <?= $json_incomes ;?> ;
    const costs_obj = <?= $json_costs ;?> ;
    const savings_obj = <?= $json_savings ;?> ;
    // object → 配列とする
    const years = Object.keys(savings_obj) ;
    const incomes = Object.keys(incomes_obj).map(function (key) {
      return [ key, incomes_obj[key] ] ;
    }) ;
    const costs_rev = Object.keys(costs_obj).map(function (key) {
      return [ key, (-1) * costs_obj[key] ] ;
    }) ;
    const savings = Object.keys(savings_obj).map(function (key) {
      return [ key, savings_obj[key] ] ;
    }) ;

    // 描画する
    var ctx = document.getElementById('saving-bar');
    var saving_bar = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: years,
        datasets: [
          {
            type: 'line',
            label: '貯蓄',
            data: savings,
            backgroundColor: '#48f',
          },
          {
            label: '収入',
            data: incomes,
            backgroundColor: '#f88',
          },
          {
            label: '支出',
            data: costs_rev,
            backgroundColor: '#484',
          },
        ],
      },
    }) ;
  </script>
  <p><a href="download_saving_simulation.php">[ダウンロード]年別の貯蓄額（csvファイル形式）</a></p>
  <p><a href="mypage.php">戻る</a></p>
</body>

<?php

include_once('_parts/_footer.php') ;
