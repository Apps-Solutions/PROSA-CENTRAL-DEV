<?php 
if ( IS_ADMIN ){
	
?>
<tr>
	<td> <?php echo utf8_decode($record['se_service'])?> </td> 
	<td> 
		<div class="input-group">
		  <input type="text"  
			  	id="inp_<?php 	echo $record['id_service'] ?>_threshold" 
			  	name="inp_<?php echo $record['id_service'] ?>_threshold" 
			  	value="<?php 	echo $record['th_threshold'] ?>" 
			  	placeholder="Umbral" class="form-control" />
		  <span class="input-group-addon"><i class="fa">%</i></span>
		</div> 
	</td>
	<td> 
		<div class="input-group">
		  <input type="text" 
			  	id="inp_<?php 	echo $record['id_service'] ?>_time_prosa" 
			  	name="inp_<?php echo $record['id_service'] ?>_time_prosa" 
			  	value="<?php 	echo $record['th_time_prosa']?>"
			  	placeholder="Tiempo" class="form-control" />
		  <span class="input-group-addon"><i class="fa">min</i> &nbsp; &nbsp; </span>
		</div> 
	</td>
	<td> 
		<div class="input-group">
		  <input type="text" 
			  	id="inp_<?php 	echo $record['id_service'] ?>_time_client" 
			  	name="inp_<?php echo $record['id_service'] ?>_time_client" 
			  	value="<?php 	echo $record['th_time_client'] ?>"  
			  	placeholder="Tiempo" class="form-control" />
		  <span class="input-group-addon"><i class="fa">min</i> &nbsp; &nbsp; </span>
		</div> 
	 </td> 
</tr>
<?php } ?>
