<?php
if ( !class_exists('Service')){
	require_once DIRECTORY_CLASS . 'class.service.php';
}
/**
 * POS Class 
 * 
 * "Servicio tradicional.
 * Flujo transaccional de todos los dispositivos/comercios y tarjetas 
 * de Clientes PROSA del tipo POS FRONT END y  BACK END (Host y BIC) 
 * pudiendo ser nacional ó internacional"
 * 
 */
class AdminPOS extends Service {
	
	function __construct( $id_client = 0) {
		
		parent::__construct();
		
		$this->class 	= "AdminPOS";
		$this->service 	= "POS";
		$this->code 	= "s7";
		
		$this->description = "&Eacute;sta consulta no incluye transacciones de Cargos Autom&aacute;ticos";

		$this->id_service = 7;
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
		//de Oracle
		/*$query = " SELECT DIA, SUM(TOTAL) AS TOTAL FROM ( "
					. " SELECT DIA, SUM(TOTAL) AS TOTAL FROM " . PFX_SRV_DB . "TBL_MON_HORA_POS_NAC_" . date('Ym') . " "  
					. " WHERE DIA = :dia AND NOT KQ2_ID_MEDIO_ACCESO = '02' GROUP BY DIA "
					. " UNION "
					. " SELECT DIA, SUM(TOTAL) AS TOTAL FROM " . PFX_SRV_DB . "TBL_MON_HORA_POS_INT_" . date('Ym') . " "  
					. " WHERE DIA = :dia AND NOT KQ2_ID_MEDIO_ACCESO = '02' GROUP BY DIA " 
				. " ) GROUP BY DIA "; 
			*/	
		//de mysql
		$query = " SELECT z.DIA, SUM(z.TOTAL) AS TOTAL FROM ( "
					. " SELECT x.DIA, SUM(x.TOTAL) AS TOTAL FROM " . PFX_SRV_DB . "TBL_MON_HORA_POS_NAC AS x "  
					. " WHERE x.DIA = :dia AND NOT x.KQ2_ID_MEDIO_ACCESO = '02' GROUP BY x.DIA "
					. " UNION "
					. " SELECT y.DIA, SUM(y.TOTAL) AS TOTAL FROM " . PFX_SRV_DB . "TBL_MON_HORA_POS_INT AS y "  
					. " WHERE y.DIA = :dia AND NOT y.KQ2_ID_MEDIO_ACCESO = '02' GROUP BY y.DIA " 
				. " ) AS z GROUP BY z.DIA ";
				
		//$result = $this->db->query( $query, array( ':dia' => $day ) ); //oracle
		$result = $obj_bd->query( $query, array( ':dia' => $day ) ); // solo my sql
	//echo $query;
		if ( $result !== FALSE ){
			return count( $result[0] ) > 0 ? $result[0]['TOTAL'] : 0;
		} else {
			$this->set_error("Ocurrió un error al obtener la última actualización.", ERR_DB_QRY );
			return FALSE;
		}
	}
	
	
	public function load_service(){ 
		$this->state = $this->is_up();
		 
		$this->indicators[0]['name'] = "POS";
		$this->indicators[0]['title'] = "Emisor";
		$this->indicators[0]['source'] = "Emisor";
		
		$this->indicators[0]['total_transactions'] = 0;
		$this->indicators[0]['total_accepted'] = 0;
		$this->indicators[0]['total_rejected'] = 0;
		
		$this->indicators[1]['name'] = "POS";
		$this->indicators[1]['title'] = "Adquirente"; 
		$this->indicators[1]['source'] = "Adquirente"; 
		
		$this->indicators[1]['total_transactions'] = 0;
		$this->indicators[1]['total_accepted'] = 0;
		$this->indicators[1]['total_rejected'] = 0;

			$resp = $this->set_service_totals();
			$resp = $this->set_top_rejected();
			$value = $this->indicators;
			//$resp = $this->insert_info_charts($value);
			$resp = $this->insert_old_last_total($id_service=7, $value);

	}
	
	
	
	private function set_service_totals(){
		 global $obj_bd;
		/* DE oracle
		 * $query0 =  " SELECT DIA, SUM(ACCEPTED) AS ACCEPTED, SUM(REJECTED) AS REJECTED, SUM(TOTAL) AS TOTAL "
					. "  FROM ( "
						. " SELECT DIA, SUM(TOTAL) AS TOTAL, " 
							. " SUM(CASE WHEN CODIGO_RESPUESTA < 11 THEN TOTAL ELSE 0 END ) AS ACCEPTED, "
							. " SUM(CASE WHEN CODIGO_RESPUESTA > 10 THEN TOTAL ELSE 0 END ) AS REJECTED " 
						. " FROM " . PFX_SRV_DB . "TBL_MON_HORA_POS_NAC_" . date('Ym') . " "
						. " WHERE DIA = :dia AND NOT KQ2_ID_MEDIO_ACCESO = '02' "  
							. (( $this->id_client > 0 ) ? " AND FIID_TARJ = :id_client " : '')
						. " GROUP BY DIA "
						. " UNION  "
						. " SELECT DIA, SUM(TOTAL) AS TOTAL, " 
							. " SUM(CASE WHEN CODIGO_RESPUESTA < 11 THEN TOTAL ELSE 0 END ) AS ACCEPTED, "
							. " SUM(CASE WHEN CODIGO_RESPUESTA > 10 THEN TOTAL ELSE 0 END ) AS REJECTED " 
						. " FROM " . PFX_SRV_DB . "TBL_MON_HORA_POS_INT_" . date('Ym') . " "
						. " WHERE DIA = :dia AND NOT KQ2_ID_MEDIO_ACCESO = '02' "  
							. (( $this->id_client > 0 ) ? " AND FIID_TARJ = :id_client " : '')
						. " GROUP BY DIA "
					. " ) GROUP BY DIA ";
		 * 
		 * 
		 */
		 $query0 =  " SELECT z.DIA, SUM(ACCEPTED) AS ACCEPTED, SUM(REJECTED) AS REJECTED, SUM(TOTAL) AS TOTAL "
					. "  FROM ( "
						. " SELECT x.DIA, SUM(x.TOTAL) AS TOTAL, " 
							. " SUM(CASE WHEN x.CODIGO_RESPUESTA < 11 THEN x.TOTAL ELSE 0 END ) AS ACCEPTED, "
							. " SUM(CASE WHEN x.CODIGO_RESPUESTA > 10 THEN x.TOTAL ELSE 0 END ) AS REJECTED " 
						. " FROM " . PFX_SRV_DB . "TBL_MON_HORA_POS_NAC AS x"
						. " WHERE DIA = :dia AND NOT x.KQ2_ID_MEDIO_ACCESO = '02' "  
							. (( $this->id_client > 0 ) ? " AND FIID_TARJ = :id_client " : '')
						. " GROUP BY x.DIA "
						. " UNION  "
						. " SELECT y.DIA, SUM(y.TOTAL) AS TOTAL, " 
							. " SUM(CASE WHEN y.CODIGO_RESPUESTA < 11 THEN y.TOTAL ELSE 0 END ) AS ACCEPTED, "
							. " SUM(CASE WHEN y.CODIGO_RESPUESTA > 10 THEN y.TOTAL ELSE 0 END ) AS REJECTED " 
						. " FROM " . PFX_SRV_DB . "TBL_MON_HORA_POS_INT AS y "
						. " WHERE y.DIA = :dia AND NOT y.KQ2_ID_MEDIO_ACCESO = '02' "  
							. (( $this->id_client > 0 ) ? " AND y.FIID_TARJ = :id_client " : '')
						. " GROUP BY y.DIA "
					. " ) as z GROUP BY z.DIA ";
		
		//Oracle
		/*$query1 =  " SELECT DIA, SUM(ACCEPTED) AS ACCEPTED, SUM(REJECTED) AS REJECTED, SUM(TOTAL) AS TOTAL "
					. "  FROM ( "
						. " SELECT DIA, SUM(TOTAL) AS TOTAL, " 
							. " SUM(CASE WHEN CODIGO_RESPUESTA < 11 THEN TOTAL ELSE 0 END ) AS ACCEPTED, "
							. " SUM(CASE WHEN CODIGO_RESPUESTA > 10 THEN TOTAL ELSE 0 END ) AS REJECTED " 
						. " FROM " . PFX_SRV_DB . "TBL_MON_HORA_POS_NAC_" . date('Ym') . " "
						. " WHERE DIA = :dia  AND NOT KQ2_ID_MEDIO_ACCESO = '02' " 
							. (( $this->id_client > 0 ) ? " AND FIID_COMER = :id_client " : '')
						. " GROUP BY DIA "
						. " UNION  "
						. " SELECT DIA, SUM(TOTAL) AS TOTAL, " 
							. " SUM(CASE WHEN CODIGO_RESPUESTA < 11 THEN TOTAL ELSE 0 END ) AS ACCEPTED, "
							. " SUM(CASE WHEN CODIGO_RESPUESTA > 10 THEN TOTAL ELSE 0 END ) AS REJECTED " 
						. " FROM " . PFX_SRV_DB . "TBL_MON_HORA_POS_INT_" . date('Ym') . " "
						. " WHERE DIA = :dia AND NOT KQ2_ID_MEDIO_ACCESO = '02' "  
							. (( $this->id_client > 0 ) ? " AND FIID_COMER = :id_client " : '')
						. " GROUP BY DIA "
					. " ) GROUP BY DIA ";
		 * 
		 */
		 $query1 =  " SELECT z.DIA, SUM(z.ACCEPTED) AS ACCEPTED, SUM(z.REJECTED) AS REJECTED, SUM(z.TOTAL) AS TOTAL "
					. "  FROM ( "
						. " SELECT DIA, SUM(x.TOTAL) AS TOTAL, " 
							. " SUM(CASE WHEN x.CODIGO_RESPUESTA < 11 THEN x.TOTAL ELSE 0 END ) AS ACCEPTED, "
							. " SUM(CASE WHEN x.CODIGO_RESPUESTA > 10 THEN x.TOTAL ELSE 0 END ) AS REJECTED " 
						. " FROM " . PFX_SRV_DB . "TBL_MON_HORA_POS_NAC AS x"
						. " WHERE x.DIA = :dia  AND NOT x.KQ2_ID_MEDIO_ACCESO = '02' " 
							. (( $this->id_client > 0 ) ? " AND FIID_COMER = :id_client " : '')
						. " GROUP BY x.DIA "
						. " UNION  "
						. " SELECT y.DIA, SUM(y.TOTAL) AS TOTAL, " 
							. " SUM(CASE WHEN y.CODIGO_RESPUESTA < 11 THEN y.TOTAL ELSE 0 END ) AS ACCEPTED, "
							. " SUM(CASE WHEN y.CODIGO_RESPUESTA > 10 THEN y.TOTAL ELSE 0 END ) AS REJECTED " 
						. " FROM " . PFX_SRV_DB . "TBL_MON_HORA_POS_INT AS y "
						. " WHERE y.DIA = :dia AND NOT y.KQ2_ID_MEDIO_ACCESO = '02' "  
							. (( $this->id_client > 0 ) ? " AND y.FIID_COMER = :id_client " : '')
						. " GROUP BY y.DIA "
					. " ) AS z GROUP BY z.DIA ";
		//echo $query1;
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
				$this->set_error(" No se obtuvieron valores del servicio ATM " . ( $this->client_code != '' ? "(" . $this->client_code . ")" : "" ) . ".", ERR_DB_QRY ); 
			}
			//$this->set_last_total( $t0 + $t1 ); 
		} else {
			$this->set_error(" Ocurrió un error al obtener los totales del servicio " . ( $this->client_code != '' ? "(" . $this->client_code . ")" : "" ) . ".", ERR_DB_QRY );
			return FALSE;
		}
	}
	
	private function set_top_rejected(){
		$this->set_top_rejected_emiadq( 'EMI', 0);
		$this->set_top_rejected_emiadq( 'ADQ', 1);
	} 
	
	private function set_top_rejected_emiadq( $type, $idx ){
		global $obj_bd;
		$fiid = ( $type == 'EMI' ) ? 'FIID_TARJ' : 'FIID_COMER';
		$this->indicators[$idx]['top_rejected'] = array(); 
		
		$query =  " SELECT z.CODIGO_RESPUESTA, SUM(z.TOTAL) AS TOTAL FROM ( "
					. " SELECT x.CODIGO_RESPUESTA, SUM(x.TOTAL) AS TOTAL FROM " . PFX_SRV_DB . "TBL_MON_HORA_POS_NAC" 
						. " AS x WHERE x.CODIGO_RESPUESTA > 10 AND DIA = :dia AND NOT x.KQ2_ID_MEDIO_ACCESO = '02' "
		 					. (( $this->id_client > 0 ) ? " AND $fiid = :id_client " : '')
			 			. " GROUP BY x.CODIGO_RESPUESTA "
			 		. " UNION "
			 		. " SELECT y.CODIGO_RESPUESTA, SUM(y.TOTAL) AS TOTAL FROM " . PFX_SRV_DB . "TBL_MON_HORA_POS_INT" 
			 			. " AS y WHERE y.CODIGO_RESPUESTA > 10 AND DIA = :dia AND NOT y.KQ2_ID_MEDIO_ACCESO = '02'  "
		 					. (( $this->id_client > 0 ) ? " AND $fiid = :id_client " : '')
			 			. " GROUP BY y.CODIGO_RESPUESTA "		
				. " ) AS z GROUP BY z.CODIGO_RESPUESTA ORDER BY z.TOTAL DESC "; 
				

		
		//$query_top = 'SELECT CODIGO_RESPUESTA, TOTAL FROM ( ' . $query . ' ) WHERE rownum <= 5 '; //oracle
		$query_top = 'SELECT h.CODIGO_RESPUESTA, h.TOTAL FROM ( ' . $query . ' ) AS h LIMIT 0,5';
		
		$result = $obj_bd->query( $query_top,   array( ":dia" => date('d'), ":id_client" => $this->client_code ) );
		
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
				$this->set_error("No se obtunvieron valores del servicio para top rechazados $type " . ( $this->client_code != '' ? "(" . $this->client_code . ")" : "" ) . "." , ERR_DB_QRY );
				return FALSE;
			}
		} else {
			$this->set_error("Ocurrió un error al obtener los totales del servicio para top rechazados $type " . ( $this->client_code != '' ? "(" . $this->client_code . ")" : "" ) . ".", ERR_DB_QRY );
			return FALSE;
		} 
	}
	 public function is_up(){
 		
        $this->last_total = $this->get_last_total(); 
		
        if ( $this->last_total ){ 
                //if ( $this->last_total['timestamp'] > time() - ( $this->time_prosa * 60 ) )
                   /*     return TRUE;

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
                }*/


                //$day_total = $this->get_day_total( date('d', $when) );
                //if ( $day_total ){
                    if ( /*$day_total > $this->last_total['total']*/ $this->last_total['total'] > $this->last_total['pre_total'] ){
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
