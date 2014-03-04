<?php
$days     =array();
$daysMoney=array();

for($d=1; $d <= date('t'); $d++)
{
    $days[]=$d;

    if(isset($data[$d]))
        $daysMoney[]=round($data[$d],2);
    else
       $daysMoney[]=0;    
}
?>
<div id="block_1">
    <div id="block_1_3">
        <div class="period">
            <ul>
                <li><div style="color: #aaaaaa;">Статистика за - <?php echo Html::_date('F'); ?></li>
            </ul>
            <ul style="margin-top: 20px; font-size: 20px; color: #7E7E7E">
                <li><?php echo CMoney::_c($amount,true); ?></li>
            </ul>
        </div>
    </div>
    <div id="block_1_1">
        <canvas id="canvas" height="120" width="120"></canvas>
        <script> //100%
            var doughnutData = [
<?php foreach($lists as $list)
{ ?>
                    {value: <?php echo round($list['precent']); ?>, color: "#<?php echo $list['color']; ?>"},
<?php } ?>
            ];
            var myDoughnut = new Chart(document.getElementById("canvas").getContext("2d")).Doughnut(doughnutData);
        </script>
    </div>
    <div id="block_1_2">
        <ul>
            <?php foreach($lists as $list)
            { ?>
                <li><span style="background:#<?php echo $list['color']; ?>"></span><?php echo Yii::t('financeModule.index',$list['lang_key']); ?> - <?php echo round($list['precent']); ?>%</li>
<?php } ?>            
        </ul>
    </div>
</div>
<div id="block_2">
    <canvas id="canvas_3" height="auto" width="540"></canvas>
    <script>
        var lineChartData = {
            labels: ["<?php echo implode("\",\"",$days); ?>"],
            datasets: [
                {
                    fillColor: "rgba(220,220,220,0.5)",
                    strokeColor: "rgba(220,220,220,1)",
                    pointColor: "rgba(220,220,220,1)",
                    pointStrokeColor: "#fff",
                    data: [<?php echo implode(',',$daysMoney); ?>]
                }
            ]

        }
        var myLine = new Chart(document.getElementById("canvas_3").getContext("2d")).Line(lineChartData);
    </script>
</div>