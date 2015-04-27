<?php 
ini_set('display_errors', TRUE ); ini_set('memory_limit', '-1'); date_default_timezone_set("America/Mexico_City"); define 
("ORCL_HOST", '192.168.105.89'); define ("ORCL_PORT", '1527'); define ("ORCL_SERVICE_NAME", 'ROP'); define ("ORCL_USER_NAME", 'AppWeb'); 
define ("ORCL_PASSWORD", 'Apps_mo1'); define ("PFX_MAIN_DB", 'APP.pra_'); define('LOG_DIR', '/var/www/html/app_demo/scripts/log/'); 
define('LOG_FILE', 'pra_log'); define('LOG_TMPLT', '[%s] %s @ %s: %s'); define('LOG_MAX_SIZE', '1073741824'); class Log{
	
	private $name = "";
	
	protected $handle;
	
	
	function Log( $file = FALSE ){
		$this->file = ( $file ) ? $file : LOG_DIR . LOG_FILE;
		$this->name = "Log";
		$this->check_file();
	}
	
	private function check_file(){
		try {
			if ( file_exists( $this->file ) ){
				if ( filesize( $this->file ) >= LOG_MAX_SIZE ){
					$success = rename($this->file, $this->file . "_" . date('YmdHis') );
					if ( !$success ){
						throw new Exception("ERROR: No se ha podido respaldar el archivo de Log");
						die("ERROR: No se ha podido respaldar el archivo de Log");
					}
				} 
			} 
			$this->handle = fopen( $this->file , 'a');
		}
		catch (Exception $e){
			throw new Exception("ERROR: No se ha podido crear el archivo de Log. ". $e );
			die( "ERROR: No se ha podido crear el archivo de Log. ". $e );
		} 
	}
	 
	public function write_log( $string, $type = 0, $level = 1, $user = FALSE){
		try {
			$formatted = sprintf( LOG_TMPLT , date('Y-m-d H:i:s') , "", "", $string . "\n");
			$success = fwrite($this->handle, $formatted );
			
			if ( !$success )
				die("ERROR: No se ha podido escribir en el archivo de Log. ". $success );
		} 
		catch (Exception $e){
			throw new Exception("ERROR: No se ha podido escribir en el archivo de Log. ". $e );
			die("ERROR: No se ha podido escribir en el archivo de Log. ". $e );
		} 
	}
	 
}
/*****************************************
 * oracle_db
 * Clase de Conexión a BD Oracle.
 *
 * @since 0.2
 * @author Manuel Fernández <manuel.fernandez@gruposellcom.com>
 *
 * ****************************************/ class oracle_db{
	/**
	 * ORACLE_SID
	 * @var string
	 * */
	private $sid;
	
	/**
	 * Objeto de conexión.
	 * @var object
	 * */
	private $connection;
	
	/**
	 * Query a ejecutar.
	 * @var string
	 * */
	private $query;
	
	/**
	 * Host de la Base de Datos.
	 * @var string
	 * */
	private $host = ORCL_HOST;
	
	/**
	 * Número total de registros
	 * @var integer
	 * */
	public $total_record;
	
	/**
	 * Apuntador de registros
	 * @var string
	 * */
	private $rec_position;
	
	/**
	 * Campos totales
	 * @var string
	 * */
	public $total_fields;
	
	/**
	 * Nombre del campo
	 * @var string
	 * */
	public $field_name;
  	
	/**
	 * Arreglo de errores
	 * @var array
	 * */
	public $error = array();
	
	/**
	 * Constructor de la clase oracle_bd()
	 * Se crea una conexión a la Base de Datos con los parámteros declarados de manera global.
	 *
	 * */
	function oracle_db(){
		try {
			$this->host = "" . ORCL_HOST . "";
			$this->sid = "( DESCRIPTION = (ADDRESS_LIST =(ADDRESS =(PROTOCOL = TCP)"
					. "(Host = " . ORCL_HOST . ")(Port = " . ORCL_PORT . ") ) )"
					. "(CONNECT_DATA = (SERVICE_NAME = " . ORCL_SERVICE_NAME . ")))";
		
			$this->connection = ocilogon(ORCL_USER_NAME,ORCL_PASSWORD,$this->sid, 'AL32UTF8')
									or die ( "Ocurrió un problema al intentar conectarse con el 
Servidor de BBDD. ");
			if ( !$this->connection ){
				die( 'Could not connect to DB <br> Message: ' . $this->get_error_msg( 0 ) );
			} 
		} catch ( Exception $e ){
			die( 'Could not connect to '. $this->host .' server <br> Message: ' . $e );
		} 
	}
	
	/**
	 * Realiza una consulta a la Base de Datos.
	 * @param $query_str String: Query que se ejecutará en la Base de Datos
	 * */
	public function query( $query_str = "", $values = FALSE ) {
		try {
			$this->query = @oci_parse($this->connection, $query_str);
			
			if ( $values && is_array($values)){
				foreach ($values as $k => $val) {
					@oci_bind_by_name( $this->query, $k, $values[$k] );
				}  
			}
			
			$result = @oci_execute($this->query);
			if ( !$result ){
				$this->error[] = oci_error( $this->query );
				return FALSE;
			} 
			$resp = array();
			oci_fetch_all($this->query, $resp, null, null, OCI_FETCHSTATEMENT_BY_ROW);
			return $resp;
		} catch (Exception $e){
			return FALSE;
		}
	}
	/**
	 * Ejecuta un query en la Base de Datos regresando un valor TRUE/FALSE de éxito.
	 * @param $query_str String: Query que se ejecutará en la Base de Datos
	 * @param $values Array: Arreglo de valores para cargar
	 * @return String -> false si existio error
	 * -> true si la consulta se realizao con éxito
	*/
	public function execute( $query, $values = FALSE ){
		try {
			$qry = @oci_parse( $this->connection, $query );
			if ( $values && is_array($values)){
				foreach ($values as $k => $val) {
					@oci_bind_by_name( $qry, $k, $values[$k] );
				}  
			}
			$resp = @oci_execute( $qry );
			if ( !$resp ){
				$this->error[] = oci_error( $qry );
			}
			return $resp;
		} catch ( Exception $err ){
			$this->error[] = $err;
			return FALSE;
		}
	}
	
	
	/**
	 * Regresa el primer valor del registro
	 *
	 * @return String: regresa el primer registro del arreglo
	 */
	function get_value() {
		$result = $this->get_array(0);
		return $result[0];
	}
	/**
	 * Regresa el siguiente valor del (@link $campo) en la (@link $tabla)
	 * @param $tabla String: Nombre de la tabla de referencia
	 * @param $campo String: Nombre del campo a incrementar
	 * @param $incremento Integer: salto de incremento
	 * @return String -> false si existio error
	 * -> true si la consulta se realizao con éxito
	 */
	function get_id($tabla = '', $campo = '', $incremento='') {
		if(empty($incremento)) {
	 		$incremento=1;
	 	}  
		$sql="SELECT nvl(max($campo),0) + $incremento as id FROM " . $tabla . " ";
		$id = $this->query($sql);
		return $id[0]['ID'];
	}
      
	/**
	 * Regresa un arreglo de registros de la Base de Datos
	 * @param $db String: Base de Datos a la que se consultará
	 * @return String -> false si existio error
	 * -> true si la consulta se realizao con éxito
	 */
	function get_array( $db = "DEFAULT" ){
		$result=@oci_fetch_array($this->query, OCI_BOTH + OCI_RETURN_NULLS );
		if(!is_array($result))
			return false;
		$this->total_field = ocinumcols( $this->query );
		if($db=="DEFAULT"){
			foreach($result as $key=>$val){
				$result[$key]=trim($val);
				$result[$key]=trim(htmlspecialchars($val));
			} 
		} 
		return $result;
	}
	
	/**
	 * Regresa el nombre del campo
	 * @param $i Integer: Índice del campo
	 * @return String: Nombre del campo
	 */
	function get_field_name( $i ){
		return ocicolumnname( $this->query, $i+1 );
	}
 
	/**
	 * Regresa el número total de campos
	 * @return Integer: Número de campos
	 */
	function get_num_cols() {
		return @ocinumcols( $this->query );
	}
	/**
	 * Regresa el tipo de campo
	 * @param $i Integer: Índice del campo
	 * @return String: Nombre del campo
	 */
	function get_column_type( $i ){
		return ocicolumntype($this->query, $i+1);
	}
	    
	/**
	 * Regresa el total de registros
	 * @return Integer: Número total de registros regresados por la consulta.
	 */
	function get_num_rows(){
		return oci_num_rows($this->query);
	}
	/**
	 * Libera los recursos de la Base de Datos
	 */
	function free(){
		ocifreestatement($this->query);
		ocilogoff($this->connection);
		unset($this);
	}
	
	/**
	 * Regresa como string el(los) Error(es) generado(s) en la Base de Datos
	 * @param $error_no Integer: Índice del error.
	 * @param $msg String: Mensaje de error.
	 * @return String: Mensaje de error con formato.
	 */
	function get_error_msg( $error_no, $msg="" ){
		$log_msg = NULL;
  		$error_msg = "<b> Error:</b> <pre><font color=red>\n\t" . ereg_replace(",",",\n\t",$msg) . "</font></pre>";
  		$error_msg .= "<b><i> Error del sistema:</i></b>";
  		$error_msg .= "<font color=red><pre>";
  		foreach(ocierror($error_no) as $key=>$val){
  			$log_msg.="$key : ".$val."\n";
  			$error_msg.="$key : $val \n";
		}
		$error_msg.="</pre></font>";
		return $error_msg;
	}
	
	/**
	 * Regresa el arreglo de errores
	 * @param	$error_no	Integer: Índice del error
	 * @return Array: Arreglo de error
	 */
	function get_error_array( ){
		 return oci_error($this->connection);
	}	
	
}
$Log = new Log(); $db = new oracle_db();
 
function get_codes(){
	global $db;
	
	$query = "SELECT id_response_code AS code FROM pra_response_code ";
	$result = $db->query($query );
	return $result;
}
function get_clients(){
	global $db;
	
	$query = "SELECT cl_code FROM pra_client WHERE cl_status > 0 ";
	$result = $db->query($query );
	return $result;
}
function get_networks(){
	global $db;
	
	$query = "SELECT id_network FROM pra_network ";
	$result = $db->query($query );
	return $result;
}
 
function populate_sms(){
	
	global $db;
	global $Log;
	
	$clients = get_clients();
	$codes = get_codes();
	
	
	
	$query = "INSERT INTO TBL_SMS_LOG ( LOCAL_DATE, LOCAL_TIME, STATUS, FIID, ID_BROKER,PCODE ) "
			. " VALUES ( TO_DATE(:lcl_date, 'yyyy-mm-dd hh24:mi'), :lcl_time, :status, :fiid, :broker, :code ) ";
	$Log->write_log('Starting SMS');
	foreach ($clients as $k => $cl) {
		$values = array(
					':lcl_date' => date('Y-m-d H:i'),
					':lcl_time' => time(),
					':status' => 1,
					':fiid' => $cl['CL_CODE'],
					':broker' => ( (rand(0,100)%10) <= 7 ) ? "P" : "A"
				);
		$values2 = array(
					':lcl_date' => date('Y-m-d H:i'),
					':lcl_time' => time(),
					':status' => 1,
					':fiid' => $cl['CL_CODE'],
					':broker' => ( (rand(0,100)%10) <= 7 ) ? "P" : "A"
				);
		$resp = $db->execute( $query, $values );
		if ( $resp === FALSE ){
			$Log->write_log("Ocurrió un error al crear el registro de tabla SMS. " . print_r($db->error[(count($db->error) - 
1)], true ));
		} else {
			$Log->write_log(" SMS Line written. " . date('d') . "-" . date('H') . "-".$cl['CL_CODE']. "-". $values[':broker']);
		}
		$resp = $db->execute( $query, $values2 );
		if ( $resp === FALSE ){
			$Log->write_log("Ocurrió un error al crear el registro de tabla SMS. " . print_r($db->error[(count($db->error) - 
1)], true ));
		} else {
			$Log->write_log(" SMS Line written. " . date('d') . "-" . date('H') . "-".$cl['CL_CODE']. "-". 
$values2[':broker']);
		}
		
		foreach ($clients as $k => $cl) {
			
			$id_cliente = $cl['CL_CODE'];
			
			for ($i=0; $i <= 9 ; $i++) {
				$code = $i;
				$values = array(
					':lcl_date' => date('Y-m-d H:i'),
					':lcl_time' => time(),
					':status' => 1,
					':fiid' => $cl['CL_CODE'],
					':broker' => ( (rand(0,100)%10) <= 7 ) ? "P" : "A",
					':code'	=> $code
				);
				
				$resp = $db->execute( $query, $values );
				if ( $resp === FALSE ){
					$Log->write_log("Ocurrió un error al crear el registro de tabla $table. " . 
print_r($db->error[(count($db->error) - 1)], true ));
				} else {
					$Log->write_log(" SMS Line written. " . date('Y-m-d H:i') . "-".$code. "-". $values[':broker']);
				} 
			} 
			
			foreach ($codes as $k => $cod) {
				
				if ( (rand(0,100)%10) < 1 ){
					
					$code = $cod['CODE'];
					$values = array(
						':lcl_date' => date('Y-m-d H:i'),
						':lcl_time' => time(),
						':status' => 1,
						':fiid' => $cl['CL_CODE'],
						':broker' => ( (rand(0,100)%10) <= 7 ) ? "P" : "A",
						':code'	=> $code
					);
					
					$resp = $db->execute( $query, $values );
					if ( $resp === FALSE ){
						$Log->write_log("Ocurrió un error al crear el registro de tabla $table. " . 
print_r($db->error[(count($db->error) - 1)], true ));
					} else {
						$Log->write_log(" SMS Line written. " . date('Y-m-d H:i') . "-".$code. "-". 
$values[':broker']);
					}  
					
				} 	
			} 	
		} 	
	}
	$Log->write_log('Finished SMS');
}
$date = date( "Ym"); echo "<pre>"; $exists = FALSE; $query = "SELECT * FROM TBL_SMS_LOG "; $rsp = $db->query($query ); if ( $rsp !== 
FALSE){
	if ( count( $rsp ) > 0 ){
		$exists = TRUE;
	} 
}
if ( !$exists ){
	$query = " CREATE TABLE APP.TBL_SMS_LOG ( PAN VARCHAR2(19), PHONE VARCHAR2(15), PHONE_NAME VARCHAR2(25), SKY VARCHAR2(15), "
		. " EMAIL VARCHAR2(50), AMOUNT NUMBER(26,8), PCODE VARCHAR2(6), TXN_TYPE CHAR(1), LOCAL_DATE DATE, LOCAL_TIME NUMBER, "
		. " TRACE NUMBER(4), REFNUM VARCHAR2(12), AUTHNUM VARCHAR2(6), TERMID VARCHAR2(8), ACCEPTORNAME VARCHAR2(42), STATUS 
VARCHAR2(5), "
		. " IDMSG VARCHAR2(16), TIPOMSG CHAR(1), FIID VARCHAR2(4), FECH_REG DATE, ID_BROKER CHAR(1), TMSG_ENV CHAR(1), NMSGS 
NUMBER(25), "
		. " CVE_TRX VARCHAR2(20), NAT_CONT VARCHAR2(20), PREFIJO VARCHAR2(20), TIPO_TH VARCHAR2(4), USER_ID VARCHAR2(10), 
NOMBRE_USUARIO VARCHAR2(45)); ";
	$resp = $db->execute( $query );


	$query = "INSERT INTO APP.pra_client ( id_client, cl_client, cl_code, cl_status, cl_timestamp ) "
		. " VALUES ( 1, 'B003', 'B003',1,1 )";
	$resp = $db->execute( $query );

}


populate_sms();
echo "</pre>";
?>
