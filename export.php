<?php
require 'init.php';

    $action = ( isset($_REQUEST['action'] ) ? $_REQUEST['action']  : NULL );
    
    
    if( $action != NULL)
    {
	require_once DIRECTORY_CLASS . "class.datatable.php";
	
	
	header("Content-Type: application/force-download");
	header("Content-Type: application/octet-stream");
	header("Content-Type: application/download");
	header('Content-Disposition: attachment; filename="'.$action.'_'.date("Y-m-d").'.xls"');
	header("Content-Transfer-Encoding: binary");
	header("Pragma: no-cache");
	header("Expires: 0");
	
	
	
	$tabla = new DataTable($action);
	
	if( isset($_REQUEST['date_srch']) && $_REQUEST['date_srch'] != '' )
	{
		if( isset($_REQUEST['date_start']) && $_REQUEST['date_start'] != '' )
		{
			$tabla->set_filter( $_REQUEST['date_srch'], strtotime( $_REQUEST['date_start'] ), '>=' );
		}
		
		if( isset($_REQUEST['date_end']) && $_REQUEST['date_end'] != '' )
		{
			$tabla->set_filter( $_REQUEST['date_srch'], strtotime( $_REQUEST['date_end'] ), '<=' );
		}
	}
	
	$xls = $tabla->get_list_xls();
	
	echo $xls;
	
	
    }
    
    

?>