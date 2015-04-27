
		<div class="col-xs-12 col-md-6" id='grf-top-0' style=''>
			<div class="row">
				<div class="col-xs-12   indicator-graf">
					 <div style=" height:230px; width: 100%;  position: relative;" id="chart_bar_<?php echo $k ?>"> </div> 
					 <div class="chart-table " style="width:100%;"> 
					 	<table class="table table-striped table-bordered datatable clearfix">
					 		<?php 
					 		$sum = 0;
					 		foreach ($data['top_rejected'] as $j => $top ) { 
							?>
							<tr> 
								<td> <?php echo $top['code'] . " - " . $top['motive'] ?> </td> 
								<td align="center"> 
									<?php 
									echo ( $data['total_rejected'] > 0 ? number_format ($top['total'] * 100 / $data['total_rejected'] , 2 ) :  0 )
									?> %
								</td> 
								<td align="right"> <?php echo number_format($top['total'], 0, '.', ',') ?> </td> 
							<?php
								 if ( $j == 0 ){
							?>
								 	<td rowspan="6" class="chart-table-side ">
						 				<div class="rotate">Rechazadas</div> 
									</td>
							<?php
								 }
								$sum += $top['total'];
							?></tr><?php  
							 }  
					 		?>   
					 	</table>
					 	<script type="text/javascript">  
						google.setOnLoadCallback(draw_chart_bar_<?php  echo $k ?>);
						
						function draw_chart_bar_<?php  echo $k ?>(){
							 
							 var data = google.visualization.arrayToDataTable([
					          ['Motivo', 'Porcentaje', { role: 'style' }, { role: 'annotation' }] 
							<?php  
							if ( count($data['top_rejected']) > 0 ){
						 		foreach ($data['top_rejected'] as $j => $top ) {
						 			echo /*( $j > 0 ? ", " : "") . */  ",['" . $top['code'] . "', "
						 				. " " . ( $data['total_rejected'] > 0 ? number_format ($top['total'] * 100 / $data['total_rejected'] , 2 , '.', ',') :  0 )  . ", " 
										. " '#990D17' ,"
										. " '" . ( $data['total_rejected'] > 0 ? number_format ($top['total'] * 100 / $data['total_rejected'] , 2, '.', ',' ) :  0 ) . "%' ]"; 
								 } 
							}else {
								echo ", [ 'NULL', 0,'#990D17', '' ]";
							}
							?> 			 		
					        ]); 
							 var options = {
								title: 'Top Motivos de Rechazo',
								backgroundColor: '#fcfcfc',
								legend: {position:'none'},
								chartArea: {left: 50,top:10,width:'90%',height:'80%'}
							};
							var where = 'chart_bar_<?php  echo $k ?>';  
							var chart = new google.visualization.ColumnChart(document.getElementById(where)); 
							chart.draw(data, options);

							$('.starthidden').hide();
						} 
						
					 	</script>
					 </div>
				</div>
			</div>
			<div class="row"> &nbsp; </div>
		</div> 
