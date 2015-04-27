<?php 
global $response; 
global $Session;
global $Settings;

if ( $Session->is_admin() ){
	switch ( $action ){
		case 'delete_client':
			require_once DIRECTORY_CLASS . "class.client.php";
			$id_client = ( isset($_POST['id_client']) && is_numeric($_POST['id_client']) && $_POST['id_client'] > 0 ) ? $_POST['id_client'] : 0;
			if ( $id_client > 0 ){
				$client = new Client( $id_client, FALSE, FALSE ); 
				$resp = $client->delete(); 
				if ( $resp ){
					$response['message'] = "El registro se borró exitosamente.";
					$response['success'] = TRUE;
				} else {
					$response['error'] 	= $client->get_errors();
				}
			} else{
				$response['error'] = "Invalid client.";
			} 
			break; 
		case 'get_client_info':
			require_once DIRECTORY_CLASS . "class.client.php";
			$id_client = ( isset($_POST['id_client']) && is_numeric($_POST['id_client']) && $_POST['id_client'] > 0 ) ? $_POST['id_client'] : 0;
			if ( $id_client > 0 ){
				$client = new Client( $id_client, FALSE, FALSE );
				if ( count($client->error) > 0 ){
					$response['error'] 	= $client->get_errors();
				} else {
					$response['client'] = $client->get_array();
					$response['success'] = TRUE;
				}
			} else{
				$response['error'] = "Invalid client.";
			} 
			break; 
		case 'get_client_services_form':
			require_once DIRECTORY_CLASS . "class.client.php";
			$id_client = ( isset($_POST['id_client']) && is_numeric($_POST['id_client']) && $_POST['id_client'] > 0 ) ? $_POST['id_client'] : 0;
			if ( $id_client > 0 ){
				$client = new Client( $id_client );
				$response['html'] = $client->get_client_services_form();
				if ( count($client->error) > 0 ){
					$response['error'] = $client->get_errors(); 
				} else {
					$response['success'] = TRUE;
				}
			} else{
				$response['error'] = "No se recibieron los datos necesarios.";
			} 
			break; 
		case 'get_client_users_services_table':
			require_once DIRECTORY_CLASS . "class.client.php";
			$id_client = ( isset($_POST['id_client']) && is_numeric($_POST['id_client']) && $_POST['id_client'] > 0 ) ? $_POST['id_client'] : 0;
			$user = ( isset($_POST['user']) && $_POST['user'] != '' ) ? $_POST['user'] : '';
			if ( $id_client > 0 ){ 
				$client = new Client( $id_client );
				$response['html'] = $client->get_client_users_services_table( $user );
				if ( count($client->error) > 0 ){
					$response['error'] = $client->get_errors(); 
				} else {
					$response['success'] = TRUE;
				}
			} else{
				$response['error'] = "No se recibieron los datos necesarios.";
			} 
			break;
		case 'set_client_service':
			$id_client = ( isset($_POST['id_client']) && is_numeric($_POST['id_client']) && $_POST['id_client'] > 0 ) ? $_POST['id_client'] : 0;
			$id_service = ( isset($_POST['id_service']) && is_numeric($_POST['id_service']) && $_POST['id_service'] > 0 ) ? $_POST['id_service'] : 0;
			
			if ( $id_client > 0 && $id_service > 0 ){
				require_once DIRECTORY_CLASS . "class.client.php";
				$client = new Client( $id_client ); 
				$status = ( isset($_POST['status']) && $_POST['status'] == "true" ) ? TRUE : FALSE; 
				$resp = $client->set_service( $id_service, $status );
				if ( $resp === TRUE ){
					$response['success'] = TRUE;
					$response['message'] = "Información actualizada.";
					
				} else {
					$response['error'] = "Ocurrió un error al guardar la información.";
				}
				
			} else{
				$response['error'] = "No se recibieron los datos necesarios.";
			} 
			break;
		case 'insert_service':
				if( IS_ADMIN)
				{
					if ( $_POST['id_client'] && $_POST['id_servicio'] )
					{
						require_once DIRECTORY_CLASS . "class.client.php"; 
						$client = new Client($_POST['cliente']);
						$resp = $client->save_service($_POST['id_servicio']);
						
						
						if( $resp )
						{
							$response['success'] = TRUE;
							$response['message'] = $resp;
							/*PENDIENTE:: insertar en la tabla de la base de datos, reorganizar el query en una funcion de otro nivel,
							reorganizar la funcion del script de admin/lst.client.php y ponerla en algun .js ya creado para que siga
							con la misma estructura.*/
						}
						else
						{
							$response['success'] = FALSE;
							$response['error'] = 'Error...';
						}
					}
					else
					{
						$response['error'] = "Invalid parameters.";
					}
				}
				else
				{
					$response['error'] = "Restricted action.";
				}
				
			break;

		default:
			$response['error'] = "Invalid action.";
			break;
	} 
} else {
	$response['error'] = "Restricted action.";
}
?>
