<?php 
global $response; 
global $Session;
global $Settings;

switch ( $action )
{
		case 'set_client_user_service':
			$us_user = ( isset($_POST['us_user']) && $_POST['us_user'] != "" ) ? $_POST['us_user'] : "";
			$id_client = ( isset($_POST['id_client']) && is_numeric($_POST['id_client']) && $_POST['id_client'] > 0 ) ? $_POST['id_client'] : 0;
			$id_service = ( isset($_POST['id_service']) && is_numeric($_POST['id_service']) && $_POST['id_service'] > 0 ) ? $_POST['id_service'] : 0;
			
			if ( $id_client > 0 && $us_user != "" && $id_service > 0 )
			{
				require_once DIRECTORY_CLASS . "class.user.php";
				$usuario = new User( ); 
				$status = ( isset($_POST['status']) && $_POST['status'] == "true" ) ? TRUE : FALSE; 
				$resp = $usuario->set_client_user_service( $id_service, $us_user, $status );
				if ( $resp === TRUE )
				{
					$response['success'] = TRUE;
					$response['message'] = "Informacion actualizada.";
					
				}
				else
				{
					$response['error'] = "Ocurri— un error al guardar la informaci—n.";
				}								
			}
			else
			{
				$response['error'] = "No se recibieron los datos necesarios.";
			} 
		    break;
			
		case 'get_client_users_services_table':

		    //require_once DIRECTORY_CLASS . "class.client.php";
			require_once DIRECTORY_CLASS . "class.user.php";
			
			global $Session; 
		    $id_client = $Session->get_user_client();
			//$id_client = ( isset($_POST['id_client']) && is_numeric($_POST['id_client']) && $_POST['id_client'] > 0 ) ? $_POST['id_client'] : 0;
			$user = ( isset($_POST['user']) && $_POST['user'] != '' ) ? $_POST['user'] : '';
			//$id_user = $Session->user;
			$usuario = new User();
			
			if ( $id_client > 0 )
			{ 
				$response['html'] = $usuario->get_client_users_services_table( $id_client, $user );
				
				if ( count($usuario->error) > 0 )
				{
					$response['error'] = $usuario->get_errors(); 
				}
				else
				{
					$response['success'] = TRUE;
				}
			}
			else
			{
				$response['error'] = "No se recibieron los datos necesarios.";
			} 
			break;
			
			
		case 'get_services_prosa_table':
		
				if( IS_ADMIN)
				{
						
						require_once DIRECTORY_CLASS . "class.agenda.php";
						$user = ( isset($_POST['user']) && $_POST['user'] != '' ) ? $_POST['user'] : '';
						
						$agenda = new Agenda( );
						$response['html'] = $agenda->get_services_users_prosa( $user );
						
						if ( count($agenda->error) > 0 )
						{
							$response['error'] = $client->get_errors(); 
						}
						else
						{
							$response['success'] = TRUE;
						}
				}
				else
						$response['error'] = "Accion Restringida.";
				
				break;
				
		case 'set_user_service_prosa':
				
		    if(IS_ADMIN)
			{
				$us_user = ( isset($_POST['us_user']) && $_POST['us_user'] != "" ) ? $_POST['us_user'] : "";
				$id_service = ( isset($_POST['id_service']) && is_numeric($_POST['id_service']) && $_POST['id_service'] > 0 ) ? $_POST['id_service'] : 0;
				
				if ( $us_user != "" && $id_service > 0 )
				{
					require_once DIRECTORY_CLASS . "class.user.php";
					$usuario = new User( ); 
					$status = ( isset($_POST['status']) && $_POST['status'] == "true" ) ? TRUE : FALSE; 
					$resp = $usuario->set_client_user_service( $id_service, $us_user, $status );
					if ( $resp )
					{
						$response['success'] = TRUE;
						$response['message'] = "Informacion actualizada.";
						
					}
					else
					{
						$response['error'] = "Ocurri— un error al guardar la informaci—n.";
					}								
				}
				else
				{
					$response['error'] = "No se recibieron los datos necesarios.";
				}
			}
			else
			{
				$response['error'] = "Accion restringida.";
			}
			break;

		default:
			$response['error'] = "Invalid action.";
			break;
} 
?>