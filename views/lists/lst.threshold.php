<?php 
if ( IS_ADMIN ){
	
?>
<tr>
	<td> <?php echo utf8_decode($record['SE_SERVICE'])?> </td> 
	<td> 
		<div class="input-group">
		  <input type="text"  
			  	id="inp_<?php 	echo $record['ID_SERVICE'] ?>_threshold" 
			  	name="inp_<?php echo $record['ID_SERVICE'] ?>_threshold" 
			  	value="<?php 	echo $record['TH_THRESHOLD'] ?>" 
			  	placeholder="Umbral" class="form-control" />
		  <span class="input-group-addon"><i class="fa">%</i></span>
		</div> 
	</td>
	<td> 
		<div class="input-group">
		  <input type="text" 
			  	id="inp_<?php 	echo $record['ID_SERVICE'] ?>_time_prosa" 
			  	name="inp_<?php echo $record['ID_SERVICE'] ?>_time_prosa" 
			  	value="<?php 	echo $record['TH_TIME_PROSA']?>"
			  	placeholder="Tiempo" class="form-control" />
		  <span class="input-group-addon"><i class="fa">min</i> &nbsp; &nbsp; </span>
		</div> 
	</td>
	<td> 
		<div class="input-group">
		  <input type="text" 
			  	id="inp_<?php 	echo $record['ID_SERVICE'] ?>_time_client" 
			  	name="inp_<?php echo $record['ID_SERVICE'] ?>_time_client" 
			  	value="<?php 	echo $record['TH_TIME_CLIENT'] ?>"  
			  	placeholder="Tiempo" class="form-control" />
		  <span class="input-group-addon"><i class="fa">min</i> &nbsp; &nbsp; </span>
		</div> 
	 </td> 
</tr>
<?php } ?>
