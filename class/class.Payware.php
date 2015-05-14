<?php
if ( !class_exists('Service')){
	require_once DIRECTORY_CLASS . 'class.service.php';
}
/**
 * Payware Class 
 * 
 */
class Payware extends Service {
	
	function __construct( $id_client = 0) {
		
		parent::__construct();
		
		$this->class 	= "Payware";
		$this->service 	= "Payware Online";
		$this->code 	= "s3";
		
		$this->id_service = 3;
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
				. " SELECT DIA, SUM(TOTAL) AS TOTAL FROM " . PFX_SRV_DB . "TBL_MON_HORA_POS_NAC_" . date('Ym') . " "  
				. " WHERE LN_TARJ = 'PEMI' AND DIA = :dia GROUP BY DIA"
				. " UNION "
				. " SELECT DIA, SUM(TOTAL) AS TOTAL FROM " . PFX_SRV_DB . "TBL_MON_HORA_POS_INT_" . date('Ym') . " "  
				. " WHERE LN_TARJ = 'PEMI' AND DIA = :dia GROUP BY DIA"
				. " UNION "
				. " SELECT DIA, SUM(TOTAL) AS TOTAL FROM " . PFX_SRV_DB . "TBL_MON_HORA_ATM_NAC_" . date('Ym') . " "  
				. " WHERE LN_TARJ = 'PEMI' AND DIA = :dia GROUP BY DIA"
				. " UNION "
				. " SELECT DIA, SUM(TOTAL) AS TOTAL FROM " . PFX_SRV_DB . "TBL_MON_HORA_ATM_INT_" . date('Ym') . " "  
				. " WHERE LN_TARJ = 'PEMI' AND DIA = :dia GROUP BY DIA" 
				. " ) GROUP BY DIA ";
				
		$query2 = " SELECT DIA, SUM(TOTAL) AS TOTAL FROM ( "
					. " SELECT DIA, SUM(TOTAL) AS TOTAL FROM " . PFX_SRV_DB . "TBL_MON_BIN_POS_NAC_" . date('Ym') . " "  
					. " WHERE DIA = :dia "
						. " AND BIN IN (SELECT BIN  FROM " . PFX_SRV_DB . "TBL_APP_PAYWARE ) "
					. " GROUP BY DIA "
					. " UNION "
					. " SELECT DIA, SUM(TOTAL) AS TOTAL FROM " . PFX_SRV_DB . "TBL_MON_BIN_POS_INT_" . date('Ym') . " "  
					. " WHERE DIA = :dia "
						. " AND BIN IN (SELECT BIN FROM " . PFX_SRV_DB . "TBL_APP_PAYWARE ) "
					. " GROUP BY DIA "
					. " UNION "
					. " SELECT DIA, SUM(TOTAL) AS TOTAL FROM " . PFX_SRV_DB . "TBL_MON_BIN_ATM_NAC_" . date('Ym') . " "  
					. " WHERE DIA = :dia "
						. " AND BIN IN (SELECT BIN FROM " . PFX_SRV_DB . "TBL_APP_PAYWARE ) "
					. " GROUP BY DIA "
					. " UNION "
					. " SELECT DIA, SUM(TOTAL) AS TOTAL FROM " . PFX_SRV_DB . "TBL_MON_BIN_ATM_INT_" . date('Ym') . " "  
					. " WHERE DIA = :dia "
						. " AND BIN IN (SELECT BIN FROM " . PFX_SRV_DB . "TBL_APP_PAYWARE ) "

					. " GROUP BY DIA " 
				. " ) GROUP BY DIA ";
		 
		$result = $obj_bd->query( $query, array( ':dia' => $day ) ); 
		$result2= $obj_bd->query( $query2, array( ':dia' => $day ) );
		
		if ( $result !== FALSE && $result2 !== FALSE ){
			$total = count( $result[0] ) > 0 ? $result[0]['TOTAL'] : 0;
			$total+= count( $result2[0] ) > 0 ? $result2[0]['TOTAL'] : 0;
			
			return $total;
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
		
		$this->indicators[0]['total_transactions'] = 0;
		$this->indicators[0]['total_accepted'] = 0;
		$this->indicators[0]['total_rejected'] = 0;
		
		
		$this->indicators[1]['title'] = "ATM";
		$this->indicators[1]['name'] = "ATM";
		$this->indicators[1]['source'] = "Emisor"; 
		
		$this->indicators[1]['total_transactions'] = 0;
		$this->indicators[1]['total_accepted'] = 0;
		$this->indicators[1]['total_rejected'] = 0;
		
		$resp = $this->set_service_totals(); 
		$resp = $this->set_top_rejected();
		
	}
	
	
	private function set_service_totals(){
		 global $obj_bd;
		$query0 =  " SELECT DIA, SUM(ACCEPTED) AS ACCEPTED, SUM(REJECTED) AS REJECTED, SUM(TOTAL) AS TOTAL "
					. "  FROM ( "
						. " SELECT DIA, SUM(TOTAL) AS TOTAL, " 
							. " SUM(CASE WHEN CODIGO_RESPUESTA < 11 THEN TOTAL ELSE 0 END ) AS ACCEPTED, "
							. " SUM(CASE WHEN CODIGO_RESPUESTA > 10 THEN TOTAL ELSE 0 END ) AS REJECTED " 
						. " FROM " . PFX_SRV_DB . "TBL_MON_HORA_POS_NAC_" . date('Ym') . " "
						. " WHERE LN_TARJ = 'PEMI' AND DIA = :dia " 
		 				. (( $this->id_client > 0 ) ? " AND FIID_TARJ = :id_client " : '')
						. " GROUP BY DIA "
						. " UNION  "
						. " SELECT DIA, SUM(TOTAL) AS TOTAL, " 
							. " SUM(CASE WHEN CODIGO_RESPUESTA < 11 THEN TOTAL ELSE 0 END ) AS ACCEPTED, "
							. " SUM(CASE WHEN CODIGO_RESPUESTA > 10 THEN TOTAL ELSE 0 END ) AS REJECTED " 
						. " FROM " . PFX_SRV_DB . "TBL_MON_HORA_POS_INT_" . date('Ym') . " "
						. " WHERE LN_TARJ = 'PEMI'  AND DIA = :dia " 
		 				. (( $this->id_client > 0 ) ? " AND FIID_TARJ = :id_client " : '')
						. " GROUP BY DIA "
						. " UNION "
						. " SELECT DIA, SUM(TOTAL) AS TOTAL, " 
							. " SUM(CASE WHEN CODIGO_RESPUESTA < 11 THEN TOTAL ELSE 0 END ) AS ACCEPTED, "
							. " SUM(CASE WHEN CODIGO_RESPUESTA > 10 THEN TOTAL ELSE 0 END ) AS REJECTED " 
						. " FROM " . PFX_SRV_DB . "TBL_MON_BIN_POS_NAC_" . date('Ym') . " "
						. " WHERE DIA = :dia "
							. " AND BIN IN (SELECT BIN FROM " . PFX_SRV_DB . "BINES_EMISOR_PROSA " . (( $this->id_client > 0 ) ? " WHERE FIID_TARJ = :id_client " : '') . ") "
		 				. (( $this->id_client > 0 ) ? " AND FIID_TARJ = :id_client " : '')
						. " GROUP BY DIA "
						. " UNION  "
						. " SELECT DIA, SUM(TOTAL) AS TOTAL, " 
							. " SUM(CASE WHEN CODIGO_RESPUESTA < 11 THEN TOTAL ELSE 0 END ) AS ACCEPTED, "
							. " SUM(CASE WHEN CODIGO_RESPUESTA > 10 THEN TOTAL ELSE 0 END ) AS REJECTED " 
						. " FROM " . PFX_SRV_DB . "TBL_MON_BIN_POS_INT_" . date('Ym') . " "
						. " WHERE DIA = :dia "
							. " AND BIN IN (SELECT BIN FROM " . PFX_SRV_DB . "BINES_EMISOR_PROSA " . (( $this->id_client > 0 ) ? " WHERE FIID_TARJ = :id_client " : '') . ") "
		 				. (( $this->id_client > 0 ) ? " AND FIID_TARJ = :id_client " : '')
						. " GROUP BY DIA " 
					. " ) GROUP BY DIA ";
		
		$query1 =  " SELECT DIA, SUM(ACCEPTED) AS ACCEPTED, SUM(REJECTED) AS REJECTED, SUM(TOTAL) AS TOTAL "
					. "  FROM ( "
					
						. " SELECT DIA, SUM(TOTAL) AS TOTAL, " 
							. " SUM(CASE WHEN CODIGO_RESPUESTA < 11 THEN TOTAL ELSE 0 END ) AS ACCEPTED, "
							. " SUM(CASE WHEN CODIGO_RESPUESTA > 10 THEN TOTAL ELSE 0 END ) AS REJECTED " 
						. " FROM " . PFX_SRV_DB . "TBL_MON_HORA_ATM_NAC_" . date('Ym') . " "
						. " WHERE LN_TARJ = 'PEMI' AND DIA = :dia " 
		 				. (( $this->id_client > 0 ) ? " AND FIID_TARJ = :id_client " : '')
						. " GROUP BY DIA "
						
						. " UNION  "
						
						. " SELECT DIA, SUM(TOTAL) AS TOTAL, " 
							. " SUM(CASE WHEN CODIGO_RESPUESTA < 11 THEN TOTAL ELSE 0 END ) AS ACCEPTED, "
							. " SUM(CASE WHEN CODIGO_RESPUESTA > 10 THEN TOTAL ELSE 0 END ) AS REJECTED " 
						. " FROM " . PFX_SRV_DB . "TBL_MON_HORA_ATM_INT_" . date('Ym') . " "
						. " WHERE LN_TARJ = 'PEMI'  AND DIA = :dia " 
		 				. (( $this->id_client > 0 ) ? " AND FIID_TARJ = :id_client " : '') 
						. " GROUP BY DIA "
						
						. " UNION "
						
						. " SELECT DIA, SUM(TOTAL) AS TOTAL, " 
							. " SUM(CASE WHEN CODIGO_RESPUESTA < 11 THEN TOTAL ELSE 0 END ) AS ACCEPTED, "
							. " SUM(CASE WHEN CODIGO_RESPUESTA > 10 THEN TOTAL ELSE 0 END ) AS REJECTED " 
						. " FROM " . PFX_SRV_DB . "TBL_MON_BIN_ATM_NAC_" . date('Ym') . " "
						. " WHERE DIA = :dia "
							. " AND BIN IN (SELECT BIN FROM " . PFX_SRV_DB . "BINES_EMISOR_PROSA " . (( $this->id_client > 0 ) ? " WHERE FIID_TARJ = :id_client " : '') . ") "
		 				. (( $this->id_client > 0 ) ? " AND FIID_TARJ = :id_client " : '')
						. " GROUP BY DIA "
						
						. " UNION  "
						
						. " SELECT DIA, SUM(TOTAL) AS TOTAL, " 
							. " SUM(CASE WHEN CODIGO_RESPUESTA < 11 THEN TOTAL ELSE 0 END ) AS ACCEPTED, "
							. " SUM(CASE WHEN CODIGO_RESPUESTA > 10 THEN TOTAL ELSE 0 END ) AS REJECTED " 
						. " FROM " . PFX_SRV_DB . "TBL_MON_BIN_ATM_INT_" . date('Ym') . " "
						. " WHERE DIA = :dia "
							. " AND BIN IN (SELECT BIN FROM " . PFX_SRV_DB . "BINES_EMISOR_PROSA " . (( $this->id_client > 0 ) ? " WHERE FIID_TARJ = :id_client " : '') . ") "
		 				. (( $this->id_client > 0 ) ? " AND FIID_TARJ = :id_client " : '')
						. " GROUP BY DIA " 
						
					. " ) GROUP BY DIA ";
					
		$result0 = $obj_bd->query( $query0, array( ":dia" => date('d'), ":id_client" => $this->client_code ) );
		
		$result1 = $obj_bd->query( $query1, array( ":dia" => date('d'), ":id_client" => $this->client_code ) );
		
		if ( $result0 !== FALSE && $result1 !== FALSE){
			$t0 = 0;
			$t1 = 0;
			if ( count($result0) > 0 ){
				
				$totals = $result0[0];
				
				$this->indicators[0]['total_transactions'] = $totals['TOTAL'];
				$this->indicators[0]['total_accepted'] = $totals['ACCEPTED'];
				$this->indicators[0]['total_rejected'] = $totals['REJECTED'];
				
				$t0 = $totals['TOTAL'];
			} else { 
				$this->set_error(" No se obtunvieron valores del servicio POS.", ERR_DB_QRY ); 
			}
			
			if ( count($result1) > 0 ){
				
				$totals = $result1[0];
				
				$this->indicators[1]['total_transactions'] = $totals['TOTAL'];
				$this->indicators[1]['total_accepted'] = $totals['ACCEPTED'];
				$this->indicators[1]['total_rejected'] = $totals['REJECTED'];
				
				$t1 = $totals['TOTAL']; 
			} else { 
				$this->set_error(" No se obtunvieron valores del servicio ATM.", ERR_DB_QRY ); 
			}
			
			$this->set_last_total( $t0 + $t1 ); 
		} else {
			$this->set_error(" Ocurrió un error al obtener los totales del servicio." . print_r( $this->db->error , TRUE ) , ERR_DB_QRY );
			return FALSE;
		}
	}
	
	private function set_top_rejected(){
		$this->set_top_rejected_POSATM( 'POS', 0);
		$this->set_top_rejected_POSATM( 'ATM', 1); 
	} 
	
	private function set_top_rejected_POSATM( $srv, $idx){
		global $obj_bd;
		$this->indicators[$idx]['top_rejected'] = array(); 
		
		$query =  " SELECT CODIGO_RESPUESTA, SUM(TOTAL) AS TOTAL FROM ( "
					  . " SELECT SUM(TOTAL) AS TOTAL, CODIGO_RESPUESTA FROM " . PFX_SRV_DB . "TBL_MON_HORA_" . $srv . "_NAC_" . date('Ym') 
						. " WHERE CODIGO_RESPUESTA > 10 AND LN_TARJ = 'PEMI' AND DIA = :dia "
		 					. (( $this->id_client > 0 ) ? " AND FIID_TARJ= :id_client " : '')
			 			. " GROUP BY CODIGO_RESPUESTA "
				 		. " UNION "
				 		. " SELECT SUM(TOTAL) AS TOTAL, CODIGO_RESPUESTA FROM " . PFX_SRV_DB . "TBL_MON_HORA_" . $srv . "_INT_" . date('Ym') 
				 			. " WHERE CODIGO_RESPUESTA > 10 AND LN_TARJ = 'PEMI' AND DIA = :dia " 
			 					. (( $this->id_client > 0 ) ? " AND FIID_TARJ= :id_client " : '')
				 			. " GROUP BY CODIGO_RESPUESTA " 
				 		. " UNION "
						  . " SELECT SUM(TOTAL) AS TOTAL, CODIGO_RESPUESTA FROM " . PFX_SRV_DB . "TBL_MON_BIN_" . $srv . "_NAC_" . date('Ym') 
							. " WHERE CODIGO_RESPUESTA > 10 AND DIA = :dia "
								. " AND BIN IN (SELECT BIN FROM " . PFX_SRV_DB . "BINES_EMISOR_PROSA " . (( $this->id_client > 0 ) ? " WHERE FIID_TARJ = :id_client " : '') . ") "
			 					. (( $this->id_client > 0 ) ? " AND FIID_TARJ= :id_client " : '')
				 			. " GROUP BY CODIGO_RESPUESTA "
				 		. " UNION "
				 		. " SELECT SUM(TOTAL) AS TOTAL, CODIGO_RESPUESTA FROM " . PFX_SRV_DB . "TBL_MON_BIN_" . $srv . "_INT_" . date('Ym') 
				 			. " WHERE CODIGO_RESPUESTA > 10 AND DIA = :dia "
								. " AND BIN IN (SELECT BIN FROM " . PFX_SRV_DB . "BINES_EMISOR_PROSA " . (( $this->id_client > 0 ) ? " WHERE FIID_TARJ = :id_client " : '') . ") "
			 					. (( $this->id_client > 0 ) ? " AND FIID_TARJ= :id_client " : '')
				 			. " GROUP BY CODIGO_RESPUESTA "
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
					 
					$this->indicators[$idx]['top_rejected'][] = $rejected;
				} 
				
				$others = array();
				$others['code']		= 0;
				$others['motive']	= 'Otros';
				$others['total']	= $this->indicators[$idx]['total_rejected'] - $sum; 
				$this->indicators[$idx]['top_rejected'][] = $others;
				
				return TRUE;
			} else { 
				$this->set_error("No se obtunvieron valores del servicio para top rechazados $srv  " . ( $this->client_code != '' ? "(" . $this->client_code . ")" : "" ) . ".", ERR_DB_QRY );
				return FALSE;
			}
		} else {
			$this->set_error("Ocurrió un error al obtener los totales del servicio para top rechazados $srv  " . ( $this->client_code != '' ? "(" . $this->client_code . ")" : "" ) . ".", ERR_DB_QRY );
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
		$resp = array( 
				"id_service" 	=> $this->id_service,
				"has_state"		=> $this->has_state, 
				"status" 		=> $this->state ? 1 : 0,
				"services"		=> array()
			);
			
		foreach ( $this->indicators as $k => $ind){
			$resp['services'][] = array(  
							"title"	 		=> $ind['title'],
							"name"	 		=> $ind['name'],
							"source" 		=> $ind['source'],
							
							"total_transactions"=> $ind['total_transactions'], 
							"accepted" 		=> ( $ind['total_transactions'] > 0 ? number_format ($ind['total_accepted'] * 100 / $ind['total_transactions'] , 2 ) : 0) ,
							"total_accepted"=> $ind['total_accepted'], 
							"rejected"  	=> ( $ind['total_transactions'] > 0 ? number_format ($ind['total_rejected'] * 100 / $ind['total_transactions'] , 2 ) : 0) ,
							"total_rejected"=> $ind['total_rejected'],
							
							"top_rejected" 	=> $ind['top_rejected'] 
						) ;
		}
		return $resp;
	}  	
}
?>
