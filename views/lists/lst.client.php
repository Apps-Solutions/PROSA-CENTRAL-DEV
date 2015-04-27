<<<<<<< HEAD
<tr> 
    <td><?php echo $record->code ?></td>
    <td><?php echo $record->client ?></td>
=======

<tr> 
    <td><?php echo $record->client ?></td>
    <td><?php echo $record->code ?></td>
>>>>>>> origin/master
    <td align="center">   
        <input type="checkbox" 
               id='inp_client_<?php echo $record->id_client ?>' 
               name='clients[]' 
               value='<?php echo $record->id_client ?>' 
               class='inp_cliente'
               onchange="service_client('<?php echo $record->id_client ?>', 'client_service');"/> 
    </td> 
<<<<<<< HEAD
</tr>
=======
</tr>
>>>>>>> origin/master
