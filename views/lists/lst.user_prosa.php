<?php

?>

<tr> 
	<td>
	  <li class="list-group-item">
		  <div class="row" id="div_row_user_prosa_<?php echo $k ?>">
			  <div class="col-xs-12" >
				  <div class="row" style="cursor:pointer;"  onclick='show_user_services( <?php echo $k ?>, "<?php echo $record->user ?>");'>
					  <span class="col-xs-12"><?php echo $record->name . " (" . $record->email . ")" ?></span>
				  </div>
				  <div id='div_user_services_<?php echo $k ?>' class="row div_user_service" style="display:none;" >
					  <table class="table table-striped table-bordered clearfix" >
						  <tr> <td> Cargando... </td> </tr>
					  </table> 
				  </div> 
			  </div>
		  </div>
	  </li>
	</td>
</tr>
