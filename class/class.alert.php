<?php

class Alert extends Object{
    
    private $db;

    
    function Alert($id_alert = 0)
    {
	$this->db = new oracle_db();
	
	if( $id_alert > 0 )
	{
	    //cargar los datos de esa alerta.
	}
	
    }
    
    public function get_list_alerts_html($id_client = 0)
    {
	$qry = 	"select id_alert, al_timestamp, se_service, cl_client, al_text, al_user ".
		"from ". PFX_MAIN_DB ."alert inner join ".PFX_MAIN_DB."service on id_service = al_se_id_service inner join ".PFX_MAIN_DB."client on id_client = al_cl_id_client";
		
		
	$where = ( IS_ADMIN ? '' : ' WHERE al_cl_id_client = '.$id_client );
		
	$resp = $this->db->query($qry.$where);
	
	if($resp)
	{
	    if( count($resp) > 0)
	    {
		$result = '';
		foreach ($resp as $k => $alert)
		{
		    $record = array();
		    $record['id_alert'] 	= $alert['ID_ALERT'];
		    $record['al_timestamp']	= $alert['AL_TIMESTAMP'];
		    $record['se_service'] 	= $alert['SE_SERVICE'];
		    $record['cl_client'] 	= $alert['CL_CLIENT'];
		    $record['al_text'] 		= $alert['AL_TEXT'];
		    $record['al_user'] 		= $alert['AL_USER'];
		    
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