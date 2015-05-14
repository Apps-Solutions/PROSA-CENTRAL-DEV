<?php
require_once 'class.api.php';
require_once 'class.ldap.php';
//ini_set('display_errors', TRUE);

class prosaApi extends api{
    protected $User;
	protected $bd;
	protected $ldap;
	public $error;
	
    public function __construct($request, $origin) {
    	global $obj_bd;
        parent::__construct($request);
		/*
		$this->ldap = new LDAP();
		if ( !$this->ldap ){
			$this->set_error(   'Unable to connect to LDAP Server.', LOG_PRC_DOWN, 1 );
			throw new Exception('Unable to connect to LDAP Server.');
		}
		*/
		//$this->db = new oracle_db();
		$this->db = new PDOMySQL();  
		if ($this->request['request'] != 'login'){ 
			if (array_key_exists('token', $this->request) && !$this->check_token()){
				$this->set_error( 'Invalid User Token. ', LOG_SESS_ERR, 1 );
	            throw new Exception('Invalid User Token');
	        }
		} 
    }
	
	/**
	 * Login
	 */
	 protected function login(){ 
	 	if ($this->method == 'POST'){ 
	 		if (!array_key_exists('user', $this->request)) {
	 			$this->set_error(   'No User Provided.' , LOG_API_ERR, 1); 
	            throw new Exception('No User provided.');
	        } else if (!array_key_exists('password', $this->request)) {
	        	$this->set_error(   'No Password provided. ', LOG_API_ERR, 1 ); 
	            throw new Exception('No Password provided.');
	        } 
			
			$user	= $this->request['user'];
			$pwd	= $this->request['password']; 
		//	$login = $this->ldap->login($user, $pwd); 
			$login = TRUE;
			if ( !$login ){
		//		return array('success' => FALSE, 'resp' => $this->ldap->error[0]);
			} 
			else { 
				$resp = $this->set_user_info($user); 
				if ( $resp ){ 
					$token = $this->set_token();
					if ($token !== FALSE)
						return array('success' => TRUE, 'resp' => 'OK', 'token' => $token);
					else{
						$this->set_error( "ERROR: An error occured while generating a Token.", LOG_SESS_ERR, 3); 
						return array('success' => FALSE, 'resp' => "ERROR: An error occured while generating a Token.");
				 	} 
				} else {
					$this->set_error( "ERROR: User doesn't have App Priviledges.", LOG_SESS_ERR, 2); 
					return array('success' => FALSE, 'resp' => "ERROR: User doesn't have App Priviledges.");
				} 
			
			}
	 	} else {
	 		$this->set_error( 'ERROR: Use POST to Login. ', LOG_API_ERR, 3 ); 
			return array('success' => FALSE, 'resp' =>"ERROR: Use POST to Login.");
        }
	 }
	
	/**
	 * logout()
	 * 
	 * @return 	Array on success; FALSE otherwise
	 */
	protected function logout(){
		global $obj_bd; 
		$query = "UPDATE " . PFX_MAIN_DB . "token SET tk_timestamp = :timestamp, tk_token_apple = :apple "
							. " WHERE tk_user = :tk_user " ;
		$params = array( ':timestamp' => 0, ':tk_user' => $this->User->user, ':apple' => '' ); 
		
		$resp = $obj_bd->execute($query,$params); 
		if ( !$resp ) {
			$this->set_error( "An error occured while updating the token. ", LOG_DB_ERR, 3 ) ;
			return FALSE;
		}
		else{  
			return array('success' => TRUE, 'resp' => 'OK' );
		} 
	}
	 
	/**
	  * Check Token
	  */
	protected function check_token(){
		global $obj_bd;
 		if (!array_key_exists('user', $this->request)) {
 			$this->set_error('No User (user) Provided', LOG_API_ERR, 3);
            throw new Exception('No User (user) Provided');
        } else if (!array_key_exists('token', $this->request) && $this->request['token'] != '') {
            $this->set_error('No Token (token) provided.', LOG_API_ERR, 3);
            throw new Exception('No Token (token) provided.');
        } 
		$user 	= $this->request['user'];
		$token 	= $this->request['token'];
		
		$query  = "SELECT * FROM " . PFX_MAIN_DB . "token WHERE tk_user = :us_user  AND tk_token_prosa = :token ";
		$result = $obj_bd->query( $query, array( ':us_user' => $user, ':token' => $token ) ); 
		if ( $result === FALSE ) {
			$this->set_error( 'An error occured while querying the DB for the token. ', LOG_DB_ERR, 3 );
			throw new Exception('Database error.');
		} 
		 
		if ( count( $result ) < 1 ) { 
			return FALSE;
		}  
		$record = $result[0]; 
		if ( !$record ){ 
			return FALSE;
		} 
		if ($record['TK_TIMESTAMP'] > 0 && $record['TK_TIMESTAMP'] > time() ){
			return $this->set_user_info( $record['TK_USER'] ); 
		} else { 
            throw new Exception('Session has expired.');
		}
	}

	protected function renew_token(){
		global $obj_bd;
		if ($this->User->user != ''){
			
			$query = "UPDATE " . PFX_MAIN_DB . "token SET tk_timestamp = :timestamp "
							. " WHERE tk_user = :user " ;
			$params = array( ':timestamp' => ( time() + (86400 * 20) ), ':user' => $this->User->user );
			
			if ( $result = $obj_bd->execute( $query, $params ) ) {
				$this->set_error("An error occured while saving the token. ", LOG_DB_ERR, 3  );
				return FALSE;
			}
			else{  
				return TRUE; 
			}
		} 
	}
	 
	protected function set_token(){
		global $obj_bd; 
		if ($this->User->user != ''){
			$token = md5( 	"PROSA#" .
							$this->User->user . "#" . 
							$this->User->name . "#" .
							date('YmdHis') );
			$query  = "SELECT * FROM " . PFX_MAIN_DB . "token WHERE tk_user = :id_user ";
			$params = array( ':id_user' => $this->User->user );
			 
			$result = $obj_bd->query( $query, $params ); 
			
			if ( $result === FALSE ){  
				$this->set_error( 'An error occured while querying the DB for the token. ', LOG_DB_ERR, 3 ); 
				throw new Exception('Database Error.');
			}  
			if (count($result) < 1 ) { 
				$query = "INSERT INTO " . PFX_MAIN_DB . "token ( tk_user, tk_token_prosa, tk_timestamp ) VALUES ( :tk_user, :tk_token, :tk_timestamp) ";
			} else {
				$query = "UPDATE " . PFX_MAIN_DB . "token SET tk_token_prosa = :tk_token, tk_timestamp = :tk_timestamp WHERE tk_user = :tk_user " ; 
			}
			  
			$params = array( ':tk_user' => $this->User->user, ':tk_token' => $token, ':tk_timestamp' => (time() + (86400 * 20) ) );
			 
			$result = $obj_bd->execute($query, $params); 
			if ( !$result ) { 
				$this->set_error("An error occured while saving the token. " , LOG_DB_ERR, 3  );
				return FALSE;
			}
			else{  
				return $token; 
			}
		} else {
			$this->set_error('Session error: user not logged in.', LOG_SESS_ERR, 3);
			throw new Exception('Session error: user not logged in.');
		}
	}

	protected function set_apple_token( ){
		global $obj_bd;
		if ( $this->User->user != '' ){
			if (!array_key_exists('apple_token', $this->request) && $this->request['apple_token'] == '') {
	            $this->set_error('No Apple Token (token) provided.', LOG_API_ERR, 3);
	            throw new Exception('No Apple Token (token) provided.');
	        }
			
	        $token= $this->request['apple_token']; 
	        $query  = "SELECT * FROM " . PFX_MAIN_DB . "token WHERE tk_user = :us_user ";
			 
			$result = $obj_bd->query( $query , array(':us_user' =>  $this->User->user  ));
			
			if ( !$result ){ 
				$this->set_error( 'An error occured while querying the DB for the token. ', LOG_DB_ERR, 3 );
				throw new Exception('Database Error.' );
			} 
			if (count($result) < 1 ) {
				return array('success' => FALSE, 'resp' => "User has not logged in yet.");
			} else {
				$query = "UPDATE " . PFX_MAIN_DB . "token SET tk_token_apple= :tk_token, tk_timestamp = :tk_timestamp WHERE tk_user = :tk_user " ;
				$params = array( ':tk_user' => $this->User->user, ':tk_token' => $token, ':tk_timestamp' => (time() + (86400 * 20) ) ); 
				$result = $obj_bd->execute($query, $params); 
				if ( !$result ) {
					$this->set_error("An error occured while saving the token. " , LOG_DB_ERR, 3  );
					return array('success' => FALSE, 'resp' => "A Database Error occurred.");
				}
				else{  
					return array('success' => TRUE, 'resp' => "OK");
				} 
			}  
		}
	}
	
	protected function get_alerts(){
		global $obj_bd;
		 if ($this->User->user != ''){
		 	if ( array_key_exists('id_service', $this->request) && $this->request['id_service'] > 0) {
	            $id_service = $this->request['id_service'];
	        } else {
	        	$id_service = 0;
	        }
			
			if ( $this->User->profile == 1 ){
				$query = "SELECT * FROM " . PFX_MAIN_DB . "alert " 
						. ( $id_service  > 0 ?  " WHERE al_se_id_service = :id_service " : "") 
						. " ORDER BY al_timestamp DESC ";
				$parmas = array( ':id_service' => $id_service ) ;
			}
			else{ 
				$id_client = $this->get_user_client($this->User->user); 
				$query  = "SELECT * FROM " . PFX_MAIN_DB . "alert "    
					. " WHERE al_cl_id_client = :id_client "
						. ( $id_service  > 0 ? " AND al_se_id_service = :id_service " : "") 
					. " ORDER BY al_timestamp DESC ";
				$params = array( ':id_client' => $id_client, ':id_service' => $id_service ); 
			}
			$query = "SELECT * FROM ( " . $query . " )  WHERE rownum <= 50 ";
			$result = $obj_bd->query( $query, $params ); 
			if ( $result === FALSE ){  
				$this->set_error( 'An error occured while querying the DB for the services. ', LOG_DB_ERR, 3 ); 
				throw new Exception('Database Error.' );
			} else {
				$alerts = array();
				foreach ($result as $k => $se) {
					$ale = array(); 
					$ale['id_alert'] 	= $se['ID_ALERT'];
					$ale['id_service'] 	= $se['AL_SE_ID_SERVICE']; 
					$ale['text']		= $se['AL_TEXT'];
					$ale['date']		= date('d/m/Y',$se['AL_TIMESTAMP']);
					$ale['time']		= date('H:i:s',$se['AL_TIMESTAMP']); 
					$alerts[] = $ale;
				}
				return array('success' => TRUE, 'resp' => "OK", 'alerts' => $alerts );
			}
		}
	} 
	
	protected function get_services(){
		global $obj_bd; 
		if ($this->User->user != ''){
/*			if ( $this->User->profile == 1 ){
				$query = "SELECT * FROM " . PFX_MAIN_DB . "service ORDER BY se_order ";
/*				$parmas = FALSE;
			}
			else{
*/				$query  = "SELECT * FROM " . PFX_MAIN_DB . "service_user " 
						. " INNER JOIN " . PFX_MAIN_DB . "service ON id_service = su_se_id_service "  
					. " WHERE su_user = :su_user ORDER BY se_order ";
				$params = array( ':su_user' => $this->User->user ); 
//			}
			$result = $obj_bd->query( $query, $params ); 
			
			if ( $result === FALSE ){  
				$this->set_error( 'An error occured while querying the DB for the services. ', LOG_DB_ERR, 3 ); 
				throw new Exception('Database Error.');
			} else {
				$services = array();
				foreach ($result as $k => $se) {
					$srv = array();
					$srv['id_service'] 	= $se['ID_SERVICE'];
					$srv['service'] 	= utf8_decode($se['SE_SERVICE']);
					$srv['command']		= $se['SE_COMMAND'];
					$srv['order']		= $se['SE_ORDER'];
					$services[] = $srv;
				}
				return array('success' => TRUE, 'resp' => "OK", 'services' => $services );
			}
		} 
	}

	protected function get_indicator(){
		global $obj_bd;
		if ( $this->User->user != '' ){
			if (!array_key_exists('id_service', $this->request) && $this->request['id_service'] > 0) {
	            $this->set_error('Invalid service.', ERR_VAL_INVALID, 3);
	            throw new Exception('Invalid service.');
	        }
			
			$id_service = $this->request['id_service'];
			if ( $this->User->profile != 1 ){
				$query = "SELECT * FROM " . PFX_MAIN_DB . "service_user "
						. " WHERE su_us_id_user = :user AND su_se_id_service = :id_service ";
				$resp = $obj_bd->query( $query, array( ':user' => $this->User->user, ':id_service' => $id_service ) );
				if ( $resp ){
					$this->set_error('An error occured while querying the DB for permitted services.', LOG_API_ERR, 3);
	           		throw new Exception('Database Error.');
				} else if (count( $resp ) == 0){
					$this->set_error("Restricted service ($id_service) for user ( " .$this->User->user . ").", LOG_API_ERR, 3);
	           		throw new Exception('User does not have permission.');
				} else { 
					$client = $this->get_user_client();
				} 
			} else {
				if (!array_key_exists('id_client', $this->request) && $this->request['id_client'] > 0) {
		            $id_client = 0;
		        } else {
		        	$id_client = $this->request['id_client'];
		        } 
			}
			
			require_once 'class.service.php';
			switch ($id_service) {
				case 1: 
					require_once 'class.PagosDiferidos.php';
					$service = new PagosDiferidos( $id_client );
					break; 
				case 2: 
					require_once 'class.PREA.php';
					$service = new PREA( $id_client );
					break; 
				case 3:
					require_once  'class.Payware.php';
					$service = new Payware( $id_client ); 
					break; 
				case 4:
					require_once  'class.SwitchAbierto.php';
					$service = new SwitchAbierto( $id_client ); 
					break;
				case 5:
					require_once  'class.PROCOM.php';
					$service = new PROCOM( $id_client ); 
					break;
				case 6:
					require_once  'class.CargosAutomaticos.php';
					$service = new CargosAutomaticos( $id_client ); 
					break;
				case 7:
					require_once  'class.POS.php';
					$service = new POS( $id_client ); 
					break;
				case 8:
					require_once  'class.ATM.php';
					$service = new ATM( $id_client ); 
					break;
				case 9:
					require_once  'class.Multiserv.php';
					$service = new Multiserv( $id_client ); 
					break;
				case 10:
					require_once  'class.SMS.php';
					$service = new SMS( $id_client ); 
					break;
				default:
					$resp["success"] = FALSE;
					$resp["resp"] = "Invalid service.";
					return $resp;
					break;  		
			}
			
			$indicator = $service->get_array(); 
			if ( count($service->error) > 0 ){
				$resp["success"] = FALSE;
				foreach ($service->error as $k => $err) {
					$resp["error"] = $err; 
				}
			} else {
				$resp["success"] = TRUE;
				$resp["resp"] = "OK"; 
			}
			$resp["indicator"] = $indicator; 
			return $resp; 
		}
	}

	protected function get_clients(){
		global $obj_bd;
		if ($this->User->user != ''){
			if ( $this->User->profile == 1 ){
				$query = "SELECT * FROM " . PFX_MAIN_DB . "client ORDER BY id_client ";
			}
			else{
				$this->set_error( 'Restricted action. ', SES_RESTRICTED_ACTION, 3 ); 
				throw new Exception('Restricted action.'); 
			}
			$result = $obj_bd->query( $query  ); 
			
			if ( $result === FALSE ){  
				$this->set_error( 'An error occured while querying the DB for the clients. ', LOG_DB_ERR, 3 ); 
				throw new Exception('Database Error.');
			} else {
				$clients = array();
				foreach ($result as $k => $se) {
					$srv = array();
					$srv['id_client'] 	= $se['ID_CLIENT'];
					$srv['client'] 		= $se['CL_CLIENT'];
					$srv['code'] 		= $se['CL_CODE'];
					$clients[] = $srv;
				}
				return array('success' => TRUE, 'resp' => "OK", 'clients' => $clients );
			}
		} 
	}
	 
	protected function set_user_info( $user ){
	/*	$info = $this->ldap->get_user_info( $user );
	  	if ( $info ){/* TODO: Check groups */ 
	 		
			//$groups = explode( '#',$info->memberof ); 
			$this->User = new stdClass;
			$this->User->user	= $user;// $info->user;
			$this->User->email 	= $user;//$info->email;
			$this->User->name 	= $user;//$info->displayname;
			$this->User->groups	= $user;//$info->memberof;  
			$this->User->profile= 1; //$info->profile;  
			return TRUE;  
	/*		
		} else {
			$this->set_error( "ERROR: User doesn't have App Priviledges.", LOG_SESS_ERR, 2); 
			return FALSE;
		}
	 * 
	 */ 
	} 
	
	protected function get_user_client( $user ){
		global $obj_bd; 
		$fiid = substr($user,0, 4); 
		$query = "SELECT id_client FROM " . PFX_MAIN_DB . "client WHERE cl_code = :code ";
		$result = $obj_bd->query( $query, array(':code' => $fiid ) ); 
		if ( $result !== FALSE ){
			return $result[0]['ID_CLIENT'];
		} else {
			$this->set_error("Ocurrió un error al obtener el ID del Cliente del usuario ( " . $this->user . " ).", ERR_DB_QRY);
			return FALSE;
		}
	}	
}
?>
