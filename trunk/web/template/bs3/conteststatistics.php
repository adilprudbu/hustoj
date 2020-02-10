<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<link rel="icon" href="../../favicon.ico">

	<title>
		<?php echo $OJ_NAME?>
	</title>  
	
	<?php include("template/$OJ_TEMPLATE/css.php");?>
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	<script src="http://cdn.bootcss.com/html5shiv/3.7.0/html5shiv.js"></script>
	<script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->

	<?php
	function formatTimeLength($length) {
		$hour = 0;
		$minute = 0;
		$second = 0;
		$result = '';

		global $MSG_SECONDS, $MSG_MINUTES, $MSG_HOURS, $MSG_DAYS;

		if ($length>=60) {
			$second = $length%60;
			
			if ($second>0 && $second<10) {
				$result = '0'.$second.' '.$MSG_SECONDS;}
			else if ($second>0) {
				$result = $second.' '.$MSG_SECONDS;
			}

			$length = floor($length/60);
			if ($length >= 60) {
				$minute = $length%60;
				
				if ($minute==0) {
					if ($result != '') {
						$result = '00'.' '.$MSG_MINUTES.' '.$result;
					}
				}
				else if ($minute>0 && $minute<10) {
					if ($result != '') {
						$result = '0'.$minute.' '.$MSG_MINUTES.' '.$result;}
					}
					else {
						$result = $minute.' '.$MSG_MINUTES.' '.$result;
					}
					
					$length = floor($length/60);

					if ($length >= 24) {
						$hour = $length%24;

					if ($hour==0) {
						if ($result != '') {
							$result = '00'.' '.$MSG_HOURS.' '.$result;
						}
					}
					else if ($hour>0 && $hour<10) {
						if($result != '') {
							$result = '0'.$hour.' '.$MSG_HOURS.' '.$result;
						}
					}
					else {
						$result = $hour.' '.$MSG_HOURS.' '.$result;
					}

					$length = floor($length / 24);
					$result = $length .$MSG_DAYS.' '.$result;
				}
				else {
					$result = $length.' '.$MSG_HOURS.' '.$result;
				}
			}
			else {
				$result = $length.' '.$MSG_MINUTES.' '.$result;
			}
		}
		else {
			$result = $length.' '.$MSG_SECONDS;
		}
		return $result;
	}
	?>

</head>

<body>
	<div class="container">
		<?php include("template/$OJ_TEMPLATE/nav.php");?>	    
		<!-- Main component for a primary marketing message or call to action -->
		<div class="jumbotron">

			<?php
			if (isset($_GET['cid'])) {
				$cid = intval($_GET['cid']);
				$view_cid = $cid;
				//print $cid;

				//check contest valid
				$sql = "SELECT * FROM `contest` WHERE `contest_id`=?";
				$result = pdo_query($sql,$cid);

				$rows_cnt = count($result);
				$contest_ok = true;
				$password = "";

				if (isset($_POST['password']))
					$password = $_POST['password'];

				if (get_magic_quotes_gpc()) {
					$password = stripslashes($password);
				}

				if ($rows_cnt==0) {
					$view_title = "比赛已经关闭!";
				}
				else{
					$row = $result[0];
					$view_private = $row['private'];

					if ($password!="" && $password==$row['password'])
						$_SESSION[$OJ_NAME.'_'.'c'.$cid] = true;

					if ($row['private'] && !isset($_SESSION[$OJ_NAME.'_'.'c'.$cid]))
						$contest_ok = false;

					if($row['defunct']=='Y')
						$contest_ok = false;

					if (isset($_SESSION[$OJ_NAME.'_'.'administrator']))
						$contest_ok = true;

					$now = time();
					$start_time = strtotime($row['start_time']);
					$end_time = strtotime($row['end_time']);
					$view_description = $row['description'];
					$view_title = $row['title'];
					$view_start_time = $row['start_time'];
					$view_end_time = $row['end_time'];
				}
			}
			?>

			<?php if (isset($_GET['cid'])) {?>
			<center>
			<div>
				<h3><?php echo $MSG_CONTEST_ID?> : <?php echo $view_cid?> - <?php echo $view_title ?></h3>
				<p>
					<?php echo $view_description?>
				</p>
				<br>
				<?php echo $MSG_SERVER_TIME?> : <span id=nowdate > <?php echo date("Y-m-d H:i:s")?></span>
				<br>
				
				<?php if (isset($OJ_RANK_LOCK_PERCENT)&&$OJ_RANK_LOCK_PERCENT!=0) { ?>
				Lock Board Time: <?php echo date("Y-m-d H:i:s", $view_lock_time) ?><br/>
				<?php } ?>
				
				<?php if ($now>$end_time) {
					echo "<span class=text-muted>$MSG_Ended</span>";
				}
				else if ($now<$start_time) {
					echo "<span class=text-success>$MSG_Start&nbsp;</span>";
					echo "<span class=text-success>$MSG_TotalTime</span>"." ".formatTimeLength($end_time-$start_time);
				}
				else {
					echo "<span class=text-danger>$MSG_Running</span>&nbsp;";
					echo "<span class=text-danger>$MSG_LeftTime</span>"." ".formatTimeLength($end_time-$now);
				}
				?>

				<br><br>

				<?php echo $MSG_CONTEST_STATUS?> : 
				
				<?php
				if ($now>$end_time)
					echo "<span class=text-muted>".$MSG_End."</span>";
				else if ($now<$start_time)
					echo "<span class=text-success>".$MSG_Start."</span>";
				else
					echo "<span class=text-danger>".$MSG_Running."</span>";
				?>
				&nbsp;&nbsp;

				<?php echo $MSG_CONTEST_OPEN?> : 

				<?php if ($view_private=='0')
					echo "<span class=text-primary>".$MSG_Public."</span>";
				else
					echo "<span class=text-danger>".$MSG_Private."</span>";
				?>

				<br>

				<?php echo $MSG_START_TIME?> : <?php echo $view_start_time?>
				<br>
				<?php echo $MSG_END_TIME?> : <?php echo $view_end_time?>
				<br><br>

				<div class="btn-group">
					<a href="contest.php?cid=<?php echo $cid?>" class="btn btn-primary btn-sm"><?php echo $MSG_PROBLEMS?></a>
					<a href="status.php?cid=<?php echo $view_cid?>" class="btn btn-primary btn-sm"><?php echo $MSG_MY_SUBMISSIONS?></a>
					<a href="contestrank.php?cid=<?php echo $view_cid?>" class="btn btn-primary btn-sm"><?php echo $MSG_STANDING?></a>
					<a href="contestrank-oi.php?cid=<?php echo $view_cid?>" class="btn btn-primary btn-sm"><?php echo "OI".$MSG_STANDING?></a>
					<a href="conteststatistics.php?cid=<?php echo $view_cid?>" class="btn btn-primary btn-sm"><?php echo $MSG_STATISTICS?></a>
				</div>
			</div>
			</center>
			<?php }?>
			<br><br>	
			<center>
				<h4><?php if (isset($locked_msg)) echo $locked_msg;?></h4>
				<table id=cs width=90%>
					<thead>
						<tr class=toprow>
							<th><th>AC<th>PE<th>WA<th>TLE<th>MLE<th>OLE<th>RE<th>CE<th><th>TR<th>Total
							<?php 
							$i = 0;
							foreach ($language_name as $lang) {
								if (isset($R[$pid_cnt][$i+11]) )	
									echo "<th class='center'>$language_name[$i]</th>";
								else
									echo "<th>";
								$i++;
							}
							?>
						</tr>
					</thead>
					<tbody>
						<?php
						for ($i=0;$i<$pid_cnt;$i++){
							if(!isset($PID[$i])) $PID[$i]="";
							if ($i&1)
								echo "<tr align=center class=oddrow><td>";
							else
								echo "<tr align=center class=evenrow><td>";
							echo "<a href='problem.php?cid=$cid&pid=$i'>$PID[$i]</a>";
							for ($j=0;$j<count($language_name)+11;$j++) {
								if(!isset($R[$i][$j])) $R[$i][$j]="";
								echo "<td>".$R[$i][$j];
							}
							echo "</tr>";
						}
						echo "<tr align=center class=evenrow><td>Total";
						for ($j=0;$j<count($language_name)+11;$j++) {
							if(!isset($R[$i][$j])) $R[$i][$j]="";
							echo "<td>".$R[$i][$j];
						}
						echo "</tr>";
						?>
					</tbody>
					<table>
						<div id=submission style="width:600px;height:300px" ></div>
					</center>

				</div>

			</div> <!-- /container -->


<!-- Bootstrap core JavaScript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<?php include("template/$OJ_TEMPLATE/js.php");?>	    
	<script type="text/javascript" src="include/jquery.tablesorter.js"></script>
	<script type="text/javascript">
		$(document).ready(function()
		{
			$("#cs").tablesorter();
		}
		);
	</script>

	<script language="javascript" type="text/javascript" src="include/jquery.flot.js"></script>
	<script type="text/javascript">
		$(function () {
			var d1 = [];
			var d2 = [];
			<?php
			foreach($chart_data_all as $k=>$d){
				?>
				d1.push([<?php echo $k?>, <?php echo $d?>]);
			<?php }?>
			<?php
			foreach($chart_data_ac as $k=>$d){
				?>
				d2.push([<?php echo $k?>, <?php echo $d?>]);
			<?php }?>
//var d2 = [[0, 3], [4, 8], [8, 5], [9, 13]];
// a null signifies separate line segments
var d3 = [[0, 12], [7, 12], null, [7, 2.5], [12, 2.5]];
$.plot($("#submission"), [
	{label:"<?php echo $MSG_SUBMIT?>",data:d1,lines: { show: true }},
	{label:"<?php echo $MSG_SOVLED?>",data:d2,bars:{show:true}} ],{
		xaxis: {
			mode: "time"
//, max:(new Date()).getTime()
//,min:(new Date()).getTime()-100*24*3600*1000
},
grid: {
	backgroundColor: { colors: ["#fff", "#333"] }
}
});
});
//alert((new Date()).getTime());
</script>

<script>
  var diff = new Date("<?php echo date("Y/m/d H:i:s")?>").getTime()-new Date().getTime();
  //alert(diff);
  function clock() {
    var x,h,m,s,n,xingqi,y,mon,d;
    var x = new Date(new Date().getTime()+diff);
    y = x.getYear()+1900;

    if (y>3000)
      y -= 1900;

    mon = x.getMonth()+1;
    d = x.getDate();
    xingqi = x.getDay();
    h = x.getHours();
    m = x.getMinutes();
    s = x.getSeconds();
    n = y+"-"+(mon>=10?mon:"0"+mon)+"-"+(d>=10?d:"0"+d)+" "+(h>=10?h:"0"+h)+":"+(m>=10?m:"0"+m)+":"+(s>=10?s:"0"+s);

    //alert(n);
    document.getElementById('nowdate').innerHTML = n;
    setTimeout("clock()",1000);
  }
  clock();
</script>

</body>
</html>
