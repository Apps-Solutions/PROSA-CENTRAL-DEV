<?php 
require 'init.php';

if ( IS_ADMIN ){
	$action		= isset( $_REQUEST['action'] ) ?  $_REQUEST['action']: '';
	$response	= array('success' => false, 'msg' => '');
	
	switch ( $action ){
		case 'threshold_edition': 
			require_once DIRECTORY_CLASS . "class.threshold.php";
			$threshold = new Threshold(); 
			$data = array();
			foreach ($threshold->thresholds as $k => $serv) {
				$id = $serv['ID_SERVICE']; 
				if ( isset($_POST['inp_' . $id . '_threshold']) && $_POST['inp_' . $id . '_threshold'] != '' ){
					$thr 		 = is_numeric( $_POST['inp_' . $id . '_threshold'] ) 	? $_POST['inp_' . $id . '_threshold'] 	: 0;
					$time_prosa  = is_numeric( $_POST['inp_' . $id . '_time_prosa'] ) 	? $_POST['inp_' . $id . '_time_prosa'] 	: 0;
					$time_client = is_numeric( $_POST['inp_' . $id . '_time_client'] ) 	? $_POST['inp_' . $id . '_time_client'] : 0;
					$data[] = array( 
									'id_service' 	=> $id ,
									'th_threshold' 	=> $thr,
									'th_time_prosa' => $time_prosa,
									'th_time_client'=> $time_client 
								);
				} 
			}  
			$resp = $threshold->save( $data ); 
			if ( $resp ){
				header("Location:index.php?command=" . NTF_FRM_THRESHOLD );
			} else {
				header("Location:index.php?command=" . NTF_FRM_THRESHOLD . "&err=" . urlencode("Ocurrió un error al guardar la información.") );
			}
			break;
		case 'edit_maintenance_window':
			require_once DIRECTORY_CLASS . "class.threshold.php";
			$threshold = new Threshold();
			$info['id_window'] 	= is_numeric( $_POST['id_window'] ) ? $_POST['id_window'] 	: 0;
			$info['id_service'] = is_numeric( $_POST['win_id_service'] )? $_POST['win_id_service'] 	: 0;
			$info['win_start'] 	= ( $_POST['win_start'] != '') 	? get_datetime2time($_POST['win_start']) : 0;
			$info['win_end'] 	= ( $_POST['win_end'] != '' ) 	? get_datetime2time($_POST['win_end']) 	 : 0;
			 
			$resp = $threshold->save_maintenance_window( $info );
			if ( !$resp ){
				$err = "No se pudo guardar la información. ";
				foreach ($threshold->error as $k => $e) {
					$err .= ( $k>0 ? "<br/>" : "" ) . $e;
				} 
				header("Location:index.php?command=" . NTF_FRM_THRESHOLD ."&tab=mnt&err=" . urlencode( $err ) );
			} else {
				header("Location:index.php?command=" . NTF_FRM_THRESHOLD ."&tab=mnt&msg=" . urlencode( "El registro se guardó correctamente.")); 
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