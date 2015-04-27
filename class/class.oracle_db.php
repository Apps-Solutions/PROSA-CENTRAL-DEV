<?php

/*****************************************
 * oracle_db
 * Clase de Conexión a BD Oracle.
 * 
 * @since 0.2
 * @author Manuel Fernández <manuel.fernandez@gruposellcom.com> 
 * 
 * ****************************************/ 
class oracle_db{
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
			$this->host = "" . ORCL_HOST . ""; //Se declara el Host de la BD
			$this->sid = "( DESCRIPTION = (ADDRESS_LIST =(ADDRESS =(PROTOCOL = TCP)"
					. "(Host = " . ORCL_HOST . ")(Port = " . ORCL_PORT . ") ) )"
					. "(CONNECT_DATA = (SID = " . ORCL_SERVICE_NAME . ")))"; 
			//$this->sid="(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=" . ORCL_HOST . ")(PORT="  . ORCL_PORT . ")))(CONNECT_DATA=(SID=" . ORCL_SERVICE_NAME . ")))";
			$this->connection = ocilogon(ORCL_USER_NAME,ORCL_PASSWORD,$this->sid, 'AL32UTF8') 
									or die ( "Ocurrió un problema al intentar conectarse con el Servidor de BBDD. ");//username,password,sid
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
	 * @param 	$query_str 	String: Query que se ejecutará en la Base de Datos
	 * @param	$values 	Array: Arreglo de valores para cargar 
	 * @return	String 	-> false si existio error
	 *					-> true  si la consulta se realizao con éxito 
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
	 * @return	 	String: regresa el primer registro del arreglo
	 */ 
	function get_value() {
		$result = $this->get_array(0);
		return $result[0];
	}

	/**
	 * Regresa el siguiente valor del (@link $campo) en la (@link $tabla)
	 * @param 	$tabla 		String: Nombre de la tabla de referencia
	 * @param	$campo 		String: Nombre del campo a incrementar
	 * @param 	$incremento	Integer: salto de incremento
	 * @return	String 	-> false si existio error
	 *					-> true  si la consulta se realizao con éxito
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
	 * @param	$db 		String: Base de Datos a la que se consultará
	 * @return	String 	-> false si existio error
	 *					-> true  si la consulta se realizao con éxito
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
	 * @param 	$i 		Integer: Índice del campo 
	 * @return			String: Nombre del campo 	
	 */ 
	function get_field_name( $i ){
		return ocicolumnname( $this->query, $i+1 );
	}
 
	/**
	 * Regresa el número total de campos  
	 * @return			Integer: Número de campos 	
	 */ 
	function get_num_cols() {
		return @ocinumcols( $this->query );
	}

	/**
	 * Regresa el tipo de campo
	 * @param 	$i 		Integer: Índice del campo 
	 * @return			String: Nombre del campo 	
	 */ 
	function get_column_type( $i ){
		return ocicolumntype($this->query, $i+1);
	}
	    
	/**
	 * Regresa el total de registros 
	 * @return			Integer: Número total de registros regresados por la consulta. 	
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
	 * @param 	$error_no	Integer: Índice del error.
	 * @param	$msg		String: Mensaje de error.
	 * @return 	String: 	Mensaje de error con formato. 
	 */
	function get_error_msg( $error_no, $msg="" ){
		$log_msg = NULL;
  		$error_msg = "<b> Error:</b> <pre><font color=red>\n\t" . ereg_replace(",",",\n\t",$msg) . "</font></pre>";
  		$error_msg .= "<b><i> Error del sistema:</i></b>";
  		$error_msg .= "<font color=red><pre>";
  		foreach(ocierror($error_no) as $key=>$val){
  			$log_msg.="$key :  ".$val."\n";
  			$error_msg.="$key : $val \n";
		}
		$error_msg.="</pre></font>";
		return $error_msg;
	}
	
	/**
	 * Regresa el arreglo de errores
	 * @param	$error_no	Integer: Índice del error
	 * @return	Array: Arreglo de error	
	 */
	function get_error_array( ){
		 return oci_error($this->connection); 
	}	
	
}
?>