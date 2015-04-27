<?php 
	
?>
<tr>
	<tr> 
		<td> <?php echo $record['service']?> </td>
		<td align="center"> 
			<input type="checkbox" 
				id='inp_<?php echo $record['pfx'] ?>service_<?php echo $record['id_service'] ?>'
				name='<?php echo $record['pfx'] ?>services[]'
				value='<?php echo $record['id_service'] ?>' 
				<?php echo ( $record['checked'] == 1 ) ? " checked='checked'" : "" ?>
				class='inp_<?php echo $record['pfx'] ?>service' 
				onchange="<?php echo $record['function'] ?>"				
				/>			
		</td>  
	</tr>
</tr> 