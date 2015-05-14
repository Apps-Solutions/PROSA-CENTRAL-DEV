<?php
/**
* Threshold CLass
* 
* @package		Prosa	
* @since        21/07/2014 
*/ 
class Threshold extends Object {
	 
	public $thresholds;
	public $timestamp;
	
	public $options	= array();
	public $instance= array();  
	public $class = "Threshold";
	public $error = array();
	
	/**
	* Threshold()    
	* Creates a Threshold object from the DB.
	*  
	* @param	$id_threshold (optional) If set populates values from DB record. 
	* 
	*/  
	function Threshold( $id_serv = 0 ){ 
		$this->load($id_serv);
	}
	
	/**
	 * load()
	 * 
	 */
	private function load( $id_serv = 0  ){
		global $obj_bd;
		$qry = "SELECT 
					id_service,
					se_service, 
					se_command, 
					th_threshold, 
					th_time_prosa,
					th_time_client,
					th_timestamp
				FROM " . PFX_MAIN_DB. "service 
					LEFT JOIN " . PFX_MAIN_DB. "threshold ON th_se_id_service = id_service ";
		if ( is_numeric($id_serv) && $id_serv > 0 ){
			$qry .= "WHERE id_service = " . $id_serv . " ";
		} 
		$qry .= " ORDER BY se_service ";
		 
		$this->thresholds = array(); 
		//$db 	= new oracle_db();
		//$db 	= new PDOMySQL();
		$info 	= $obj_bd->query( $qry );
		if ( $info ){
		 	$this->thresholds = $info;
		}  else {
			$this->set_error( "Ocurrió un error al obtener los umbrales de la BBDD. " . $db->error[0], ERR_DB_QRY, 1 );
			return FALSE;
		} 
	}
	
	/**
	 * get_list_hmtl()
	 * 
	 * 
	 */
	 public function get_list_hmtl(){ 
		if ( IS_ADMIN ){
			$response = "";  
			if ( count($this->thresholds) > 0 ){
				foreach ($this->thresholds as $k => $record) { 
					ob_start();
					require DIRECTORY_VIEWS . "/lists/lst.threshold.php"; 
					$response .= ob_get_clean(); 
				} 
			}  
			return $response;
		} else {
			return "";
		} 
	 }
	 

	/**
	 * get_list_maintainance()
	 * 
	 * 
	 */
	 public function get_list_maintainance_html( $p = 0 ){ 
		if ( IS_ADMIN ){
			global $obj_bd;
			//$db = new oracle_db();
			$db = new PDOMySQL();
			$query = "SELECT * FROM " . PFX_MAIN_DB . "maintenance "
						. " INNER JOIN " . PFX_MAIN_DB . "service ON id_service = ma_se_id_service "
					. " WHERE ma_status > 0 ";
			
			$windows = $obj_bd->query( $query );
			$response = ""; 
			if ( count($windows) > 0 ){
				foreach ($windows as $k => $record) { 
					ob_start();
					require DIRECTORY_VIEWS . "/lists/lst.maintainance.php"; 
					$response .= ob_get_clean(); 
				} 
			}
			return $response;
		} else {
			return "";
		} 
	 }
	 
	 public function filter_list_maintainance_html($filtros, $valores, $oprs, $formato)
	 {
	 	global $obj_bd;
		if ( IS_ADMIN )
		{
			
			//$db = new oracle_db();
			$db = new PDOMySQL();
			$query =  " SELECT * FROM " . PFX_MAIN_DB . "maintenance "
				. " INNER JOIN " . PFX_MAIN_DB . "service ON id_service = ma_se_id_service "
				. " WHERE ma_status > 0 ";
			
			$params = array_filter(explode("|", $filtros));
			$values = array_filter(explode("|", $valores));
			$oprdrs = array_filter(explode("|", $oprs));
			$format = array_filter(explode("|", $formato));
			
			$where = '';
			for($i=0; $i<count($params); $i++ )
			{
				if($format[$i] == 'DATE')
					$values[$i] = strtotime($values[$i]);
					
				$where .= ' AND '.$params[$i] . ' '. $oprdrs[$i]. ' ' . $values[$i]. ' ';
			}
			
			$windows = $obj_bd->query( $query.$where );
			$response = "";
			
			if ( count($windows) > 0 )
			{
				foreach ($windows as $k => $record)
				{ 
					ob_start();
					require DIRECTORY_VIEWS . "/lists/lst.maintainance.php"; 
					$response .= ob_get_clean(); 
				} 
			}
			else
			{
				$response = '<tr><td colspan="5">No hay mantenimiento(s) programado(s).</td></tr>';
			}
			
			return $response;//.'<tr><td colspan="5">'.$query.$where.'</td></tr>';
		}
		else {
			$this->set_error("Acción restringida.", SES_RESTRICTED_ACTION, 3);
			return FALSE;
		} 
	 }
	 
	/**
	 * get_services() 
	 * 
	 */
	private function get_services(){ 
		return $this->thresholds; 
	}
	
	/**
	 * get_services_options();
	 * 
	 * 
	 */
	 public function get_service_options(){
	 	if ( IS_ADMIN ){
			$response = "";  
			if ( count($this->thresholds) > 0 ){
				foreach ($this->thresholds as $k => $record) { 
					$response .= "<option value='" . $record['id_service'] . "'>" . utf8_decode($record['se_service']) . " </option>"; 
				} 
			}   
			return $response;
		} else {
			return "";
		} 
	 }
	 
	/**
	* validate()    
	* Validates the values before inputing to Data Base 
	*  
	* @return        Boolean TRUE if valid; FALSE if invalid
	*/ 
	public function validate(){ 
		 return TRUE;
	}
	
	/**
	* save()    
	* Inserts or Update the record in the DB. 
	* 
	* @return        Boolean TRUE if success; FALSE if failed
	*/  
	public function save( $data = FALSE ){
		global $obj_bd;
		if ( IS_ADMIN ){
			if ( $data && is_array($data)){
				//$db = new oracle_db();
				//$db = new PDOMySQL();
				$query = "UPDATE " . PFX_MAIN_DB. "threshold SET "
							. " th_threshold 	= :th_threshold , "
							. " th_time_prosa 	= :th_time_prosa,  "
							. " th_time_client 	= :th_time_client,  "
							. " th_timestamp 	= :th_timestamp " 
						. " WHERE th_se_id_service = :id_service  " ;
				$response = TRUE; 
				foreach ($data as $k => $vals) {
					$params = array( ":th_threshold"  	=> number_format($vals['th_threshold'],2), 
									 ":th_time_prosa" 	=> number_format($vals['th_time_prosa'],0),
									 ":th_time_client" 	=> number_format($vals['th_time_client'],0),
									 ":id_service" 		=> number_format($vals['id_service'],0), 
									 ":th_timestamp"	=> time() 
								);
					$resp = $obj_bd->execute( $query, $params );
					if ( !$resp ){ 
						$this->error[] = $db->get_error_msg( );
					} 
					$response = ( $response && $resp );  
				}
				return $response;
			}
			else return FALSE;
		} else { 
	 		$this->set_error("Acción restringida.", SES_RESTRICTED_ACTION, 3);
	 		return FALSE;
	 	}
	} 
	
	/**
	 * save_maintenance_window()
	 * 
	 */
	 public function save_maintenance_window( $info = FALSE ){
	 	global $obj_bd;
	 	if ( IS_ADMIN ){
	 		if ( $info && is_array( $info ) ){
	 			//$db = new oracle_db();
	 			//$db = new oracle_db();
	 			if ( $info['id_window'] > 0 ){
	 				$query = "UPDATE " . PFX_MAIN_DB . "maintenance SET "
	 							. " ma_se_id_service = :id_service, "
	 							. " ma_start = :ma_start,  "
	 							. " ma_end = :ma_end, "
	 							. " ma_timestamp = :ma_timestamp "
							. " WHERE id_maintenance = :id_maintenance ";
					$data = array( 
								":id_service" 	=> $info['id_service'],
								":ma_start" 	=> $info['win_start'],
								":ma_end" 		=> $info['win_end'],
								":ma_timestamp" => time(),
								":id_maintenance"=> $info['id_window']
							);
	 			} else {
	 				$query = "INSERT INTO " . PFX_MAIN_DB . "maintenance "
	 						. " ( id_maintenance, ma_se_id_service, ma_start, ma_end, ma_timestamp ) "
	 						. " VALUES ( :id_maintenance, :id_service, :ma_start, :ma_end, :ma_timestamp ) ";
					$data = array( 
								":id_service" 	=> $info['id_service'],
								":ma_start" 	=> $info['win_start'],
								":ma_end" 		=> $info['win_end'],
								":ma_timestamp" => time(),
								":id_maintenance"=> $db->get_id( PFX_MAIN_DB . "maintenance", "id_maintenance", 1 )
							);
	 			}
	 			 
				$resp = $obj_bd->execute( $query, $data );
				
				if ( !$resp ){ 
					$errs = $obj_bd->get_error_array( ); 
					$this->set_error( $errs[0], ERR_DB_EXEC, 2);
					return FALSE;
				} 
				else return TRUE;
				
	 		} else { 
		 		$this->set_error("No se recibió la información necesaria.", ERR_VAL_EMPTY, 1);
		 		return FALSE;
		 	}
	 	} else { 
	 		$this->set_error("Acción restringida.", SES_RESTRICTED_ACTION, 3);
	 		return FALSE;
	 	}
	 }
	
	
	/**
	* clean()    
	* Cleans all parameters and resets all objects
	*  
	*/  
	public function clean(){ 
		$this->error = array(); 
	}
	
	public function info_maintenance($id_main)
	{
		global $obj_bd;
		//$db = new oracle_db();
		$db = new PDOMySQL();
		$qry = 	" select id_maintenance, ma_start, ma_end, se_service, ma_se_id_service ".
			" from ". PFX_MAIN_DB."maintenance inner join ".PFX_MAIN_DB."service on id_service = ma_se_id_service ".
			" where id_maintenance = :id_main ";
		
		$result = $obj_bd->query($qry, array( ':id_main' => $id_main ) );
		//$resp = $db->query($qry);
		
		if( $result != FALSE )
		{	
			$info = $result[0];
			$response = array();
			$response["start_str"] = date( 'Y/m/d H:i:s', $info['ma_start']);
			$response["end_str"] =   date( 'Y/m/d H:i:s', $info['ma_end']);
			$response["id_service"] = $info["ma_se_id_service"];
			$response["id_window"] = $info["id_maintenance"];
			$response["qry"] = $qry;
			return $response;
		}
		else
		{
			$errs = $obj_bd->get_error_array( ); 
			$this->set_error( $errs[0], ERR_DB_EXEC, 2);
			return FALSE;
		}
	}
	
	public function get_list_threshold_html($id_table)
	{
		require_once DIRECTORY_CLASS . 'class.datatable.php';
		$tabla = new DataTable('lst_threshold', $id_table);
		
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
