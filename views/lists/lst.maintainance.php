<?php 
if ( IS_ADMIN ){ 
?>
<tr>
	<td> <?php echo utf8_decode($record['se_service']) ?> </td>
	<td> <?php echo date( 'Y-m-d H:i:s', $record['ma_start']); ?> </td>
	<td> <?php echo date( 'Y-m-d H:i:s', $record['ma_end']); ?> 
	<td> <?php
		$t = time();
		$ts = $record['ma_start'];
		$te = $record['ma_end'];
		
		//echo 'tn:'.$t.' ts:'.$ts.' te:'.$te.'<br/>';
		
		if ( $record['MA_END'] < time() )
		{ 
			echo "Finalizado";
		}
		else if ( $record['MA_START'] < time() && $record['ma_end'] > time() )
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
		<?php if ( $record['ma_start'] > time() ){ ?>
			<button onclick="edit_window('<?php echo $record['id_maintenance'] ?>');" > <i a class="fa fa-edit"></i> </button>
			<button onclick="delete_window('<?php echo $record['id_maintenance'] ?>');"> <i a class="fa fa-trash-o"></i> </button>
		<?php } else {
			echo "-";
			}?> 
	</td> 
</tr>
<?php } ?>
