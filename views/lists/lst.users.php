<?php
 $usr = $record;
?>
<tr>
	<td align='center'> <?php echo $usr['id_user'] ?> </td>
	<td align='center'> <?php  
			if ( $usr['co_sex'] == 'F' ){
				$imagen = "img/ico_ella.png";
			} else{ 
				$imagen = "img/ico_el.png";
			} 
		?><div class="avatar">
			<img class="img-rounded" alt="<?php echo $usr['us_user'] ?>" src="<?php echo $imagen; ?>">
		</div>
	</td>
	<td> <?php echo $usr['us_user'] ?> </td>
	<td> <?php echo $usr['name'] ?> </td>
	<td> <?php echo $usr['pf_profile'] ?> </td>
	<td align='center'> <?php echo date('Y-m-d H:i:s',$usr['us_lastlogin']) ?> </td>
	<td align='center'> 
		<button class='button' title="Consultar" onclick='detail_user(<?php echo $usr['id_user'] ?>);'><i class="fa fa-eye"></i></button>
		<button class='button' title="Editar" 	 onclick='edit_user(<?php echo $usr['id_user'] ?>);'><i class="fa fa-edit"></i></button>
	<?php
	if ( IS_ADMIN ){
	?>
		<button class='button' title="Cambiar Contraseña" onclick='change_password(<?php echo $usr['id_user'] ?>);'><i class="fa fa-lock"></i></button>
		<button class='button' title="Eliminar" onclick='delete_user(<?php echo $usr['id_user'] ?>);'><i class="fa fa-eraser"></i></button>
	<?php
	} 
	?> 
	</td>
</tr>