<?php

	require_once DIRECTORY_CLASS.'class.alert.php';
	$histo = new Alert();
	global $Session; 
?>   
<div class="row" id="section-header">
	<div class="col-xs-12">

		<h1 style="text-align: center; border:none;"> Notificaciones<i class="fa fa-exclamation-circle"> </i> </h1>

	</div>   
</div> 

<div class="row">
	<div class="col-xs-12">
	<div class="col-md-6">
		<h2> <?php echo $Index->title ?></h2>
	</div> 
	<div class="col-md-6">
		<button  class="btn pull-right" onclick="export_table_xls('tbl_threshold');" style="background: #990d17; color: #FFF;"><i class="fa fa-cloud-download"></i>Exportar</button>
	</div>
	</div>
</div>
<div id='section-content' class='row' style="margin-top: 20px;"> 
	<div class="col-xs-12">
		<div class="row">  
			<div class='col-xs-12'>
				<ul class="nav nav-tabs" role="tablist">
					<li><a href="#"><?php echo $Index->title ?></a></li> 
				</ul>  
				<table id='tbl_threshold' class="table table-striped table-bordered clearfix text-center" >
			
					<?php echo $histo->get_alerts_table(); ?> 
				</table> 
			</div> 
			<div class="row"> &nbsp; </div> 
		</div>  
	</div>  
</div>   
<div class="row"> &nbsp; </div>
<div class="row"> &nbsp; </div>