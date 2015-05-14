<?php 
if ( IS_ADMIN )
{ 
?>
<tr>
	<tr>
		<td> <?php echo date( 'Y-m-d', $record['al_timestamp']); ?>	</td>
		<td> <?php echo date( 'H:i:s', $record['al_timestamp']); ?> </td>
		<td> <?php echo $record['se_service']; ?> </td>
		<td> <?php echo $record['cl_client']; ?> </td>
		<td class="text-left"> <?php echo $record['al_text']; ?> </td>
		<td> <?php echo $record['al_user']; ?> </td>
	</tr> 
</tr>
<?php } ?>