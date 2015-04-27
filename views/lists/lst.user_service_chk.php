<?php 
	
?>
<li class="list-group-item">
	<div class="row">
		<div class="col-xs-12" >
			<div class="row" style="cursor: pointer;"  onclick='show_user_services( <?php echo $k ?>, "<?php echo $user->user ?>");'>
				<span class="col-xs-12"><?php echo $user->name . " (" . $user->user . ")"?></span>
			</div>
			<div id='div_user_services_<?php echo $k ?>' class="row div_user_service" style="display:none;">
				<table class="table table-striped table-bordered clearfix" >
					<tr> <td> Cargando... </td> </tr>
				</table> 
			</div> 
		</div>
	</div>
</li> 
