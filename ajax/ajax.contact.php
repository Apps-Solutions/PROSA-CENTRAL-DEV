<?php 
global $response; 
global $Session;
global $Settings;

switch ( $action ){
	case 'get_contact_option':
		if ( $_POST['id_contact_option'] && $_POST['id_contact_option'] > 0 ){
			$id_contact_option = $_POST['id_contact_option'];
			
			require_once DIRECTORY_CLASS . "class.contact_meta.php";
			$contact_meta = new ContactMeta();
			$option = $contact_meta->get_option( $id_contact_option );
			if ( $option ){
				$response['message'] = "OK";
				$response['info'] 	 = $option;
				$response['success'] = TRUE;
			} else {
				$response['error'] = "Option not found.";
			}
		} else {
			$response['error'] = "Invalid parameters.";
		}
		break;
	case 'save_contact_option':
		if ( $_POST['id_contact_option'] && $_POST['id_contact_option'] > 0 ){
			$id_contact_option = $_POST['id_contact_option'];
		} else $id_contact_option = 0;
		
		$valid = TRUE;
		$option = new stdClass;
		$option->id_option = $id_contact_option;
		
		if ( $_POST['label'] && $_POST['label'] != '' ){
			$option->label = $_POST['label'];
		} else {
			$valid = FALSE;
			$response['error'] = "Etiqueta inválida.";
		} 
		if ( $_POST['id_data_type'] && $_POST['id_data_type'] > 0 ){
			$option->id_data_type = $_POST['id_data_type'];
		} else {
			$valid = FALSE;
			$response['error'] = "Tipo de dato inválido.";
		}  
		if ( $_POST['options'] && $_POST['options'] != '' ){
			$option->options = $_POST['options'];
		} 

		if ( $valid ){
			require_once DIRECTORY_CLASS . "class.contact_meta.php";
			$contact_meta = new ContactMeta();
			$resp = $contact_meta->save_option( $option );
			if ( $resp ){
				$contact_meta = new ContactMeta();
				$response['message'] = "OK";
				ob_start();
				$contact_meta->get_list_html( TRUE );
				$response['html'] 	 = ob_get_clean(); 
				$response['success'] = TRUE;
			} else {
				$response['error'] = "Error al guardar la información: " . $contact_meta->get_errors();
			}
		}
		
		break;
	case 'delete_contact_option':
		if ( IS_ADMIN ){
			if ( $_POST['id_contact_option'] && $_POST['id_contact_option'] > 0 ){
				$id_contact_option = $_POST['id_contact_option'];
				
				require_once DIRECTORY_CLASS . "class.contact_meta.php";
				$contact_meta = new ContactMeta();
				$option = $contact_meta->delete_option( $id_contact_option );
				if ( $option ){
					$contact_meta = new ContactMeta();
					$response['message'] = "OK";
					ob_start();
					$contact_meta->get_list_html( TRUE );
					$response['html'] 	 = ob_get_clean(); 
					$response['success'] = TRUE;
				} else {
					$response['error'] = "Error al guardar la información: " . $contact_meta->get_errors();
				}
			} else {
				$response['error'] = "Invalid parameters.";
			}
		}else {
			$response['error'] = "Restricted action.";
		} 
		break;
	default:
		$response['error'] = "Invalid action.";
		break;
} 
?>