<?php 
global $response; 
if ( IS_ADMIN ){
	switch ( $action ){
		case 'is_unique_user': 
			global $Validate;
			$id_user = $_POST['id_user'];
			$user 	 = $_POST['user']; 
			$id_user = ( !is_numeric($id_user) ) ? 0 : $id_user; 
			$response['unique']  = $Validate->is_unique( 'user', 'us_user', $user, 'id_user', $id_user );
			$response['success'] = TRUE; 
			break;
		case 'get_user_info': 
			require_once DIRECTORY_CLASS . "class.user.php";
			$id_user = ( isset($_POST['id_user']) && is_numeric($_POST['id_user']) && $_POST['id_user'] > 0 ) ? $_POST['id_user'] : 0;
			if ( $id_user > 0 ){
				$User = new User( $id_user );
				$response['info'] = $User->get_array();
				if ( count($User->error) > 0 ){
					$response['error'] = $User->get_errors(); 
				} else {
					$response['success'] = TRUE;
				}
			} else{
				$response['error'] = "Invalid user.";
			} 
			break;
		case 'get_user_info_html':
			require_once DIRECTORY_CLASS . "class.user.php";
			$id_user = ( isset($_POST['id_user']) && is_numeric($_POST['id_user']) && $_POST['id_user'] > 0 ) ? $_POST['id_user'] : 0;
			if ( $id_user > 0 ){
				$User = new User( $id_user ); 
				$response['html'] = $User->get_user_html( TRUE );
				if ( count($User->error) > 0 ){
					$response['error'] = $User->get_errors(); 
				} else {
					$response['success'] = TRUE;
				}
			} else{
				$response['error'] = "Invalid user.";
			} 
			
			break;
		default:
			$response['error'] = "Invalid action.";
			break;
	}
} else {
	$response['error'] = "Invalid action.";
}
?>