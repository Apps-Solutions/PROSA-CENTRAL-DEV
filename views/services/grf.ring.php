		<div class="col-xs-12 col-md-6 text-center" id='grf-ring-<?php  echo $k ?>'>
			<div class="row"> &nbsp; </div>
			<div class="row"> 
				<div class="col-xs-1">&nbsp;  </div>
				<div class="div-chart-ring col-xs-11  indicator-graf">
					<div class="chart-ring" id="div_chart_ring_<?php  echo $k ?>"> </div>
					<div class="chart-side hidden-xs" style="vertical-align: middle;">
						<div class="rotate"> Aceptadas </div>
					</div>
					<div class="overlay text-center" >
						<div style="font-size: 48px; font-weight: bold; text-shadow: 2px 0 0 #fff, -2px 0 0 #fff, 0 2px 0 #fff, 0 -2px 0 #fff, 1px 1px #fff, -1px -1px 0 #fff, 1px -1px 0 #fff, -1px 1px 0 #fff; margin-left: 5%;">
						<?php echo number_format(( $data['total_transactions'] > 0 ? number_format ($data['total_accepted'] * 100 / $data['total_transactions'] , 2 ) : 0) , 2 ) ?>%
						</div>
						<div style="font-size: 18px;">Aceptadas</div> 
					</div> 
					<?php if ($data['source'] != "" ) {?>
					<div class="overlay_title">
						<div > <?php echo $data['source'] ?> </div>
					</div>
					<?php 
						}
						if ($data['name'] != "" ) {
					?>
					<div class="overlay-source">
						<div > <?php echo $data['name'] ?> </div>
					</div>
					<?php } ?> 
					<div class="chart-bottom col-xs-12 hidden-sm hidden-md hidden-lg">
						  Aceptadas  
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-9">
					<table class="table table-striped table-bordered datatable clearfix">
						<tr> <td>  </td><td> % </td><td> Total </td> </tr>
						<tr> 
							<td> Aceptadas </td>
							<td><?php echo number_format(( $data['total_transactions'] > 0 ?  ($data['total_accepted'] * 100 / $data['total_transactions'])   : 0) , 2 ) ?> </td>
							<td> <?php echo number_format($data['total_accepted']); ?> </td> 
						</tr>
						<tr> <td> Rechazadas </td>
							<td><?php echo number_format(( $data['total_transactions'] > 0 ?  ($data['total_rejected'] * 100 / $data['total_transactions'])   : 0) , 2 ) ?> </td>
							<td> <?php echo number_format($data['total_rejected']); ?> </td> 
						</tr>
					</table>
					 
				</div>
				<script type="text/javascript">
					google.setOnLoadCallback(draw_chart_<?php  echo $k ?>); 
					
					function draw_chart_<?php  echo $k ?>(){
						
						var data= google.visualization.arrayToDataTable([
							['Height', 'Width'],
							['Aceptadas', <?php echo number_format(( $data['total_transactions'] > 0 ?  ($data['total_accepted'] * 100 / $data['total_transactions'])   : 0) , 1 ) ?>],
							['Rechazadas', <?php echo number_format(( $data['total_transactions'] > 0 ?  ($data['total_rejected'] * 100 / $data['total_transactions'])  : 0) , 1 ) ?>] 
						]);
						 var options = { 
							legend: {position:'none'},
							chartArea: {left: 0,top:10,width:'80%',height:'100%'},
							backgroundColor: '',
							pieHole: 0.75,
							pieSliceText: 'none',
							colors: [ '#444', '#990d17']
						};
						var where = 'div_chart_ring_<?php  echo $k ?>'; 
						 
						var chart = new google.visualization.PieChart(document.getElementById('div_chart_ring_<?php  echo $k ?>'));
						chart.draw(data, options);
					} 
				</script>
			</div>
			<div class="row"> &nbsp; </div>
		</div>
