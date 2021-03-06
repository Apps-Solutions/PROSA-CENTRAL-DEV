<?php
if ( !class_exists('Service')){
	require_once DIRECTORY_CLASS . 'class.service.php';
}
/**
 * SwitchAbierto Class 
 *
 * Servicio que brinda PROSA para efectuar transacciones 
 * a través de una página de Internet (fuente PROSA) 
 * para aquellos comercios que no desean invertir en un desarrollo específico. 
 * Se puede identificar a través de redes lógicas como: PRSA, SAHS, SWDL, PRSH 
 * */ 
class AdminSwitchAbierto extends Service {
	
	function __construct( $id_client = 0) {
		
		parent::__construct();
		
		$this->class 	= "AdminSwitchAbierto";
		$this->service 	= "Switch Abierto";
		$this->code 	= "s4";
		
		$this->id_service = 4;
		$this->has_state = TRUE;
		
		
		$this->id_client = $id_client;
		
		if ( $this->id_client > 0 ){
			$this->set_client_code();
		}
		
		$this->tables	= array(); 
		$this->load_threshold(); 
		$this->load_service();
	}
	
	private function get_day_total( $day , $grouped = FALSE ){
		global $obj_bd;
		/* ORACLE
		 * $query = " SELECT DIA, SUM(TOTAL) AS TOTAL FROM ( "
				. "SELECT DIA, SUM(TOTAL) AS TOTAL FROM " . PFX_SRV_DB . "TBL_MON_HORA_POS_NAC_" . date('Ym') . " "  
				. " WHERE LN_COMER IN ('PRSA', 'SAHS', 'SWDL', 'PRSH' ) AND DIA = :dia GROUP BY DIA "
				. " UNION "
				."SELECT DIA, SUM(TOTAL) AS TOTAL FROM " . PFX_SRV_DB . "TBL_MON_HORA_POS_INT_" . date('Ym') . " "  
				. " WHERE LN_COMER IN ('PRSA', 'SAHS', 'SWDL', 'PRSH' ) AND DIA = :dia GROUP BY DIA "
				. " ) GROUP BY DIA ";
		 */
		 
		$query = " SELECT a.DIA, SUM(a.TOTAL) AS TOTAL FROM ( "
				. "SELECT b.DIA, SUM(b.TOTAL) AS TOTAL FROM " . PFX_SRV_DB . "TBL_MON_HORA_POS_NAC AS b"  
				. " WHERE b.LN_COMER IN ('PRSA', 'SAHS', 'SWDL', 'PRSH' ) AND b.DIA = :dia GROUP BY b.DIA "
				. " UNION "
				."SELECT c.DIA, SUM(c.TOTAL) AS TOTAL FROM " . PFX_SRV_DB . "TBL_MON_HORA_POS_INT AS c"  
				. " WHERE c.LN_COMER IN ('PRSA', 'SAHS', 'SWDL', 'PRSH' ) AND c.DIA = :dia GROUP BY c.DIA "
				. " ) AS a GROUP BY a.DIA ";
		 
		$result = $obj_bd->query( $query, array( ':dia' => $day ) );
		if ( $result !== FALSE ){
			return count( $result[0] ) > 0 ? $result[0]['TOTAL'] : 0;
		} else {
			$this->set_error("Ocurrió un error al obtener la última actualización.", ERR_DB_QRY );
			return FALSE;
		}
	}
	 
	public function load_service(){
		
		$this->state = $this->is_up();
		 
		
		$this->indicators[0]['title'] = "POS";
		$this->indicators[0]['name'] = "POS";
		$this->indicators[0]['source'] = "Adquirente";
		
		
			$resp = $this->set_service_totals();
			$resp = $this->set_top_rejected();

			$value = $this->indicators;
			//$resp = $this->insert_info_charts($value);
			$resp = $this->insert_old_last_total($id_service=4, $value);
		
		
	}
	
	private function set_sellcom_service_totals()
	{
		global $obj_bd;
		$arreglo_datos = array();
		$this->indicators[0]['total_transactions'] = 0;
		$this->indicators[0]['total_accepted'] = 0;
		$this->indicators[0]['total_rejected'] = 0;


		$query = "SELECT MAX(idpra_charts) AS id FROM " . PFX_MAIN_DB . "charts WHERE pcs_type='adquirente_pos' AND pcs_se_id_service=4";

		$result = $obj_bd->query($query);

		if($result !== FALSE)
		{
			if(count($result) > 0)
			{
				$total = $result[0];
				$this->id_service = $total['id'];

				$query = " SELECT * FROM  " . PFX_MAIN_DB . "charts WHERE  idpra_charts=" . $this->id_service;
				$result = $obj_bd->query($query);
				if($result !== FALSE)
				{
					if(count($result) > 0)
					{
						$total = $result[0];
						
						$this->indicators[0]['total_transactions'] = $total['pcs_total_acepted'] + $total['pcs_total_rejected'];
						$this->indicators[0]['total_accepted'] = $total['pcs_total_acepted'];
						$this->indicators[0]['total_rejected'] = $total['pcs_total_rejected'];
						
						$datos = $total['pcs_top_5_rejected'];
						if(count($datos) > 0)
						{	
							$sum = 0;
							$arreglo_datos = explode(",", $datos);
							for($i = 0; $i < 14; $i += 3)
							{
								$rejected =  array();
								$rejected['code'] = $arreglo_datos[$i];
								$rejected['motive'] = $arreglo_datos[$i+1];
								$rejected['total'] = $arreglo_datos[$i+2];

								$sum += $rejected['total'];

								$this->indicators[0]['top_rejected'][] = $rejected;
							}
							$others = array();
							$others['code'] = 0;
							$others['motive'] = 'Otros';
							$others['total'] = $this->indicators[0]['total_rejected'] - $sum;
							 $this->indicators[0]['top_rejected'][] = $others;
							return TRUE;
						}
						
						return TRUE;
					}
					else 
					{
						$this->set_error("No se obtuvieron valores totales del servicio " , ERR_DB_QRY );
						return FALSE;
					}
				}
				else
				{
					$this->set_error("Ocurrió un error al obtener los totales del servicio " , ERR_DB_QRY );
					return FALSE;
				}
			}
			else
			{
				$this->set_error("No se obtuvo el id " , ERR_DB_QRY );
				return FALSE;
			}
		}
		else
		{
			$this->set_error("Ocurrió un error al obtener el id del servicio " , ERR_DB_QRY );
			return FALSE;
		}
	}

	private function set_service_totals(){
		global $obj_bd;
		$this->indicators[0]['total_transactions'] = 0;
		$this->indicators[0]['total_accepted'] = 0;
		$this->indicators[0]['total_rejected'] = 0;
		 /* ORACLE		
		$query =  " SELECT DIA, SUM(ACCEPTED) AS ACCEPTED, SUM(REJECTED) AS REJECTED, SUM(TOTAL) AS TOTAL "
					. "  FROM ( "
						. " SELECT DIA, SUM(TOTAL) AS TOTAL, " 
							. " SUM(CASE WHEN CODIGO_RESPUESTA < 11 THEN TOTAL ELSE 0 END ) AS ACCEPTED, "
							. " SUM(CASE WHEN CODIGO_RESPUESTA > 10 THEN TOTAL ELSE 0 END ) AS REJECTED " 
						. " FROM " . PFX_SRV_DB . "TBL_MON_HORA_POS_NAC_" . date('Ym') . " "
						. " WHERE LN_COMER IN ('PRSA', 'SAHS', 'SWDL', 'PRSH' )  AND DIA = :dia "
		 				. (( $this->id_client > 0 ) ? " AND FIID_COMER = :id_client " : '')
						. " GROUP BY DIA "
						. " UNION  "
						. " SELECT DIA, SUM(TOTAL) AS TOTAL, " 
							. " SUM(CASE WHEN CODIGO_RESPUESTA < 11 THEN TOTAL ELSE 0 END ) AS ACCEPTED, "
							. " SUM(CASE WHEN CODIGO_RESPUESTA > 10 THEN TOTAL ELSE 0 END ) AS REJECTED " 
						. " FROM " . PFX_SRV_DB . "TBL_MON_HORA_POS_INT_" . date('Ym') . " "
						. " WHERE LN_COMER IN ('PRSA', 'SAHS', 'SWDL', 'PRSH' )  AND DIA = :dia "
		 				. (( $this->id_client > 0 ) ? " AND FIID_COMER = :id_client " : '')
						. " GROUP BY DIA "
					. " ) GROUP BY DIA ";
			*/
			$query =  " SELECT a.DIA, SUM(a.ACCEPTED) AS ACCEPTED, SUM(a.REJECTED) AS REJECTED, SUM(a.TOTAL) AS TOTAL "
					. "  FROM ( "
						. " SELECT b.DIA, SUM(b.TOTAL) AS TOTAL, " 
							. " SUM(CASE WHEN b.CODIGO_RESPUESTA < 11 THEN b.TOTAL ELSE 0 END ) AS ACCEPTED, "
							. " SUM(CASE WHEN b.CODIGO_RESPUESTA > 10 THEN b.TOTAL ELSE 0 END ) AS REJECTED " 
						. " FROM " . PFX_SRV_DB . "TBL_MON_HORA_POS_NAC AS b"
						. " WHERE b.LN_COMER IN ('PRSA', 'SAHS', 'SWDL', 'PRSH' )  AND b.DIA = :dia "
		 				. (( $this->id_client > 0 ) ? " AND b.FIID_COMER = :id_client " : '')
						. " GROUP BY b.DIA "
						. " UNION  "
						. " SELECT c.DIA, SUM(c.TOTAL) AS TOTAL, " 
							. " SUM(CASE WHEN c.CODIGO_RESPUESTA < 11 THEN c.TOTAL ELSE 0 END ) AS ACCEPTED, "
							. " SUM(CASE WHEN c.CODIGO_RESPUESTA > 10 THEN c.TOTAL ELSE 0 END ) AS REJECTED " 
						. " FROM " . PFX_SRV_DB . "TBL_MON_HORA_POS_INT AS c"
						. " WHERE LN_COMER IN ('PRSA', 'SAHS', 'SWDL', 'PRSH' )  AND c.DIA = :dia "
		 				. (( $this->id_client > 0 ) ? " AND c.FIID_COMER = :id_client " : '')
						. " GROUP BY c.DIA "
					. " ) AS a GROUP BY a.DIA ";	
		//echo $query;	
		$result = $obj_bd->query( $query, array( ":dia" => date('d'), ":id_client" => $this->client_code ) );
		if ( $result !== FALSE ){
			if ( count($result) > 0 ){
				
				$totals = $result[0];
				
				$this->indicators[0]['total_transactions'] = $totals['TOTAL'];
				$this->indicators[0]['total_accepted'] = $totals['ACCEPTED'];
				$this->indicators[0]['total_rejected'] = $totals['REJECTED'];
				
				//$this->set_last_total( $totals['TOTAL'] ); 
				return TRUE;
			} else {
				
				$this->set_error("No se obtunvieron valores del servicio " . ( $this->client_code != '' ? "(" . $this->client_code . ")" : "" ) . ".", ERR_DB_QRY );
				return FALSE;
			}
		} else {
			$this->set_error("Ocurrió un error al obtener los totales del servicio " . ( $this->client_code != '' ? "(" . $this->client_code . ")" : "" ) . ".", ERR_DB_QRY );
			return FALSE;
		}
	}
	
	private function set_top_rejected(){
			global $obj_bd;
		$this->indicators[0]['top_rejected'] = array();
		 
		/* ORACLE
		$query =  " SELECT CODIGO_RESPUESTA, SUM(TOTAL) AS TOTAL FROM ( "
					  . " SELECT SUM(TOTAL) AS TOTAL, CODIGO_RESPUESTA FROM " . PFX_SRV_DB . "TBL_MON_HORA_POS_NAC_" . date('Ym') 
						. " WHERE CODIGO_RESPUESTA > 10 AND  LN_COMER IN ('PRSA', 'SAHS', 'SWDL', 'PRSH' )  AND DIA = :dia "
		 					. (( $this->id_client > 0 ) ? " AND FIID_TARJ = :id_client " : '')
			 			. " GROUP BY CODIGO_RESPUESTA "
			 		. " UNION "
			 		. " SELECT SUM(TOTAL) AS TOTAL, CODIGO_RESPUESTA FROM " . PFX_SRV_DB . "TBL_MON_HORA_POS_INT_" . date('Ym') 
			 			. " WHERE CODIGO_RESPUESTA > 10 AND  LN_COMER IN ('PRSA', 'SAHS', 'SWDL', 'PRSH' )  AND DIA = :dia "
		 					. (( $this->id_client > 0 ) ? " AND FIID_TARJ = :id_client " : '')
			 			. " GROUP BY CODIGO_RESPUESTA "
			 		. " ORDER BY CODIGO_RESPUESTA "
				. " ) GROUP BY CODIGO_RESPUESTA ORDER BY TOTAL DESC ";
		
		$query_top = ' SELECT CODIGO_RESPUESTA, TOTAL FROM ( ' . $query . ' ) WHERE rownum <= 5 ' ;
		*/
		
		$query =  " SELECT a.CODIGO_RESPUESTA, SUM(a.TOTAL) AS TOTAL FROM ( "
					  . " SELECT SUM(b.TOTAL) AS TOTAL, CODIGO_RESPUESTA FROM " . PFX_SRV_DB . "TBL_MON_HORA_POS_NAC AS b" 
						. " WHERE b.CODIGO_RESPUESTA > 10 AND  b.LN_COMER IN ('PRSA', 'SAHS', 'SWDL', 'PRSH' )  AND b.DIA = :dia "
		 					. (( $this->id_client > 0 ) ? " AND b.FIID_TARJ = :id_client " : '')
			 			. " GROUP BY b.CODIGO_RESPUESTA "
			 		. " UNION "
			 		. " SELECT SUM(c.TOTAL) AS TOTAL, c.CODIGO_RESPUESTA FROM " . PFX_SRV_DB . "TBL_MON_HORA_POS_INT AS c " 
			 			. " WHERE c.CODIGO_RESPUESTA > 10 AND  c.LN_COMER IN ('PRSA', 'SAHS', 'SWDL', 'PRSH' )  AND c.DIA = :dia "
		 					. (( $this->id_client > 0 ) ? " AND c.FIID_TARJ = :id_client " : '')
			 			. " GROUP BY c.CODIGO_RESPUESTA "
			 		. " ORDER BY CODIGO_RESPUESTA "
				. " ) AS a GROUP BY a.CODIGO_RESPUESTA ORDER BY a.TOTAL DESC ";
		
		$query_top = ' SELECT d.CODIGO_RESPUESTA, d.TOTAL FROM ( ' . $query . ' ) AS d LIMIT 0,5' ;
		
		
		$params[':dia'] = date('d');
		if ( $this->id_client > 0 )  $params[':id_client'] =  $this->client_code ; 
		
		$result = $obj_bd->query( $query_top,  $params );
		if ( $result !== FALSE ){
			if ( count($result) > 0 ){
				$sum = 0;
				foreach ($result as $k => $top) {
					$rejected = array();
					$rejected['code'] 	= $top['CODIGO_RESPUESTA'];
					$rejected['motive']	=$this->codes[$top['CODIGO_RESPUESTA']];
					$rejected['total']	= $top['TOTAL'];
					
					$sum += $top['TOTAL']; 
					
					$this->indicators[0]['top_rejected'][] = $rejected;
				}
				
				$others = array();
				$others['code']		= 0;
				$others['motive']	= 'Otros';
				$others['total']	= $this->indicators[0]['total_rejected'] - $sum;
				
				$this->indicators[0]['top_rejected'][] = $others;
				
				return TRUE;
			} else { 
				
				$this->set_error("No se obtunvieron valores del servicio para top rechazados " . ( $this->client_code != '' ? "(" . $this->client_code . ")" : "" ) . ".", ERR_DB_QRY );
				return FALSE;
			}
		} else {
			$this->set_error("Ocurrió un error al obtener los totales del servicio para top rechazados " . ( $this->client_code != '' ? "(" . $this->client_code . ")" : "" ) . ".", ERR_DB_QRY );
			return FALSE;
		} 
	}
	
	public function is_up(){
		
		$this->last_total = $this->get_last_total();
		if ( $this->last_total ){
			
			//if ( $this->last_total['timestamp'] > time() - ( $this->time_prosa * 60 ) )
				/*return TRUE;
			
			if ( date('H') == 1 && date('i') < TIME_DB_UPDATE){
				if ( date('d') == 1 ) {
					if ( $this->last_total['timestamp'] < time() - ( $this->time_prosa * 60 ) ){
						return FALSE;
					} else {
						$this->set_last_total( 0 );
						return TRUE;
					} 
				} else {
					$flag = TRUE;
					$when = time() - (60 * ( TIME_DB_UPDATE + 5 ));
				}
			}else{
				$flag = FALSE;
				$when = time();
			} */
			
			//$day_total = $this->get_day_total( date('d', $when) );
			//if ( $day_total ){
				if ( /*$day_total > $this->last_total['total']*/ $this->last_total['total'] > $this->last_total['pre_total'] ){
					//$this->set_last_total( $day_total );
					return TRUE;
				} else{ 
					return FALSE;
				}
			/*} else {
				return FALSE;
			}*/
		} else {
			return FALSE;
		}
	}

	public function get_array(){
		return array( 
			"id_service" 	=> $this->id_service, 
			"has_state"		=> $this->has_state, 
			"status" 		=> $this->state ? 1 : 0, 
			"services"		=> array(
					array( 
						"title"	 		=> $this->indicators[0]['title'],
						"name"	 		=> $this->indicators[0]['name'],
						"source" 		=> $this->indicators[0]['source'],
						
						"total_transactions"=> $this->indicators[0]['total_transactions'], 
						"accepted" 		=> ( $this->indicators[0]['total_transactions'] > 0 ? number_format ($this->indicators[0]['total_accepted'] * 100 / $this->indicators[0]['total_transactions'] , 2 ) : 0) ,
						"total_accepted"=> $this->indicators[0]['total_accepted'], 
						"rejected"  	=> ( $this->indicators[0]['total_transactions'] > 0 ? number_format ($this->indicators[0]['total_rejected'] * 100 / $this->indicators[0]['total_transactions'] , 2 ) : 0) ,
						"total_rejected"=> $this->indicators[0]['total_rejected'],
						
						"top_rejected" 	=> $this->indicators[0]['top_rejected'] 
					) 
				)
		);
	} 
}

?>
