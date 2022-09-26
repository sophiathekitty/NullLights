<?php 
if(!isset($_GET['room_id'])) die();
require_once("../../../includes/main.php");
$charts = WeMoChart::HourlyWeMoRoomLog($_GET['room_id']);
if(count($charts) == 0) die();
function ChartsHour($hour,$charts){ if($hour < 10) $hour = "0".(string)$hour; ?>
<div class="hour" hour="<?=$hour?>"><?php
foreach($charts as $chart){?>
<div class="light" mac_address="<?=$chart['light']['mac_address'];?>" style="background-color: <?=$chart['hourly'][(int)$hour]['color'];?>;"></div>
<?php }
?></div>
<?php }
?>
<div class="lights_chart simple" collection="lights_chart" room_id="<?=$_GET['room_id'];?>">
    <div class="time_bar"></div>
    <?php 
    for($h = 0; $h < 24; $h++) ChartsHour($h,$charts);
    ?>
</div>