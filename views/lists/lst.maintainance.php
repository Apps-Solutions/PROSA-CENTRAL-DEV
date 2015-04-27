<?php 
if ( IS_ADMIN ){ 
?>
<tr>
	<td> <?php echo utf8_decode($record['SE_SERVICE']) ?> </td>
	<td> <?php echo date( 'Y-m-d H:i:s', $record['MA_START']); ?> </td>
	<td> <?php echo date( 'Y-m-d H:i:s', $record['MA_END']); ?> 
	<td> <?php
		$t = time();
		$ts = $record['MA_START'];
		$te = $record['MA_END'];
		
		//echo 'tn:'.$t.' ts:'.$ts.' te:'.$te.'<br/>';
		
		if ( $record['MA_END'] < time() )
		{ 
			echo "Finalizado";
		}
		else if ( $record['MA_START'] < time() && $record['MA_END'] > time() )
		{
			echo "Iniciado";
		}
		else if ( $record['MA_START'] >= time() - 600 )
		{
			echo "Por comenzar"; 
		}
		else
		{
			echo "Pendiente";
		}
		?>
	</td> 
	<td>
		<?php if ( $record['MA_START'] > time() ){ ?>
			<button onclick="edit_window('<?php echo $record['ID_MAINTENANCE'] ?>');" > <i a class="fa fa-edit"></i> </button>
			<button onclick="delete_window('<?php echo $record['ID_MAINTENANCE'] ?>');"> <i a class="fa fa-trash-o"></i> </button>
		<?php } else {
			echo "-";
			}?> 
	</td> 
</tr>
<?php } ?>
