<?php

// ユーザー一覧表示
require_once(__DIR__ . '/../config/config.php');

// // Autoログアウトの実装
$autologout = new MyApp\Controller\Auto_logout();
$autologout->run();

$app = new MyApp\Controller\Enq_chart();
$app->run();
// $app->me()
// var_dump($app->getValues()->enq_data);
// exit;
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>チャート</title>
  <!-- bootstrap CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <!-- CSS -->
  <link rel="stylesheet" href="css/styles.css">
</head>
<body class="bg_user">

  <!-- コンテナ -->
  <div id="container">
    <!-- トップページ -->
    <p class="fs12"><a href="index.php">会員トップページ</a></p>

    <!-- ログアウト -->
    <form action="logout.php" method="post" id="logout">
      <input type="submit" value="Logout">
      <input type="hidden" name="token" value="<?= h($_SESSION['token']); ?>">
    </form>

    <!-- メイン -->
    <p class="userinfo">
      <?= "ようこそ " . h($app->me()->username) . " さん ("  . h($app->me()->email) . ") " . h($app->me()->role ? "プレミアム" : "一般ユーザー") ;?>
    </p>

    <canvas id="my_chart">
      Canvas not supported...
    </canvas>

    <!-- アンケートテーブル -->
    <table class="table table-hover width_enq_table">
      <tr class="table_th_color_setting">
        <td>ID</td>
        <td>性別</td>
        <td>年齢</td>
        <td>味</td>
      </tr>
      <?php foreach($app->getValues()->enq_data as $row) : ?>
      <tr>
        <td><?= h($row->id) ; ?></td>
        <td><?= h($row->gender) ; ?></td>
        <td><?= h($row->old) ; ?></td>
        <td><?= h($row->taste) ; ?></td>
      </tr>
      <?php endforeach; ?>
    </table>

    <!-- チャート作成のデータ -->
    <?php
      // var_dump($app->getValues()->enq_data);
      // exit;
      $m1 = 0;
      $m2 = 0;
      $m3 = 0;
      $m4 = 0;
      $m5 = 0;
      $f1 = 0;
      $f2 = 0;
      $f3 = 0;
      $f4 = 0;
      $f5 = 0;

      foreach($app->getValues()->enq_data as $row) {
        // $i = count($row->old==1 ?);

        $check = (int)$row->old;
        switch ($check) {
            case 1: // １０代なら
                $row->gender == 1 ? $m1++ : $f1++;
                break;
            case 2:
                $row->gender == 1 ? $m2++ : $f2++;
                break;
            case 3:
                $row->gender == 1 ? $m3++ : $f3++;
                break;
            case 4:
                $row->gender == 1 ? $m4++ : $f4++;
                break;
            case 5:
                $row->gender == 1 ? $m5++ : $f5++;
                break;
        }
      }
      // var_dump($m1);
      // var_dump($f1);
      // exit;
    ?>




    <!-- <h1>CSV出力</h1>
    <form action="enq_csv.php" method="post">
      Download
    <input type="submit" name="export_csv" value="ダウンロード" />
    </form> -->


  </div>
<script src="js/functions.js"></script>
<script src="js/chart273.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script> -->


<!-- <script src="./enq_chart_enq1.js"></script> -->
<!-- jsファイルにphpをかけないので、以下のようにする -->
<script>
(function() {
  'use strict';
  // var type = 'line';
  var type = 'bar';
  var data = {
    labels: [10, 20, 30, 40, 50],
    datasets: [{
      // 売り上げ
      label: '男',

      // data: [10, 20, 30, 40, 50],
      data: [<?= h($m1).','.h($m2).','.h($m3).','.h($m4).','.h($m5) ;?> ],
      borderColor: 'blue',
      borderWitdh: 3,
      fill: true,
      // // type->bar settings
      backgroundColor: 'skyblue',
      // borderWitdh: 0
      yAxisID: 'sales-axis',
    },{
      // 購読者
      label: '女',
      // data: [180, 250, 320, 180],
      data: [<?= h($f1).','.h($f2).','.h($f3).','.h($f4).','.h($f5) ;?> ],
      borderColor: 'red',
      borderWitdh: 3,
      // backgroundColor: 'rgba(0, 0, 200, 0.2)',
      backgroundColor: 'pink',
      lineTension: 0,
      pointStyle: 'circle',
      yAxisID: 'subscribers-axis',
    }]
  };
  var options = {
    scales: {
      yAxes: [{
        // 売り上げ
        ticks: {
          suggestedMin: 0,
          suggestedMax: 10,
          stepSize: 10,
          callback: function(value, index, values) {
            return value + '男';
          }
        },
        id: 'sales-axis',
        type: 'linear',
        position: 'right',
      }, {
        // 購読者
        ticks: {
          suggestedMin: 0,
          suggestedMax: 10,
          stepSize: 10,
          callback: function(value, index, values) {
            return value + '女';
          }
        },
        id: 'subscribers-axis',
        type: 'linear',
        position: 'left',
        gridLines: {
          display: false
        }
      }]
    },
    title: {
      display: true,
      text: '年代別の男女数の集計',
      fontSize: 22,
      position: 'top'
    },
    animation: {
      duration: 500
    },
    legend: {
      position: 'right',
      display: true,
    }
  };
  var ctx = document.getElementById('my_chart').getContext('2d');
  var myChart = new Chart(ctx, {
    type: type,
    data: data,
    options: options
  });

})();

</script>

</body>
</html>
