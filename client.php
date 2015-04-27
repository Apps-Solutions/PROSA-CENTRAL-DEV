<?php 
require 'init.php'; 

if ( IS_ADMIN ){
	$action		= isset( $_REQUEST['action'] ) ?  $_REQUEST['action']: '';
	$response	= array('success' => false, 'msg' => '');
	
	switch ( $action ){
		case 'client_edition': 
			require_once DIRECTORY_CLASS . "class.client.php";
			
			$id_client = ( isset( $_POST['id_client'] ) && is_numeric( $_POST['id_client'] ) ) ? $_POST['id_client'] : 0; 
			$client = new Client( $id_client ); 
			$client->client = ( isset( $_POST['client'] ) && $_POST['client'] != '' ) ? strip_tags( $_POST['client']) : "" ;
			$client->code = ( isset( $_POST['code'] ) && $_POST['code'] != '' ) ? strip_tags( $_POST['code']) : "" ;
			$resp = $client->save();  
			if ( $resp ){
				header("Location:index.php?command=" . LST_CLIENT . "&msg=" . urlencode("El registro se guardó correctamente. "));
			} else { 
				header("Location:index.php?command=" . LST_CLIENT. "&err=" . urlencode( "Ocurrió un error al guardar la información." ) ); 
			}
			break;
		default:
			header("Location:index.php?command=" . ERR_404 );
			break;
	}
} else {
	header("Location:index.php?command=" . ERR_403 );
}
die();
?>