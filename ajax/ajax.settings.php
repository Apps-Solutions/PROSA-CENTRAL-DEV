<?php 
global $response; 
global $Session;
global $Settings;

switch ( $action ){
	case 'update_settings': 
		$value 	= $_POST['value'];
		$option	= $_POST['option'];		
		if ( $value != '' && $option != ''){
			$resp = $Settings->save_settings_option( $option , $value, 0, TRUE );
			if ( $resp ){
				$response['message'] = "OK";
				$response['success'] = TRUE;
			} else {
				$response['error'] = "Error updating @ settings: " . $Settings->get_errors();
			}
		} else {
			$response['error'] = "Error updating @ settings: Invalid parameters."; 
		} 
		break; 
	default:
		$response['error'] = "Invalid action.";
		break;
} 
?>