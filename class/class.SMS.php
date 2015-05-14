<?php
if ( !class_exists('Service')){
	require_once DIRECTORY_CLASS . 'class.service.php';
}
/**
 * SMS Class
 * 
 * 
 */
class SMS extends Service {
	
	function __construct( $id_client = 0) {
		
		parent::__construct();
		
		$this->class 	= "SMS";
		$this->service 	= "SMS";
		$this->code 	= "s10";
		
		$this->id_service = 10;
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
		$query =  "SELECT COUNT(*) AS TOTAL FROM " . PFX_SMS_DB . "TBL_SMS_LOG@LG_ROP_APPWEB_SMS_SMS "  
				. " WHERE ID_BROKER = 'P' AND LOCAL_DATE > TO_DATE(:lcl_date, 'yyyy-mm-dd hh24:mi') " ;
		$result = $obj_bd->query( $query, array( ':lcl_date' => date('Y-m-d 00:00') ) );
		if ( $result !== FALSE ){
			return count( $result[0] ) > 0 ? $result[0]['TOTAL'] : 0;
		} else {
			$this->set_error("Ocurrió un error al obtener la última actualización SMS.", ERR_DB_QRY );
			return FALSE;
		} 
	}
	 
	public function load_service(){
		
		$this->state = $this->is_up(); 
		
	}
	
	private function set_service_totals(){
		
		$this->indicators[0]['total_transactions'] = 0;
		$this->indicators[0]['total_accepted'] = 0;
		$this->indicators[0]['total_rejected'] = 0; 
		
	}
	
	private function set_top_rejected(){
		$this->indicators[0]['top_rejected'] = array();  
	}
	
	public function is_up(){
		
		$this->last_total = $this->get_last_total();
		if ( $this->last_total ){
			
			if ( $this->last_total['timestamp'] > time() - ( $this->time_prosa * 60 ) ){ 
				return TRUE;
			}
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
				"status" 		=> $this->state ? 1 : 0
			);
	}  
}
?>
