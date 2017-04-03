<?PHP
$pageURL = ((isset($_SERVER['HTTPS'])) && ($_SERVER['HTTPS']) == 'on') ? 'https://' : 'http://';
$pageURL .= $_SERVER['SERVER_PORT'] != '80' ? $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"] : $_SERVER['SERVER_NAME'];
$rUrl = "'$pageURL/share-counter/api.php?key=".$_GET['rkey']."'";
?>

<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<script type="text/javascript">

$.ajax({
	type:'Get',
	dataType:'jsonp',
	url: <?PHP echo $rUrl; ?>,
	success:function(json) {
	
		var dataXY = [];
		$.each( json.datas, function( key, v ) {
		  dataXY.push([Date.parse(v.date),parseInt(v.value)]);		  
		});				

		var options = {
			chart: {
				 renderTo: 'container',
				 type: 'spline',      
			},
			title: { text: 'Track your weight' },
			subtitle: { text: 'ShareCounter' },
			xAxis: {
				type: 'datetime',
				dateTimeLabelFormats: { // don't display the dummy year
					month: '%e. %b',
					year: '%b'
				},
				title: {
					text: 'Date'
				}
			},
			yAxis: {
				title: { text: 'Weight (Kg)' }, min: 0,
			},
			tooltip: {
				headerFormat: '<b>{series.name}</b><br>',
				pointFormat: '{point.x:%e. %b}: {point.y:.2f} Kg'
			},

			plotOptions: {
				spline: {
					marker: { enabled: true }
				}
			},
			series:[{
				data: dataXY,
			}]
	   };

	   /** Create a chart instance and pass options. */
	   var chart = new Highcharts.Chart(options);
	},
	error:function(err) {
		console.error(err);
	}
});
	
		</script>
	</head>
	<body>	
		<script src="../third-parties/highcharts-4.1.7/js/highcharts.js"></script>
		<script src="../third-parties/highcharts-4.1.7/js/modules/exporting.js"></script>
		<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
	</body>
</html>
