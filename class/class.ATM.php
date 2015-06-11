<?php
if ( !class_exists('Service')){
	require_once DIRECTORY_CLASS . 'class.service.php';
}
/**
 * ATM Class 
 * 
 * "Servicio tradicional.
 * Flujo transaccional de todos los dispositivos/comercios y tarjetas 
 * de Clientes PROSA del tipo ATM FRONT END y  BACK END (Host y BIC) 
 * pudiendo ser nacional ó internacional"
 * 
 */
class ATM extends Service {
	
	function __construct( $id_client = 0) {
		
		parent::__construct();
		
		$this->class 	= "ATM";
		$this->service 	= "ATM";
		$this->code 	= "s8";
		
		$this->id_service = 8;
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
					. " SELECT DIA, SUM(TOTAL) AS TOTAL FROM " . PFX_SRV_DB . "TBL_MON_HORA_ATM_NAC_" . date('Ym') . " "  
					. " WHERE DIA = :dia GROUP BY DIA "
					. " UNION "
					. " SELECT DIA, SUM(TOTAL) AS TOTAL FROM " . PFX_SRV_DB . "TBL_MON_HORA_ATM_INT_" . date('Ym') . " "  
					. " WHERE DIA = :dia GROUP BY DIA " 
				. " ) GROUP BY DIA "; 
		if ( $result !== FALSE ){
			return count( $result[0] ) > 0 ? $result[0]['TOTAL'] : 0;
		} else {
			$this->set_error("Ocurrió un error al obtener la última actualización.", ERR_DB_QRY );
			return FALSE;
		}
	}
	
	
	public function load_service(){
		$this->state = $this->is_up();  
		 
		$this->indicators[0]['name'] = "ATM";
		$this->indicators[0]['title'] = "Emisor";
		$this->indicators[0]['source'] = "Emisor";
		
		$this->indicators[0]['total_transactions'] = 0;
		$this->indicators[0]['total_accepted'] = 0;
		$this->indicators[0]['total_rejected'] = 0;
		
		$this->indicators[1]['name'] = "ATM";
		$this->indicators[1]['title'] = "Adquirente";
		$this->indicators[1]['source'] = "Adquirente"; 
		
		$this->indicators[1]['total_transactions'] = 0;
		$this->indicators[1]['total_accepted'] = 0;
		$this->indicators[1]['total_rejected'] = 0;
		
		$resp = $this->set_sellcom_service_totals(); 
		//$resp = $this->set_top_rejected();
		
	}
	
	private function set_sellcom_service_totals()
	{
		global $obj_bd;
		$arreglo_pos = array();
		$arreglo_atm = array();

		$this->indicators[0]['total_transactions'] = 0;
		$this->indicators[0]['total_accepted'] = 0;
		$this->indicators[0]['total_rejected'] = 0;

		$this->indicators[1]['total_transactions'] = 0;
		$this->indicators[1]['total_accepted'] = 0;
		$this->indicators[1]['total_rejected'] = 0;


		$query = "SELECT MAX(idpra_charts) AS id FROM " . PFX_MAIN_DB . "charts WHERE pcs_type='emisor_atm' AND pcs_se_id_service=8";
		$query2 = "SELECT MAX(idpra_charts) AS id FROM " . PFX_MAIN_DB . "charts WHERE pcs_type='adquirente_atm' AND pcs_se_id_service=8";


		$result = $obj_bd->query($query);
		$result2 = $obj_bd->query($query2);

		if($result !== FALSE && $result2 !== FALSE)
		{
			if (count($result) > 0 && count($result2) > 0) 
			{
				$total = $result[0];
				$total2 = $result2[0];

				$this->id_service = $total['id'];
				$this->id_service2 = $total2['id'];

				$query = " SELECT * FROM " . PFX_MAIN_DB . "charts WHERE idpra_charts=" . $this->id_service;

				$query2 = " SELECT * FROM " . PFX_MAIN_DB . "charts WHERE idpra_charts=" . $this->id_service2;


				$result = $obj_bd->query($query);
				$result2 = $obj_bd->query($query2);

				if ($result !== FALSE && $result2 !== FALSE) 
				{
					if (count($result) > 0 && count($result2) > 0) 
					{
						$total = $result[0];
						$total2 = $result2[0];

						$this->indicators[0]['total_transactions'] = $total['pcs_total_acepted'] + $total['pcs_total_rejected'];
						$this->indicators[0]['total_accepted'] = $total['pcs_total_acepted'];
						$this->indicators[0]['total_rejected'] = $total['pcs_total_rejected'];

						$this->indicators[1]['total_transactions'] = $total2['pcs_total_acepted'] + $total2['pcs_total_rejected'];
						$this->indicators[1]['total_accepted'] = $total2['pcs_total_acepted'];
						$this->indicators[1]['total_rejected'] = $total2['pcs_total_rejected'];

						$datos = $total['pcs_top_5_rejected'];
						$datos2 = $total2['pcs_top_5_rejected'];

						if (count($datos) > 0 && count($datos2) > 0) 
						{
							$t0 = 0;
							$t1 = 0;

							$arreglo_pos = explode(",", $datos);
							$arreglo_atm = explode(",", $datos2);

							for ($i=0 ; $i < 11 ; $i+= 3 ) 
							{ 
								$rejected =  array();
								$rejected['code'] = $arreglo_pos[$i];
								$rejected['motive'] = $arreglo_pos[$i+1];
								$rejected['total'] = $arreglo_pos[$i+2];

								$t0 += $rejected['total'];

								$this->indicators[0]['top_rejected'][] = $rejected;

								$rejected =  array();
								$rejected['code'] = $arreglo_atm[$i];
								$rejected['motive'] = $arreglo_atm[$i+1];
								$rejected['total'] = $arreglo_atm[$i+2];

								$t1 += $rejected['total'];

								$this->indicators[1]['top_rejected'][] = $rejected;
							}

							$others = array();
							$others['code'] = 0;
							$others['motive'] = 'Otros';
							$others['total'] = $this->indicators[0]['total_rejected'] - $t0;
							$this->indicators[0]['top_rejected'][] = $others;

							$others = array();
							$others['code'] = 0;
							$others['motive'] = 'Otros';
							$others['total'] = $this->indicators[1]['total_rejected'] - $t1;
							 $this->indicators[1]['top_rejected'][] = $others;
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
		 /* ORACLE
		$query0 =  " SELECT DIA, SUM(ACCEPTED) AS ACCEPTED, SUM(REJECTED) AS REJECTED, SUM(TOTAL) AS TOTAL "
					. "  FROM ( "
						. " SELECT DIA, SUM(TOTAL) AS TOTAL, " 
							. " SUM(CASE WHEN CODIGO_RESPUESTA < 11 THEN TOTAL ELSE 0 END ) AS ACCEPTED, "
							. " SUM(CASE WHEN CODIGO_RESPUESTA > 10 THEN TOTAL ELSE 0 END ) AS REJECTED " 
						. " FROM " . PFX_SRV_DB . "TBL_MON_HORA_ATM_NAC_" . date('Ym') . " "
						. " WHERE DIA = :dia "  . (( $this->id_client > 0 ) ? " AND FIID_TARJ = :id_client " : '')
						. " GROUP BY DIA "
						. " UNION  "
						. " SELECT DIA, SUM(TOTAL) AS TOTAL, " 
							. " SUM(CASE WHEN CODIGO_RESPUESTA < 11 THEN TOTAL ELSE 0 END ) AS ACCEPTED, "
							. " SUM(CASE WHEN CODIGO_RESPUESTA > 10 THEN TOTAL ELSE 0 END ) AS REJECTED " 
						. " FROM " . PFX_SRV_DB . "TBL_MON_HORA_ATM_INT_" . date('Ym') . " "
						. " WHERE DIA = :dia "  . (( $this->id_client > 0 ) ? " AND FIID_TARJ = :id_client " : '')
						. " GROUP BY DIA "
					. " ) GROUP BY DIA ";
		*/
		$query0 =  " SELECT a.DIA, SUM(a.ACCEPTED) AS ACCEPTED, SUM(a.REJECTED) AS REJECTED, SUM(a.TOTAL) AS TOTAL "
					. "  FROM ( "
						. " SELECT b.DIA, SUM(b.TOTAL) AS TOTAL, " 
							. " SUM(CASE WHEN b.CODIGO_RESPUESTA < 11 THEN b.TOTAL ELSE 0 END ) AS ACCEPTED, "
							. " SUM(CASE WHEN b.CODIGO_RESPUESTA > 10 THEN b.TOTAL ELSE 0 END ) AS REJECTED " 
						. " FROM " . PFX_SRV_DB . "TBL_MON_HORA_ATM_NAC AS b"
						. " WHERE b.DIA = :dia "  . (( $this->id_client > 0 ) ? " AND b.FIID_TARJ = :id_client " : '')
						. " GROUP BY b.DIA "
						. " UNION  "
						. " SELECT c.DIA, SUM(c.TOTAL) AS TOTAL, " 
							. " SUM(CASE WHEN c.CODIGO_RESPUESTA < 11 THEN c.TOTAL ELSE 0 END ) AS ACCEPTED, "
							. " SUM(CASE WHEN c.CODIGO_RESPUESTA > 10 THEN c.TOTAL ELSE 0 END ) AS REJECTED " 
						. " FROM " . PFX_SRV_DB . "TBL_MON_HORA_ATM_INT AS c" 
						. " WHERE c.DIA = :dia "  . (( $this->id_client > 0 ) ? " AND c.FIID_TARJ = :id_client " : '')
						. " GROUP BY c.DIA "
					. " ) AS a GROUP BY a.DIA ";
		//echo $query0 ;			
					/*Oracle
		$query1 =  " SELECT DIA, SUM(ACCEPTED) AS ACCEPTED, SUM(REJECTED) AS REJECTED, SUM(TOTAL) AS TOTAL "
					. "  FROM ( "
						. " SELECT DIA, SUM(TOTAL) AS TOTAL, " 
							. " SUM(CASE WHEN CODIGO_RESPUESTA < 11 THEN TOTAL ELSE 0 END ) AS ACCEPTED, "
							. " SUM(CASE WHEN CODIGO_RESPUESTA > 10 THEN TOTAL ELSE 0 END ) AS REJECTED " 
						. " FROM " . PFX_SRV_DB . "TBL_MON_HORA_ATM_NAC_" . date('Ym') . " "
						. " WHERE DIA = :dia "  . (( $this->id_client > 0 ) ? " AND FIID_CAJERO = :id_client " : '')
						. " GROUP BY DIA "
						. " UNION  "
						. " SELECT DIA, SUM(TOTAL) AS TOTAL, " 
							. " SUM(CASE WHEN CODIGO_RESPUESTA < 11 THEN TOTAL ELSE 0 END ) AS ACCEPTED, "
							. " SUM(CASE WHEN CODIGO_RESPUESTA > 10 THEN TOTAL ELSE 0 END ) AS REJECTED " 
						. " FROM " . PFX_SRV_DB . "TBL_MON_HORA_ATM_INT_" . date('Ym') . " "
						. " WHERE DIA = :dia "  . (( $this->id_client > 0 ) ? " AND FIID_CAJERO = :id_client " : '')
						. " GROUP BY DIA "
					. " ) GROUP BY DIA ";
		*/
		
			$query1 =  " SELECT a.DIA, SUM(a.ACCEPTED) AS ACCEPTED, SUM(a.REJECTED) AS REJECTED, SUM(a.TOTAL) AS TOTAL "
					. "  FROM ( "
						. " SELECT b.DIA, SUM(b.TOTAL) AS TOTAL, " 
							. " SUM(CASE WHEN b.CODIGO_RESPUESTA < 11 THEN b.TOTAL ELSE 0 END ) AS ACCEPTED, "
							. " SUM(CASE WHEN b.CODIGO_RESPUESTA > 10 THEN b.TOTAL ELSE 0 END ) AS REJECTED " 
						. " FROM " . PFX_SRV_DB . "TBL_MON_HORA_ATM_NAC AS b"
						. " WHERE b.DIA = :dia "  . (( $this->id_client > 0 ) ? " AND b.FIID_CAJERO = :id_client " : '')
						. " GROUP BY b.DIA "
						. " UNION  "
						. " SELECT c.DIA, SUM(c.TOTAL) AS TOTAL, " 
							. " SUM(CASE WHEN c.CODIGO_RESPUESTA < 11 THEN c.TOTAL ELSE 0 END ) AS ACCEPTED, "
							. " SUM(CASE WHEN c.CODIGO_RESPUESTA > 10 THEN c.TOTAL ELSE 0 END ) AS REJECTED " 
						. " FROM " . PFX_SRV_DB . "TBL_MON_HORA_ATM_INT AS c"
						. " WHERE c.DIA = :dia "  . (( $this->id_client > 0 ) ? " AND c.FIID_CAJERO = :id_client " : '')
						. " GROUP BY c.DIA "
					. " ) AS a GROUP BY a.DIA ";
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
				$this->set_error(" No se obtunvieron valores del servicio ATM Emisor " . ( $this->client_code != '' ? "(" . $this->client_code . ")" : "" ) . ".", ERR_DB_QRY ); 
			}
			
			if ( count($result1) > 0 ){
				
				$totals = $result1[0];
				
				$this->indicators[1]['total_transactions'] = $totals['TOTAL'];
				$this->indicators[1]['total_accepted'] = $totals['ACCEPTED'];
				$this->indicators[1]['total_rejected'] = $totals['REJECTED'];
				
				$t1 = $totals['TOTAL']; 
			} else { 
				$this->set_error(" No se obtunvieron valores del servicio ATM Adquiriente" . ( $this->client_code != '' ? "(" . $this->client_code . ")" : "" ) . ".", ERR_DB_QRY ); 
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
		$fiid = ( $type == 'EMI' ) ? 'FIID_TARJ' : 'FIID_CAJERO';
		$this->indicators[$idx]['top_rejected'] = array();
		/*ORACLE
		$query =  " SELECT CODIGO_RESPUESTA, SUM(TOTAL) AS TOTAL FROM ( "
					. " SELECT SUM(TOTAL) AS TOTAL, CODIGO_RESPUESTA FROM " . PFX_SRV_DB . "TBL_MON_HORA_ATM_NAC_" . date('Ym') 
						. " WHERE  CODIGO_RESPUESTA > 10  AND DIA = :dia "
		 					. (( $this->id_client > 0 ) ? " AND $fiid = :id_client " : '')
			 			. " GROUP BY CODIGO_RESPUESTA "
			 		. " UNION "
			 		. " SELECT SUM(TOTAL) AS TOTAL, CODIGO_RESPUESTA FROM " . PFX_SRV_DB . "TBL_MON_HORA_ATM_INT_" . date('Ym') 
			 			. " WHERE  CODIGO_RESPUESTA > 10  AND DIA = :dia "
		 					. (( $this->id_client > 0 ) ? " AND $fiid = :id_client " : '')
			 			. " GROUP BY CODIGO_RESPUESTA "
			 		. " ORDER BY CODIGO_RESPUESTA "
				. " ) GROUP BY CODIGO_RESPUESTA ORDER BY TOTAL DESC ";
		*/
		
			$query =  " SELECT a.CODIGO_RESPUESTA, SUM(a.TOTAL) AS TOTAL FROM ( "
					. " SELECT SUM(b.TOTAL) AS TOTAL, b.CODIGO_RESPUESTA FROM " . PFX_SRV_DB . "TBL_MON_HORA_ATM_NAC AS b" 
						. " WHERE  b.CODIGO_RESPUESTA > 10  AND b.DIA = :dia "
		 					. (( $this->id_client > 0 ) ? " AND $fiid = :id_client " : '')
			 			. " GROUP BY b.CODIGO_RESPUESTA "
			 		. " UNION "
			 		. " SELECT SUM(c.TOTAL) AS TOTAL, c.CODIGO_RESPUESTA FROM " . PFX_SRV_DB . "TBL_MON_HORA_ATM_INT AS c" 
			 			. " WHERE  c.CODIGO_RESPUESTA > 10  AND DIA = :dia "
		 					. (( $this->id_client > 0 ) ? " AND $fiid = :id_client " : '')
			 			. " GROUP BY c.CODIGO_RESPUESTA "
			 		. " ORDER BY CODIGO_RESPUESTA "
				. " ) AS a GROUP BY a.CODIGO_RESPUESTA ORDER BY a.TOTAL DESC ";
			//echo $query;	
		$query_top = ' SELECT d.CODIGO_RESPUESTA, d.TOTAL FROM ( ' . $query . ' ) AS d LIMIT 0,5' ;
		//echo $query_top;
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
				$this->set_error("No se obtunvieron valores del servicio para top rechazados $type  " . ( $this->client_code != '' ? "(" . $this->client_code . ")" : "" ) . ".", ERR_DB_QRY );
				return FALSE;
			}
		} else {
			$this->set_error("Ocurrió un error al obtener los totales del servicio para top rechazados $type  " . ( $this->client_code != '' ? "(" . $this->client_code . ")" : "" ) . ".", ERR_DB_QRY );
			return FALSE;
		} 
	}


	public function is_up(){
 		
	    $this->last_total = $this->get_last_total(); 
	    if ( $this->last_total ){ 
            //if ( $this->last_total['timestamp'] > time() - ( $this->time_prosa * 60 ) )
			/*	return TRUE;

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
