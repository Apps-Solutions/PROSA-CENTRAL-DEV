<?php
if ( !class_exists('Service')){
	require_once DIRECTORY_CLASS . 'class.service.php';
}
/**
 * PREA Class 
 * 
 * Servicio que PROSA brinda tanto para dispositivos del tipo POS Y ATM, 
 * donde el Cliente Emisor genera reglas a fin de que sea validada la transacción 
 * y en caso de no cumplirlas se genere un rechazo.
 * Se identifican a través de ciertos bines 
 * 
 */
class AdminPREA extends Service {
	
	function __construct( $id_client = 0) {
		
		parent::__construct();
		
		$this->class 	= "AdminPREA";
		$this->service 	= "Preautorizador (PREA)";
		$this->code 	= "s2";
		
		$this->id_service = 2;
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
		/*ORACLE
		$query = " SELECT DIA, SUM(TOTAL) AS TOTAL FROM ( "
					. " SELECT DIA, SUM(TOTAL) AS TOTAL FROM " . PFX_SRV_DB . "TBL_MON_BIN_POS_NAC_" . date('Ym') . " "  
					. " WHERE BIN IN ( SELECT PREFIJO FROM " . strtoupper(PFX_MAIN_DB) . "TBL_APP_PREAUTORIZADOR ) AND DIA = :dia GROUP BY DIA "
					. " UNION "
					. " SELECT DIA, SUM(TOTAL) AS TOTAL FROM " . PFX_SRV_DB . "TBL_MON_BIN_POS_INT_" . date('Ym') . " "  
					. " WHERE BIN IN ( SELECT PREFIJO FROM " . strtoupper(PFX_MAIN_DB) . "TBL_APP_PREAUTORIZADOR ) AND DIA = :dia GROUP BY DIA "
					. " UNION "
					. " SELECT DIA, SUM(TOTAL) AS TOTAL FROM " . PFX_SRV_DB . "TBL_MON_BIN_ATM_NAC_" . date('Ym') . " "  
					. " WHERE BIN IN ( SELECT PREFIJO FROM " . strtoupper(PFX_MAIN_DB) . "TBL_APP_PREAUTORIZADOR ) AND DIA = :dia GROUP BY DIA "
					. " UNION "
					. " SELECT DIA, SUM(TOTAL) AS TOTAL FROM " . PFX_SRV_DB . "TBL_MON_BIN_ATM_INT_" . date('Ym') . " "  
					. " WHERE BIN IN ( SELECT PREFIJO FROM " . strtoupper(PFX_MAIN_DB) . "TBL_APP_PREAUTORIZADOR ) AND DIA = :dia GROUP BY DIA "
				. " ) GROUP BY DIA "; 
		*/
		$query = " SELECT h.DIA, SUM(h.TOTAL) AS TOTAL FROM ( "
					. " SELECT a.DIA, SUM(a.TOTAL) AS TOTAL FROM " . PFX_SRV_DB . "TBL_MON_BIN_POS_NAC AS a "  
					. " WHERE a.BIN IN ( SELECT b.PREFIJO FROM " . PFX_MAIN_DB . "tbl_app_preautorizador AS b) AND a.DIA = :dia GROUP BY a.DIA "
					. " UNION "
					. " SELECT c.DIA, SUM(c.TOTAL) AS TOTAL FROM " . PFX_SRV_DB . "TBL_MON_BIN_POS_INT AS c "  
					. " WHERE c.BIN IN ( SELECT PREFIJO FROM " . PFX_MAIN_DB . "tbl_app_preautorizador AS d) AND c.DIA = :dia GROUP BY c.DIA "
					. " UNION "
					. " SELECT d.DIA, SUM(d.TOTAL) AS TOTAL FROM " . PFX_SRV_DB . "TBL_MON_BIN_ATM_NAC AS d "  
					. " WHERE d.BIN IN ( SELECT e.PREFIJO FROM " . PFX_MAIN_DB . "tbl_app_preautorizador AS e) AND DIA = :dia GROUP BY d.DIA "
					. " UNION "
					. " SELECT f.DIA, SUM(f.TOTAL) AS TOTAL FROM " . PFX_SRV_DB . "TBL_MON_BIN_ATM_INT AS f"  
					. " WHERE f.BIN IN ( SELECT g.PREFIJO FROM " . PFX_MAIN_DB . "tbl_app_preautorizador AS g) AND f.DIA = :dia GROUP BY f.DIA "
				. " ) AS h GROUP BY h.DIA "; 
		//echo $query ;
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
			$value = $this->indicators;
			//$resp = $this->insert_info_charts($value);
			$resp = $this->insert_old_last_total($id_service=2, $value);
		
	}
	
	private function set_service_totals(){
		 global $obj_bd;
		/* ORACLE
		$query0 =  " SELECT DIA, SUM(ACCEPTED) AS ACCEPTED, SUM(REJECTED) AS REJECTED, SUM(TOTAL) AS TOTAL "
					. "  FROM ( "
						. " SELECT DIA, SUM(TOTAL) AS TOTAL, " 
							. " SUM(CASE WHEN CODIGO_RESPUESTA < 11 THEN TOTAL ELSE 0 END ) AS ACCEPTED, "
							. " SUM(CASE WHEN CODIGO_RESPUESTA > 10 THEN TOTAL ELSE 0 END ) AS REJECTED " 
						. " FROM " . PFX_SRV_DB . "TBL_MON_BIN_POS_NAC_" . date('Ym') . " "
						. " WHERE BIN IN ( SELECT PREFIJO FROM " . strtoupper(PFX_MAIN_DB) . "TBL_APP_PREAUTORIZADOR " .  (( $this->id_client > 0 ) ? " WHERE FIID_TARJ = :id_client " : '') . ") AND DIA = :dia " 
		 				. (( $this->id_client > 0 ) ? " AND FIID_TARJ = :id_client " : '')
						. " GROUP BY DIA "
						. " UNION  "
						. " SELECT DIA, SUM(TOTAL) AS TOTAL, " 
							. " SUM(CASE WHEN CODIGO_RESPUESTA < 11 THEN TOTAL ELSE 0 END ) AS ACCEPTED, "
							. " SUM(CASE WHEN CODIGO_RESPUESTA > 10 THEN TOTAL ELSE 0 END ) AS REJECTED " 
						. " FROM " . PFX_SRV_DB . "TBL_MON_BIN_POS_INT_" . date('Ym') . " "
						. " WHERE BIN IN ( SELECT PREFIJO FROM " . strtoupper(PFX_MAIN_DB) . "TBL_APP_PREAUTORIZADOR " .  (( $this->id_client > 0 ) ? " WHERE FIID_TARJ = :id_client " : '') . ") AND DIA = :dia "
		 				. (( $this->id_client > 0 ) ? " AND FIID_TARJ = :id_client " : '')
						. " GROUP BY DIA "
					. " ) GROUP BY DIA ";
		*/
		
		$query0 =  " SELECT a.DIA, SUM(a.ACCEPTED) AS ACCEPTED, SUM(a.REJECTED) AS REJECTED, SUM(a.TOTAL) AS TOTAL "
					. "  FROM ( "
						. " SELECT b.DIA, SUM(b.TOTAL) AS TOTAL, " 
							. " SUM(CASE WHEN b.CODIGO_RESPUESTA < 11 THEN b.TOTAL ELSE 0 END ) AS ACCEPTED, "
							. " SUM(CASE WHEN b.CODIGO_RESPUESTA > 10 THEN b.TOTAL ELSE 0 END ) AS REJECTED " 
						. " FROM " . PFX_SRV_DB . "TBL_MON_BIN_POS_NAC AS b"
						. " WHERE b.BIN IN ( SELECT c.PREFIJO FROM " . PFX_MAIN_DB . "tbl_app_preautorizador AS c" .  (( $this->id_client > 0 ) ? " WHERE FIID_TARJ = :id_client " : '') . ") AND b.DIA = :dia " 
		 				. (( $this->id_client > 0 ) ? " AND FIID_TARJ = :id_client " : '')
						. " GROUP BY b.DIA "
						. " UNION  "
						. " SELECT d.DIA, SUM(d.TOTAL) AS TOTAL, " 
							. " SUM(CASE WHEN d.CODIGO_RESPUESTA < 11 THEN d.TOTAL ELSE 0 END ) AS ACCEPTED, "
							. " SUM(CASE WHEN d.CODIGO_RESPUESTA > 10 THEN d.TOTAL ELSE 0 END ) AS REJECTED " 
						. " FROM " . PFX_SRV_DB . "TBL_MON_BIN_POS_INT AS d "
						. " WHERE d.BIN IN ( SELECT e.PREFIJO FROM " . PFX_MAIN_DB . "tbl_app_preautorizador AS e " .  (( $this->id_client > 0 ) ? " WHERE FIID_TARJ = :id_client " : '') . ") AND d.DIA = :dia "
		 				. (( $this->id_client > 0 ) ? " AND FIID_TARJ = :id_client " : '')
						. " GROUP BY d.DIA "
					. " ) AS a GROUP BY a.DIA ";
				//echo $query0;	
		/* ORACLE
		 $query1 =   " SELECT DIA, SUM(ACCEPTED) AS ACCEPTED, SUM(REJECTED) AS REJECTED, SUM(TOTAL) AS TOTAL "
					. "  FROM ( "
						. " SELECT DIA, SUM(TOTAL) AS TOTAL, " 
							. " SUM(CASE WHEN CODIGO_RESPUESTA < 11 THEN TOTAL ELSE 0 END ) AS ACCEPTED, "
							. " SUM(CASE WHEN CODIGO_RESPUESTA > 10 THEN TOTAL ELSE 0 END ) AS REJECTED " 
						. " FROM " . PFX_SRV_DB . "TBL_MON_BIN_ATM_NAC_" . date('Ym') . " "
						. " WHERE BIN IN ( SELECT PREFIJO FROM " . strtoupper(PFX_MAIN_DB) . "TBL_APP_PREAUTORIZADOR " .  (( $this->id_client > 0 ) ? " WHERE FIID_TARJ = :id_client " : '') . ") AND DIA = :dia "
		 				. (( $this->id_client > 0 ) ? " AND FIID_TARJ = :id_client " : '')
						. " GROUP BY DIA "
						. " UNION  "
						. " SELECT DIA, SUM(TOTAL) AS TOTAL, " 
							. " SUM(CASE WHEN CODIGO_RESPUESTA < 11 THEN TOTAL ELSE 0 END ) AS ACCEPTED, "
							. " SUM(CASE WHEN CODIGO_RESPUESTA > 10 THEN TOTAL ELSE 0 END ) AS REJECTED " 
						. " FROM " . PFX_SRV_DB . "TBL_MON_BIN_ATM_INT_" . date('Ym') . " "
						. " WHERE BIN IN ( SELECT PREFIJO FROM " . strtoupper(PFX_MAIN_DB) . "TBL_APP_PREAUTORIZADOR " .  (( $this->id_client > 0 ) ? " WHERE FIID_TARJ = :id_client " : '') . ") AND DIA = :dia "
		 				. (( $this->id_client > 0 ) ? " AND FIID_TARJ = :id_client " : '')
						. " GROUP BY DIA "
					. " ) GROUP BY DIA "; 
		 * */
		 		
		$query1 =   " SELECT a.DIA, SUM(a.ACCEPTED) AS ACCEPTED, SUM(a.REJECTED) AS REJECTED, SUM(a.TOTAL) AS TOTAL "
					. "  FROM ( "
						. " SELECT b.DIA, SUM(b.TOTAL) AS TOTAL, " 
							. " SUM(CASE WHEN b.CODIGO_RESPUESTA < 11 THEN b.TOTAL ELSE 0 END ) AS ACCEPTED, "
							. " SUM(CASE WHEN b.CODIGO_RESPUESTA > 10 THEN b.TOTAL ELSE 0 END ) AS REJECTED " 
						. " FROM " . PFX_SRV_DB . "TBL_MON_BIN_ATM_NAC AS b"
						. " WHERE b.BIN IN ( SELECT c.PREFIJO FROM " . PFX_MAIN_DB . "tbl_app_preautorizador " .  (( $this->id_client > 0 ) ? " WHERE FIID_TARJ = :id_client " : '') . " AS c) AND b.DIA = :dia "
		 				. (( $this->id_client > 0 ) ? " AND FIID_TARJ = :id_client " : '')
						. " GROUP BY b.DIA "
						. " UNION  "
						. " SELECT d.DIA, SUM(d.TOTAL) AS TOTAL, " 
							. " SUM(CASE WHEN d.CODIGO_RESPUESTA < 11 THEN d.TOTAL ELSE 0 END ) AS ACCEPTED, "
							. " SUM(CASE WHEN d.CODIGO_RESPUESTA > 10 THEN d.TOTAL ELSE 0 END ) AS REJECTED " 
						. " FROM " . PFX_SRV_DB . "TBL_MON_BIN_ATM_INT AS d"
						. " WHERE BIN IN ( SELECT e.PREFIJO FROM " . PFX_MAIN_DB . "tbl_app_preautorizador " .  (( $this->id_client > 0 ) ? " WHERE FIID_TARJ = :id_client " : '') . " AS e) AND d.DIA = :dia "
		 				. (( $this->id_client > 0 ) ? " AND FIID_TARJ = :id_client " : '')
						. " GROUP BY d.DIA "
					. " ) AS a GROUP BY a.DIA ";
		//echo  $query1;
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
				$this->set_error(" No se obtunvieron valores del servicio POS " . ( $this->client_code != '' ? "(" . $this->client_code . ")" : "" ) . ".", ERR_DB_QRY ); 
			}
			
			if ( count($result1) > 0 ){
				
				$totals = $result1[0];
				
				$this->indicators[1]['total_transactions'] = $totals['TOTAL'];
				$this->indicators[1]['total_accepted'] = $totals['ACCEPTED'];
				$this->indicators[1]['total_rejected'] = $totals['REJECTED'];
				
				$t1 = $totals['TOTAL']; 
			} else { 
				$this->set_error(" No se obtunvieron valores del servicio ATM " . ( $this->client_code != '' ? "(" . $this->client_code . ")" : "" ) . ".", ERR_DB_QRY ); 
			}
			//$this->set_last_total( $t0 + $t1 ); 
		} else {
			$this->set_error(" Ocurrió un error al obtener los totales del servicio.", ERR_DB_QRY );
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
		/* ORACLE
		 * $query =  " SELECT CODIGO_RESPUESTA, SUM(TOTAL) AS TOTAL FROM ( "
					  . " SELECT SUM(TOTAL) AS TOTAL, CODIGO_RESPUESTA FROM " . PFX_SRV_DB . "TBL_MON_BIN_" . $srv . "_NAC_" . date('Ym') 
						. " WHERE BIN IN ( SELECT PREFIJO FROM " . strtoupper(PFX_MAIN_DB) . "TBL_APP_PREAUTORIZADOR " .  (( $this->id_client > 0 ) ? " WHERE FIID_TARJ = :id_client " : '') . ") AND DIA = :dia "
							. " AND CODIGO_RESPUESTA > 10 "
		 					. (( $this->id_client > 0 ) ? " AND FIID_TARJ = :id_client " : '')
			 			. " GROUP BY CODIGO_RESPUESTA "
			 		. " UNION "
			 		. " SELECT SUM(TOTAL) AS TOTAL, CODIGO_RESPUESTA FROM " . PFX_SRV_DB . "TBL_MON_BIN_" . $srv . "_INT_" . date('Ym') 
			 			. " WHERE BIN IN ( SELECT PREFIJO FROM " . strtoupper(PFX_MAIN_DB) . "TBL_APP_PREAUTORIZADOR " .  (( $this->id_client > 0 ) ? " WHERE FIID_TARJ = :id_client " : '') . ") AND DIA = :dia "
			 			. " AND CODIGO_RESPUESTA > 10 "
		 					. (( $this->id_client > 0 ) ? " AND FIID_TARJ = :id_client " : '')
			 			. " GROUP BY CODIGO_RESPUESTA "
			 		. " ORDER BY CODIGO_RESPUESTA "
				. " ) GROUP BY CODIGO_RESPUESTA ORDER BY TOTAL DESC ";*/
		
		$query =  " SELECT a.CODIGO_RESPUESTA, SUM(a.TOTAL) AS TOTAL FROM ( "
					  . " SELECT SUM(b.TOTAL) AS TOTAL, b.CODIGO_RESPUESTA FROM " . PFX_SRV_DB . "TBL_MON_BIN_" . $srv . "_NAC" 
						. " AS b WHERE b.BIN IN ( SELECT c.PREFIJO FROM " . PFX_MAIN_DB . "tbl_app_preautorizador " .  (( $this->id_client > 0 ) ? " WHERE FIID_TARJ = :id_client " : '') . "AS c) AND b.DIA = :dia "
							. " AND b.CODIGO_RESPUESTA > 10 "
		 					. (( $this->id_client > 0 ) ? " AND FIID_TARJ = :id_client " : '')
			 			. " GROUP BY b.CODIGO_RESPUESTA "
			 		. " UNION "
			 		. " SELECT SUM(d.TOTAL) AS TOTAL, d.CODIGO_RESPUESTA FROM " . PFX_SRV_DB . "TBL_MON_BIN_" . $srv . "_INT" 
			 			. " AS d WHERE d.BIN IN ( SELECT e.PREFIJO FROM " . PFX_MAIN_DB . "tbl_app_preautorizador " .  (( $this->id_client > 0 ) ? " WHERE FIID_TARJ = :id_client " : '') . " AS e) AND d.DIA = :dia "
			 			. " AND CODIGO_RESPUESTA > 10 "
		 					. (( $this->id_client > 0 ) ? " AND FIID_TARJ = :id_client " : '')
			 			. " GROUP BY d.CODIGO_RESPUESTA "
			 		. " ORDER BY CODIGO_RESPUESTA "
				. " ) AS a GROUP BY a.CODIGO_RESPUESTA ORDER BY a.TOTAL DESC ";
		/* ORACLE
		$query_top = ' SELECT CODIGO_RESPUESTA, TOTAL FROM ( ' . $query . ' ) WHERE rownum <= 5 ' ;*/
		$query_top = ' SELECT f.CODIGO_RESPUESTA, f.TOTAL FROM ( ' . $query . ' ) AS f LIMIT 0,5  ' ;
	
		
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
				$others = array();
				$others['code']		= 0;
				$others['motive']	= 'Otros';
				$others['total']	= $this->indicators[$idx]['total_rejected'] ;
				
				$this->indicators[$idx]['top_rejected'][] = $others;
				$this->set_error("No se obtunvieron valores del servicio para top rechazados $srv " . ( $this->client_code != '' ? "(" . $this->client_code . ")" : "" ) . ".", ERR_DB_QRY );
				return FALSE;
			}
		} else {
			$this->set_error("Ocurrió un error al obtener los totales del servicio para top rechazados $srv " . ( $this->client_code != '' ? "(" . $this->client_code . ")" : "" ) . ".", ERR_DB_QRY );
			return FALSE;
		} 
	}

	 public function is_up(){

                $this->last_total = $this->get_last_total();
                if ( $this->last_total ){

                        //if ( $this->last_total['timestamp'] > time() - ( $this->time_prosa * 60 ) )
                              /*  return TRUE;

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
*/
                        //$day_total = $this->get_day_total( date('d', $when) );
                        //if ( $day_total ){
                                if ( /*$day_total > $this->last_total['total']*/ $this->last_total['total'] > $this->last_total['pre_total']){
                                        //$this->set_last_total( $day_total );
                                        return TRUE;
                                } else{
                                        //$this->set_last_total( $day_total );
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