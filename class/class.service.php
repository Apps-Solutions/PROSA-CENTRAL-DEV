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
    public $id_service2 = '';
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
	public $pre_total = 0;
	public $date;
	public $id_last_total = 0;
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
    	//$this->db = new oracle_db();
    	$this->db = new PDOMySQL(); 
		
		$this->codes = $this->get_codes();
	}
	
	protected function load_threshold(){
		global $obj_bd;
		if ( $this->code != '' ){
			$query = "SELECT * FROM " . PFX_MAIN_DB . "service "
				. " INNER JOIN " . PFX_MAIN_DB . "threshold ON th_se_id_service = id_service "
				. " WHERE se_command = :code "; 			
			$info = $obj_bd->query( $query, array( ':code' => $this->code ) );
			if ( $info && count($info) > 0 ){
				$this->threshold  = $info[0]['th_threshold'];
				$this->time_prosa = $info[0]['th_time_prosa'];
				$this->time_client= $info[0]['th_time_client'];
			} else {
				$this->set_error("Threshold info not found (" . $this->code . ")", ERR_DB_QRY );
			}
		}
	} 
	
	protected function get_last_total(){
		global $obj_bd;
		$query = " SELECT MAX(id_last_total) AS id FROM " . PFX_MAIN_DB . "last_total WHERE lt_se_id_service=" . $this->id_service;		
		$result = $obj_bd->query($query);

		if ($result !== FALSE) 
		{
			if (count($result) > 0) 
			{
				$total = $result[0];
				$this->id_last_total = $total['id'];
					
				$query = "SELECT lt_total, lt_pre_total, lt_fecha, lt_timestamp FROM " . PFX_MAIN_DB . "last_total WHERE lt_se_id_service = :id_service AND id_last_total= :id_last_total ";
				
				$result = $obj_bd->query( $query, array( ':id_service' => $this->id_service, ':id_last_total' => $this->id_last_total) );
				//var_dump($this->id_last_total);
				if ( $result !== FALSE ){
					$resp = array();
					if ( count($result) > 0 ){ 
						$resp['total']	 	= $result[0]['lt_total'];
						$resp['timestamp'] 	= $result[0]['lt_timestamp'];
						$resp['pre_total']  = $result[0]['lt_pre_total'];
						$resp['date'] 		= $result[0]['lt_fecha'];
					} else {
						$resp['total']	 	= 0;
						$resp['timestamp'] 	= 0;
						$resp['pre_total']  = 0;
						$resp['date']		= '';
					}
					
					$this->last_total 		= $resp['total'];
					$this->last_timestamp 	= $resp['timestamp'];
					$this->pre_total 		= $resp['pre_total'];
					$this->date 			= $resp['date'];
					
					return $resp;
				} else {
					$this->set_error("Ocurrió un error al obtener la última actualización.", ERR_DB_QRY );
					return FALSE;
				}
			}
		}
	}
	
	protected function set_last_total( $total ){
		global $obj_bd;
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
			if ($result = $obj_bd->execute( $query, $values ) !== FALSE){
				return TRUE;
			} else {
				$this->set_error("Ocurrió un error al actualizar el último total.", ERR_DB_EXEC );
				return FALSE;
			}
		} 
		return FALSE;
	}
	
	public function has_maintenance(){
		global $obj_bd; 
		$query = "SELECT * FROM " . PFX_MAIN_DB . "maintenance WHERE ma_se_id_service = " . $this->id_service . " AND ma_start <= :now AND ma_end <= :now ";
		$result = $obj_bd->query( $query, array( ':now' => time()) );
		if ( $result !== FALSE ){
			if ( count($result) > 0 ) return TRUE;
			else return FALSE;
		} else {
			$this->set_error("Ocurrió un error al buscar la ventana de mantenimiento.", ERR_DB_QRY );
			return FALSE;
		} 
	} 
	
	public function get_indicators_html(){

		if ( count($this->indicators) > 0 ){//print_r($this->indicators);

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
			$header[] = '|';
			$header[] = '|';
			$header[] = 'Top 5 Rechazadas';
			$xls->set_header($header, $rowIni);
			$rowIni++;
			
			$header = array();
			$header[] = '|';
			$header[] = '%';
			$header[] = 'Total';
			$header[] = '|';
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
				$xls->xlsWriteLabel($rowIni + $j, 4, $top['code'] . " - " . utf8_encode($top['motive']));
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
		global $obj_bd; 
		$query = "SELECT CODIGO AS response, CODIGO_RESPUESTA AS code FROM " . PFX_SRV_DB . "COD_RESP_POS ";
		$result = $obj_bd->query($query );
		$resp = array();
		foreach ($result as $k => $cod) {
			$resp[ $cod['CODE'] ] = ($cod['RESPONSE']); 
		}
		return $resp;
	}
 
	 
	protected function set_client_code(){
		global $obj_bd;
		if ( $this->id_client > 0 ){
			$query = "SELECT * FROM " . PFX_MAIN_DB . "client WHERE id_client = :id_client ";
		
			$result = $obj_bd->query( $query, array( ':id_client' => $this->id_client ) );
			if ( $result !== FALSE){ 
				if ( count($result) > 0 ){
					$info = $result[0];
					$this->client_code = $info['cl_code'];
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
	
	
	
	public function return_indicators_xls(){
		 /**/
	
		$per_acep=array();
		$tot_acep=array();
		$per_rech=array();
		$tot_rech=array();
		/*$top_code=array(array());
		$top_data=array(array());
		$top_total=array(array());*/
		    $top_code=array();
			$top_data=array();
			$top_total=array();
			
		foreach($this->indicators as $k => $data){
		//print_r($data);
			
			//Aceptadas
		  	
			$per_acep[$k]=number_format(( $data['total_transactions'] > 0 ?  ($data['total_accepted'] * 100 / $data['total_transactions'])   : 0) , 2 );
			$tot_acep[$k]=number_format($data['total_accepted']) ;
			
		  	
			//Rechazadas
			$per_rech[$k]=number_format(( $data['total_transactions'] > 0 ?  ($data['total_rejected'] * 100 / $data['total_transactions'])   : 0) , 2 );
			$tot_rech[$k]=number_format($data['total_rejected']) ;
			
    		// Top 5
			/**/foreach ($data['top_rejected'] as $j => $top ){
				
				$top_code[$k][$j]=$top['code'] . " - " . utf8_encode($top['motive']);
				$top_data[$k][$j]=( $data['total_rejected'] > 0 ? number_format ($top['total'] * 100 / $data['total_rejected'] , 2 ) :  0 );
				$top_total[$k][$j]=number_format($top['total'], 0, '.', ',') ;
				
							
			}
			
		}
		
	///return $this->indicators['total_transactions'];	
$data = array(
    array("A" => $this->service, "B" => " ", 				"C" =>"Estado del Servicio",  "D"=>($this->state ? 'UP' : 'DOWN' ), "E"=>" ", "F"=>"Última actualización", "G"=>date('Y:m:d H:i:s', $this->last_timestamp)),
    array("A" => " ", 			 "B" => " ", 				"C" =>" ",  "D"=>" ", 												"E"=>" ", "F"=>" ", "G"=>" "),
    array("A" => " ", 			 "B" => " ", 				"C" =>" ",  "D"=>" ", "E"=>" ", "F"=>" ", "G"=>" "),
    array("A" => "Emisor", 		 "B" => $this->service, 	"C" =>" ",  "D"=>" ", "E"=>"Top 5 Rechazadas", "F"=>" ", "G"=>" "),
    array("A" => " ", 	 		 "B" => "%", 				"C" =>"Total",  "D"=>" ", "E"=>"Motivo", "F"=>"%", "G"=>"Total"),
    array("A" => "Aceptadas", 	 "B" => $per_acep[0], 		"C" =>$tot_acep[0],  "D"=>" ", "E"=>$top_code[0][0], "F"=>$top_data[0][0], "G"=>$top_total[0][0]),
    array("A" => "Rechazadas", 	 "B" => $per_rech[0], 		"C" =>$tot_rech[0],  "D"=>" ", "E"=>$top_code[0][1], "F"=>$top_data[0][1], "G"=>$top_total[0][1]),
    array("A" => " ", 	 		 "B" => " ", 				"C" =>" ",  "D"=>" ", "E"=>$top_code[0][2], "F"=>$top_data[0][2], "G"=>$top_total[0][2]),
    array("A" => " ", 	 		 "B" => " ", 				"C" =>" ",  "D"=>" ", "E"=>$top_code[0][3], "F"=>$top_data[0][3], "G"=>$top_total[0][3]),
    array("A" => " ", 			 "B" => " ", 				"C" =>" ",  "D"=>" ", "E"=>$top_code[0][4], "F"=>$top_data[0][4], "G"=>$top_total[0][4]),
    array("A" => " ", 	 		 "B" => " ", 				"C" =>" ",  "D"=>" ", "E"=>$top_code[0][5], "F"=>$top_data[0][5], "G"=>$top_total[0][5]),
    array("A" => " ", 	 		 "B" => " ", 				"C" =>" ",  "D"=>" ", "E"=>" ", "F"=>" ", "G"=>" "),
    array("A" => " ", 	 		 "B" => " ", 				"C" =>" ",  "D"=>" ", "E"=>" ", "F"=>" ", "G"=>" "),
    array("A" => "Adquirente", 	 "B" => $this->service, 	"C" =>" ",  "D"=>" ", "E"=>"Top 5 Rechazadas", "F"=>" ", "G"=>" "),
    array("A" => " ", 	 		 "B" => "%", 				"C" =>"Total",  "D"=>" ", "E"=>"Motivo", "F"=>"%", "G"=>"Total"),
    array("A" => "Aceptadas", 	 "B" => $per_acep[1], 		"C" =>$tot_acep[1],  "D"=>" ", "E"=>$top_code[1][0], "F"=>$top_data[1][0], "G"=>$top_total[1][0]),
    array("A" => "Rechazadas", 	 "B" => $per_rech[1], 		"C" =>$tot_rech[1],  "D"=>" ", "E"=>$top_code[1][1], "F"=>$top_data[1][1], "G"=>$top_total[1][1]),
    array("A" => " ", 	 		 "B" => " ", 				"C" =>" ",  "D"=>" ", "E"=>$top_code[1][2], "F"=>$top_data[1][2], "G"=>$top_total[1][2]),
    array("A" => " ", 	 		 "B" => " ", 				"C" =>" ",  "D"=>" ", "E"=>$top_code[1][3], "F"=>$top_data[1][3], "G"=>$top_total[1][3]),
    array("A" => " ", 			 "B" => " ", 				"C" =>" ",  "D"=>" ", "E"=>$top_code[1][4], "F"=>$top_data[1][4], "G"=>$top_total[1][4]),
    array("A" => " ", 	 		 "B" => " ", 				"C" =>" ",  "D"=>" ", "E"=>$top_code[1][5], "F"=>$top_data[1][5], "G"=>$top_total[1][5]),
  );		
   return $data;
  
	}
 /*Carlos Servín*/
	public function get_pra_chart($service){
	global $obj_bd;
	$values=array(
					":service" => $service,
					":timea"   => time(),
					":timeb"   => strtotime ( '-11 minute' , time() ) ,					
				 );
	$query="SELECT * FROM " . PFX_MAIN_DB . "charts WHERE pcs_se_id_service=:service AND pcs_timestamp BETWEEN :timeb AND :timea";
	$result = $obj_bd->query($query, $values);
		if(count($result)>0){
			return TRUE;
		}else{
			return FALSE;
		}
	}

	public function insert_info_charts($indicators)
	{
		global $obj_bd; //echo 'si';
		$id_service=explode("s",$_REQUEST['command']);
		$val=$indicators;		  
		$top_5=array();
		$datatop=array();

		for ($i=0; $i <= count($val)-1; $i++) 
		{ 
			foreach ($val[$i] as $key => $value) 
			{   
				foreach($value as $k => $data){
				       	
				    if ($i===0) 
				    {				       	
			        	$string1.=$data['code'].",".$data['motive']." ,".$data['total'].",";
			        	
			        	$values=array(
			        		":total_acepted"   => $val[$i]['total_accepted'],
			        		":total_rejected"  => $val[$i]['total_rejected'],
			        		":top_5_rejected"  => $string1,
			        		":id_service"  => $id_service[1],
			        		":service_status"  => "UP",
			        		":type"  => strtolower($val[$i]['source'].'_'.$val[$i]['name']),
			        		":timestamp" => time()
			      		); 
			      		
				    }
				    elseif($i===1)
				    {
			         	$string2.=$data['code'].",".$data['motive']." ,".$data['total'].",";
			         	
			         	$values2=array(
			        		":total_acepted"   => $val[$i]['total_accepted'],
			        		":total_rejected"  => $val[$i]['total_rejected'],
			        		":top_5_rejected"  => $string2,
			        		":id_service"  => $id_service[1],
			        		":service_status"  => "UP",
			        		":type"  => strtolower($val[$i]['source'].'_'.$val[$i]['name']),
			        		":timestamp" => time()
			      		); 

			      		
				    }
				    
				}
				
			}
		}  
		$this->insert_true($values);
		$this->insert_true($values2);
		  
 }

 public function insert_true($datos)
 {
 	global $obj_bd;

 	$query="INSERT INTO " . PFX_MAIN_DB . "charts (pcs_total_acepted, pcs_total_rejected, pcs_top_5_rejected, pcs_se_id_service, pcs_service_status, pcs_type, pcs_timestamp)". 
		    " VALUES(:total_acepted, :total_rejected, :top_5_rejected, :id_service, :service_status, :type, :timestamp)";
		 
	$result = $obj_bd->query($query, $datos);
		  
	if(count($result)>0)
	{
		return TRUE;
	}
	else
	{
		return FALSE;
 	}
}
 
 public function insert_old_last_total($id_service){
 	global $obj_bd;
	
	$query0="SELECT x.id_last_total, x.lt_total FROM (SELECT * FROM pra_last_total ORDER BY lt_se_id_service  DESC) AS x WHERE lt_se_id_service=:id_service GROUP BY lt_se_id_service";
	$result0 = $obj_bd->query($query0, array(":id_service"=> $id_service));
	
	echo $result0[0]['lt_total'];	

 	$query="INSERT INTO " . PFX_MAIN_DB . "last_total (lt_se_id_service, lt_total, lt_pre_total, lt_fecha, lt_timestamp)". 
		    " VALUES(:lt_se_id_service, :lt_total, :lt_pre_total, :lt_fecha, :lt_timestamp)";
		 
	$result = $obj_bd->query($query, $datos);
		  
	if(count($result)>0)
	{
		return TRUE;
	}
	else
	{
		return FALSE;
 	}
}

}
?>