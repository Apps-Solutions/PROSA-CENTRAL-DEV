<?php 
if ( IS_ADMIN )
{ 
?>
<tr>
	<tr>
		<td> <?php echo date( 'Y-m-d', $record['AL_TIMESTAMP']); ?>	</td>
		<td> <?php echo date( 'H:i:s', $record['AL_TIMESTAMP']); ?> </td>
		<td> <?php echo $record['SE_SERVICE']; ?> </td>
		<td> <?php echo $record['CL_CLIENT']; ?> </td>
		<td class="text-left"> <?php echo $record['AL_TEXT']; ?> </td>
		<td> <?php echo $record['AL_USER']; ?> </td>
	</tr> 
</tr>
<?php } ?>