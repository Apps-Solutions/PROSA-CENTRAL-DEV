<?php
/**
 * Service Class
 * 
 * @package		Prosa
 */
abstract class Service extends Object{
 	 /**
     * Property: id_service
     * Service ID
     */
    public $id_service = '';
	 /**
     * Property: id_service
     * Service ID
     */
    protected $id_client = '';
	 /**
     * Property: client_code
     * Client Code (FIID)
     */
    protected $client_code = '';
	
	 /**
     * Property: service
     * Service Name
     */
    public $service = '';
	
	 /**
     * Property: code
     * Service Code
     */
    protected $code = '';
	
	 /**
     * Property: threshold
     * reyected threshold
     */
    public $threshold = '';
	
	 /**
     * Property: time_prosa
     * 
     */
    public $time_prosa = '';
	
	 /**
     * Property: time_client
     * 
     */
    public $time_client = '';
	
	 /**
     * Property: total_transactions 
     */
    public $total_transactions = 0;
	
	 /**
     * Property: total_accepted 
     */
    protected $total_accepted= 0;
	
	 /**
     * Property: total_rejected 
     */
    protected $total_rejected = 0;
	


	/**
     * Property: description
     * 
     */
    public $description = '';
	
	
	
	public $state = FALSE;
	
	public $has_state = FALSE;
	
	
	 /**
     * Property: last_total
     * 
     */
    public $last_total = FALSE;
	
	 /**
     * Property: db
     * 
     */
    protected $db = FALSE;
	
	/**
	 * protected Codes array
	 */
	 protected  $codes = array();
	
	/**
	 * protected Codes array
	 */
	 public  $indicators = array();
	
	/**
	 * protected top_rejected array
	 */
	 public $top_rejected = array();
	
	/**
     * Constructor: __construct
     * 
     */ 
    public function __construct() {
    	$this->db = new oracle_db(); 
		$this->codes = $this->get_codes();
	}
	
	protected function load_threshold(){
		if ( $this->code != '' ){
			$query = "SELECT * FROM " . PFX_MAIN_DB . "service "
				. " INNER JOIN " . PFX_MAIN_DB . "threshold ON th_se_id_service = id_service "
				. " WHERE se_command = :code "; 
			$info = $this->db->query( $query, array( ':code' => $this->code ) );
			if ( $info && count($info) > 0 ){
				$this->threshold  = $info[0]['TH_THRESHOLD'];
				$this->time_prosa = $info[0]['TH_TIME_PROSA'];
				$this->time_client= $info[0]['TH_TIME_CLIENT'];
			} else {
				$this->set_error("Threshold info not found (" . $this->code . ")", ERR_DB_QRY );
			}
		}
	} 
	
	protected function get_last_total(){
		$query = "SELECT lt_total, lt_timestamp FROM " . PFX_MAIN_DB . "last_total WHERE lt_se_id_service = :id_service ";
		$result = $this->db->query( $query, array( ':id_service' => $this->id_service) );
		if ( $result !== FALSE ){
			$resp = array();
			if ( count($result) > 0 ){ 
				$resp['total']	 	= $result[0]['LT_TOTAL'];
				$resp['timestamp'] 	= $result[0]['LT_TIMESTAMP'];
			} else {
				$resp['total']	 	= 0;
				$resp['timestamp'] 	= 0;
			}
			
			$this->last_total 		= $resp['total'];
			$this->last_timestamp 	= $resp['timestamp'];
			
			return $resp;
		} else {
			$this->set_error("Ocurrió un error al obtener la última actualización.", ERR_DB_QRY );
			return FALSE;
		}
	}
	
	protected function set_last_total( $total ){
		if ( $exist = $this->get_last_total() ){
			if ($exist['timestamp'] == 0 ){
				$query = "INSERT INTO " . PFX_MAIN_DB . "last_total ( lt_se_id_service, lt_total, lt_timestamp) "
						. " VALUES ( :id_service, :total, :timestamp ) "; 
			} else {
				$query = " UPDATE " . PFX_MAIN_DB . "last_total SET "
					. " lt_total = :total,  lt_timestamp = :timestamp "
					. " WHERE lt_se_id_service = :id_service ";
			}
			$values = array( ':id_service' => $this->id_service, ':total' => $total, ':timestamp' => time() );
			if ($result = $this->db->execute( $query, $values ) !== FALSE){
				return TRUE;
			} else {
				$this->set_error("Ocurrió un error al actualizar el último total.", ERR_DB_EXEC );
				return FALSE;
			}
		} 
		return FALSE;
	}
	
	public function has_maintenance(){ 
		$query = "SELECT * FROM " . PFX_MAIN_DB . "maintenance WHERE ma_se_id_service = " . $this->id_service . " AND ma_start <= :now AND ma_end <= :now ";
		$result = $this->db->query( $query, array( ':now' => time()) );
		if ( $result !== FALSE ){
			if ( count($result) > 0 ) return TRUE;
			else return FALSE;
		} else {
			$this->set_error("Ocurrió un error al buscar la ventana de mantenimiento.", ERR_DB_QRY );
			return FALSE;
		} 
	} 
	
	public function get_indicators_html(){
		if ( count($this->indicators) > 0 ){
			foreach ( $this->indicators as $k => $data ) {
				/* 
				<div class='indicator-content'>
					<div class="indicator-menu col-xs-12">  
						<div class="row text-center">
							<button class='indicator-btn btn' onclick="change_graf('ring', <?php echo $k ?> );">
								<span> Aceptadas </span>
							</button>
							&nbsp;&nbsp;&nbsp;
							<button class='indicator-btn btn' onclick="change_graf('top', <?php echo $k ?> );">
								<span> Top 5  </span>
							</button>
						</div>
					</div>
				*/
				
				require DIRECTORY_VIEWS . "services/grf.ring.php";
				require DIRECTORY_VIEWS . "services/grf.top.php";
				echo "<div class='row'> &nbsp; </div>"; 
			}
			
		}
	}
	
	public function get_indicators_xls()
	{
		require DIRECTORY_CLASS . 'class.xlsmngr.php';
		$xls = new XlsMngr();
		
		$rowIni = 0;
		$header = array();
		$header[] = $this->service;
		$header[] = '';
		$header[] = 'Estado del Servicio:';
		$header[] = ($this->state ? 'UP' : 'DOWN' );
		$header[] = '';
		$header[] =  utf8_decode('Última actualización:');
		$header[] = date('Y:m:d H:i:s', $this->last_timestamp);
		$xls->set_header($header, $rowIni);
		
		$rowIni++;
		
		foreach($this->indicators as $k => $data)
		{
			$header = array();
			$header[] = $data['source'];
			$header[] = $data['name'];
			$header[] = '';
			$header[] = '';
			$header[] = 'Top 5 Rechazadas';
			$xls->set_header($header, $rowIni);
			$rowIni++;
			
			$header = array();
			$header[] = '';
			$header[] = '%';
			$header[] = 'Total';
			$header[] = '';
			$header[] = 'Motivo';
			$header[] = '%';
			$header[] = 'Total';
			$xls->set_header($header, $rowIni);
			$rowIni++;
			
			$xls->xlsWriteLabel($rowIni, 0, 'Aceptadas');
			$xls->xlsWriteLabel($rowIni, 1, number_format(( $data['total_transactions'] > 0 ?  ($data['total_accepted'] * 100 / $data['total_transactions'])   : 0) , 2 ) );
			$xls->xlsWriteLabel($rowIni, 2, number_format($data['total_accepted']) );
			
			$xls->xlsWriteLabel($rowIni +1, 0, 'Rechazadas');
			$xls->xlsWriteLabel($rowIni +1, 1, number_format(( $data['total_transactions'] > 0 ?  ($data['total_rejected'] * 100 / $data['total_transactions'])   : 0) , 2 ) );
			$xls->xlsWriteLabel($rowIni +1, 2, number_format($data['total_rejected']) );
			
			foreach ($data['top_rejected'] as $j => $top )
			{
				$xls->xlsWriteLabel($rowIni + $j, 4, $top['code'] . " - " . $top['motive'] );
				$xls->xlsWriteLabel($rowIni + $j, 5, ( $data['total_rejected'] > 0 ? number_format ($top['total'] * 100 / $data['total_rejected'] , 2 ) :  0 ) );
				$xls->xlsWriteLabel($rowIni + $j, 6, number_format($top['total'], 0, '.', ',') );
				//$sum += $top['total'];
			}
			
			$rowIni += 6;
		}
		
		$exp = $xls->finish_xls();
		return $exp;
	}
	
	public function get_state_html(){
		if ( $this->has_state ){
			$state = $this->state;
			require_once DIRECTORY_VIEWS . "services/srv.state.php"; 
		}
	}
	
	protected function get_codes(){ 
		$query = "SELECT CODIGO AS response, CODIGO_RESPUESTA AS code FROM " . PFX_SRV_DB . "COD_RESP_POS ";
		$result = $this->db->query($query );
		$resp = array();
		foreach ($result as $k => $cod) {
			$resp[ $cod['CODE'] ] = ($cod['RESPONSE']); 
		}
		return $resp;
	}
 
	 
	protected function set_client_code(){
		if ( $this->id_client > 0 ){
			$query = "SELECT * FROM " . PFX_MAIN_DB . "client WHERE id_client = :id_client ";
			$result = $this->db->query( $query, array( ':id_client' => $this->id_client ) );
			if ( $result !== FALSE){ 
				if ( count($result) > 0 ){
					$info = $result[0];
					$this->client_code = $info['CL_CODE'];
					return TRUE;
				} else {
					$this->set_error("No se pudo obtener el código del cliente ( $this->id_client ).", ERR_DB_QRY );
					return FALSE;
				}  
			} else {
				$this->set_error("Ocurrió un error al obtener el código del cliente.", ERR_DB_QRY );
				return FALSE;
			}
		}
	}	
}
?>