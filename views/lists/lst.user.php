
<tr> 
	<td> <?php echo $record->name . " (" . $record->email . ")" ?> </td> 
	<td align="center"> 
		<input type="checkbox" id='inp_usr_<?php echo str_replace(".","_", $record->user) ?>' name='users[]' value='<?php echo $record->user ?>' class='inp_user_chk' />
	</td> 
</tr>