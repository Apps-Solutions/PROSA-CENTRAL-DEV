<?php
if ( !class_exists('Service')){
	require_once DIRECTORY_CLASS . 'class.service.php';
}
/**
 * PagosDiferidos Class 
 * 
 */
class PagosDiferidos extends Service {
	
	function __construct( $id_client = 0) {
		
		parent::__construct();
		
		$this->class 	= "PagosDiferidos";
		$this->service 	= "Pagos Diferidos";
		$this->code 	= "s1";
		
		$this->id_service = 1;
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
		$query = " SELECT DIA, SUM(TOTAL) AS TOTAL FROM ( "
				. "SELECT DIA, SUM(TOTAL) AS TOTAL FROM " . PFX_SRV_DB . "TBL_MON_HORA_POS_NAC_" . date('Ym') . " "  
				. " WHERE KQ6_ID_TOKEN > '00' AND DIA = :dia GROUP BY DIA"
			/*	. " UNION "
				."SELECT DIA, SUM(TOTAL) AS TOTAL FROM " . PFX_SRV_DB . "TBL_MON_HORA_POS_INT_" . date('Ym') . " "  
				. " WHERE KQ6_ID_TOKEN > '00' AND DIA = :dia GROUP BY DIA"
			*/	. " ) GROUP BY DIA";
		 
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
		$this->indicators[0]['source'] = "Emisor";
		
		$resp = $this->set_service_totals();
		
		$resp = $this->set_top_rejected();
		
	}
	
	private function set_service_totals(){
		global $obj_bd;
		$this->indicators[0]['total_transactions'] = 0;
		$this->indicators[0]['total_accepted'] = 0;
		$this->indicators[0]['total_rejected'] = 0;
		 		
		$query =  " SELECT DIA, SUM(ACCEPTED) AS ACCEPTED, SUM(REJECTED) AS REJECTED, SUM(TOTAL) AS TOTAL "
					. "  FROM ( "
						. " SELECT DIA, SUM(TOTAL) AS TOTAL, " 
							. " SUM(CASE WHEN CODIGO_RESPUESTA < 11 THEN TOTAL ELSE 0 END ) AS ACCEPTED, "
							. " SUM(CASE WHEN CODIGO_RESPUESTA > 10 THEN TOTAL ELSE 0 END ) AS REJECTED " 
						. " FROM " . PFX_SRV_DB . "TBL_MON_HORA_POS_NAC_" . date('Ym') . " "
						. " WHERE KQ6_ID_TOKEN > '00'  AND DIA = :dia "
		 				. (( $this->id_client > 0 ) ? " AND FIID_TARJ = :id_client " : '')
						. " GROUP BY DIA "
				/*		. " UNION  "
						. " SELECT DIA, SUM(TOTAL) AS TOTAL, " 
							. " SUM(CASE WHEN CODIGO_RESPUESTA < 11 THEN TOTAL ELSE 0 END ) AS ACCEPTED, "
							. " SUM(CASE WHEN CODIGO_RESPUESTA > 10 THEN TOTAL ELSE 0 END ) AS REJECTED " 
						. " FROM " . PFX_SRV_DB . "TBL_MON_HORA_POS_INT_" . date('Ym') . " "
						. " WHERE KQ6_ID_TOKEN > '00'  AND DIA = :dia "
		 				. (( $this->id_client > 0 ) ? " AND FIID_TARJ = :id_client " : '')
						. " GROUP BY DIA "
				*/	. " ) GROUP BY DIA ";
					
		$result = $obj_bd->query( $query, array( ":dia" => date('d'), ":id_client" => $this->client_code ) );
		if ( $result !== FALSE ){
			if ( count($result) > 0 ){
				
				$totals = $result[0];
				
				$this->indicators[0]['total_transactions'] = $totals['TOTAL'];
				$this->indicators[0]['total_accepted'] = $totals['ACCEPTED'];
				$this->indicators[0]['total_rejected'] = $totals['REJECTED'];
				
				$this->set_last_total( $totals['TOTAL'] ); 
				return TRUE;
			} else {
				
				$this->set_error("No se obtuvieron valores totales del servicio " . ( $this->client_code != '' ? "(" . $this->client_code . ")" : "" ) . ".", ERR_DB_QRY );
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
		 
		
		$query =  " SELECT CODIGO_RESPUESTA, SUM(TOTAL) AS TOTAL FROM ( "
					  . " SELECT SUM(TOTAL) AS TOTAL, CODIGO_RESPUESTA FROM " . PFX_SRV_DB . "TBL_MON_HORA_POS_NAC_" . date('Ym') 
						. " WHERE CODIGO_RESPUESTA > 10 AND KQ6_ID_TOKEN > '00' AND DIA = :dia "
		 					. (( $this->id_client > 0 ) ? " AND FIID_TARJ = :id_client " : '')
			 			. " GROUP BY CODIGO_RESPUESTA "
			/*		. " UNION "
			 		. " SELECT SUM(TOTAL) AS TOTAL, CODIGO_RESPUESTA FROM " . PFX_SRV_DB . "TBL_MON_HORA_POS_INT_" . date('Ym') 
			 			. " WHERE CODIGO_RESPUESTA > 10 AND KQ6_ID_TOKEN > '00' AND DIA = :dia "
		 					. (( $this->id_client > 0 ) ? " AND FIID_TARJ = :id_client " : '')
			 			. " GROUP BY CODIGO_RESPUESTA "
			*/ 		. " ORDER BY CODIGO_RESPUESTA "
				. " ) GROUP BY CODIGO_RESPUESTA ORDER BY TOTAL DESC ";
		
		$query_top = ' SELECT CODIGO_RESPUESTA, TOTAL FROM ( ' . $query . ' ) WHERE rownum <= 5 ' ;
		
		
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
				$others = array();
				$others['code']		= 0;
				$others['motive']	= 'Otros';
				$others['total']	= 0;
				
				$this->indicators[0]['top_rejected'][] = $others;
				 
				$this->set_error("No se obtuvieron valores del servicio para top rechazados " . ( $this->client_code != '' ? "(" . $this->client_code . ")" : "" ) . ".", ERR_DB_QRY );
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
			
			if ( $this->last_total['timestamp'] > time() - ( $this->time_prosa * 60 ) )
				return TRUE;
			
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
			} 
			
			$day_total = $this->get_day_total( date('d', $when) );
			if ( $day_total ){
				if ( $day_total > $this->last_total['total'] ){
					$this->set_last_total( $day_total );
					return TRUE;
				} else{ 
					$this->set_last_total( $day_total );
					return FALSE;
				}
			} else {
				return FALSE;
			}
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
