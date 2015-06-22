
<tr> 
    <td><?php echo $record['ps_minutes']; ?></td>
    <td><?php echo $record['ps_name']; ?></td>

    <td align="center">   
  			
	
				
			<button id="edit_client_<?php echo 1; ?>"
				name="clientes[]"
				onclick="edit_client(<?php echo 1; ?>);"
				style="margin: 5px 10px; "/>
				<i a class="fa fa-edit"></i>
			</button>
				
				

				
			<button id="delete_client_<?php echo 1; ?>"
				name="clientes[]"
				onclick="delete_client(<?php echo 1; ?>);"
				style="margin: 5px 10px; "/>
				<i a class="fa fa-trash-o"></i>
			</button>
	
    </td> 

</tr>
