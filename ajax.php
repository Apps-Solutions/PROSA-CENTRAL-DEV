<?php
require_once "init.php"; 
//ini_set('display_errors', true);
$response 	= array( 'success' => false );
$resource	= isset($_REQUEST['resource']) ? $_REQUEST['resource'] : '';
$action 	= isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

switch ( $resource ){
	case 'agenda':
		require_once DIRECTORY_AJAX . 'ajax.agenda.php';
		break;
	case 'client':
		if ( IS_ADMIN )
			require_once DIRECTORY_AJAX . 'ajax.client.php';
		else 
			$response['error'] = "Restricted access";
		break; 
	case 'lists':
		require_once DIRECTORY_AJAX . 'ajax.lists.php';
		break;
	case 'user':
		/*
		if ( IS_ADMIN )
			require_once DIRECTORY_AJAX . 'ajax.user.php';
		else 
			$response['error'] = "Restricted access"; 
		*/
		require_once DIRECTORY_AJAX . 'ajax.user.php'; 
		break;
	//case 'user':
	case 'profile':
		if ( IS_ADMIN )
			require_once DIRECTORY_AJAX . 'ajax.admin.php';
		else 
			$response['error'] = "Restricted access"; 
		break;  
	default: 
		$responce['error'] = "Invalid resource";
		break; 
	case 'threshold':
		if ( IS_ADMIN )
			require_once DIRECTORY_AJAX . 'ajax.threshold.php';
		else 
			$response['error'] = "Restricted access";
		break;
	
	case 'service':
		if ( IS_ADMIN )
			require_once DIRECTORY_AJAX . 'ajax.service.php';
		else 
			$response['error'] = "Restricted access";
		break;
        case 'all_clients':
            if(IS_ADMIN)
                require_once DIRECTORY_AJAX.'ajax.service.php';
                else
                    $response['error'] = "Restricted access";
            break;
        case 'notification':
			 require_once DIRECTORY_AJAX.'ajax.notification.php';
			/*
                if(IS_ADMIN)
                {
                        require_once DIRECTORY_AJAX.'ajax.notification.php';
                }
                else
                {
                        $response['error'] = "Restricted access";
                }
			 * 
			 */
                break;
} 
echo json_encode( $response );
?>