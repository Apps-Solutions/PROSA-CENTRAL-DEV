<?php

class Alert extends Object{
    
    private $db;
    
    function Alert($id_alert = 0)
    {
    	global $obj_bd;
	//$this->db = new oracle_db();
	//$this->$obj_bd = new PDOMySQL();
	if( $id_alert > 0 )
	{
	    //cargar los datos de esa alerta.
	}
	
    }
    
    public function get_list_alerts_html($id_client = 0)
    {
    	global $obj_bd;
	$qry = 	"select id_alert, al_timestamp, se_service, cl_client, al_text, al_user ".
		"from ". PFX_MAIN_DB ."alert inner join ".PFX_MAIN_DB."service on id_service = al_se_id_service inner join ".PFX_MAIN_DB."client on id_client = al_cl_id_client";
		
		
	$where = ( IS_ADMIN ? '' : ' WHERE al_cl_id_client = '.$id_client );
		
	$resp = $this->$obj_bd->query($qry.$where);
	
	if($resp)
	{
	    if( count($resp) > 0)
	    {
		$result = '';
		foreach ($resp as $k => $alert)
		{
		    $record = array();
		    $record['id_alert'] 	= $alert['id_alert'];
		    $record['al_timestamp']	= $alert['al_timestamp'];
		    $record['se_service'] 	= $alert['se_service'];
		    $record['cl_client'] 	= $alert['cl_client'];
		    $record['al_text'] 		= $alert['al_text'];
		    $record['al_user'] 		= $alert['al_user'];
		    
		    ob_start();
		    require DIRECTORY_VIEWS . "/lists/lst.alert.php";
		    $result .= ob_get_clean();
		} 
	    }
	    else
	    {
		$result = '<tr><td>No se encontraron alertas.</td></tr>';
	    }
	}
	
	echo $result;
    }
    
    public function get_alerts_table()
    {
	require_once DIRECTORY_CLASS . "class.datatable.php";
	
	$tabla = new DataTable('lst_alert_history', 'tbl_threshold');
	
	/*
	$tabla->set_filter( $_POST['filterIdx'], $_POST['filterVal']);
	$tabla->fidx = $_POST['filterIdx'];
	$tabla->fval = $_POST['filterVal'];
	*/
	
	$response['html'] = $tabla->get_list_html( FALSE );
	if ( count( $tabla->error ) > 0 )
	{
		$response['error'] = $tabla->get_errors( );
		$response['html'] = "";
		
		echo $response['error'];
	}
	else
	{
		$response['lbl_foot'] = $tabla->get_foot_records_label();
		$response['tpages'] = $tabla->total_pages;
		$response['page'] = $tabla->page;
		$response['trecords'] = $tabla->total_records;
		$response['rows'] = $tabla->rows;
		$response['success'] = TRUE;
	}
	
	return $response['html'];
    }
}

?>