<?php 
	
?>
<tr>
	
		<td> <?php echo $record->code ?></td>
		<td> <?php echo $record->client ?></td>
		<td align="center" style="width: 30%">   			
	
				
			<button id="edit_client_<?php echo $record->id_client ?>"
				name="clientes[]"
				onclick="edit_client(<?php echo $record->id_client ?>);"
				style="margin: 5px 10px; "/>
				<i a class="fa fa-edit"></i>
			</button>
				
				
			<button id="services_client_<?php echo $record->id_client ?>"
				name="clientes[]"
				value="Servicios"
				onclick="get_client_services(<?php echo $record->id_client ?>);"
				style="margin: 5px 10px; "/>
				<i a class="fa fa-bar-chart-o"></i>
			</button>
				
				
			<button id="delete_client_<?php echo $record->id_client ?>"
				name="clientes[]"
				onclick="delete_client(<?php echo $record->id_client ?>);"
				style="margin: 5px 10px; "/>
				<i a class="fa fa-trash-o"></i>
			</button>
		</td>
	
</tr> 
