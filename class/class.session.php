<?php

class Session {
	var $name;
	var $hash;
	var $profile;
	var $user;
	var $email;
	var $token;
	var $id; 
	var $settings;
	
	var $services;

	function Session(){
		$this->services = array();
		$this->init();
	}

	private function init() { 
		global $Debug; 
		$this->user  = "";
		$this->name  = "";
		$this->email = "";
		$this->token = "";
		$this->profile = 0;
		$this->id = "";
		 
		if ( $this->set_from_session() ) {
            return TRUE;
		} else{
			$this->end_session();
			return FALSE;
		}
	}
	
	public function login( $user, $password = FALSE, $token = FALSE){ 
		if ( $password && $password != ''){
			/*$ldap = new LDAP(); 
			if ( !$ldap ){
				$this->set_error(   'Unable to connect to LDAP Server.', LOG_PRC_DOWN, 1 );
				throw new Exception('Unable to connect to LDAP Server.');
			}
			$login =  $ldap->login($user, $password);
			*/
		/*	if ( !$login ){ 
				return LOGIN_BADLOGIN; 
			} else {
				$info = $ldap->get_user_info( $user ); 
				//$info = TRUE;
				if ( $info ){*/ 
				 /* TODO: Check groups */
					//$groups = explode( '#',$info->memberof );
					/*$this->name 	= $info->displayname;
					$this->user 	= $info->user;
					$this->email 	= $info->email;
					$this->profile 	= $info->profile;
					
					$_SESSION[PFX_SYS . 'name']		= $this->name;
					$_SESSION[PFX_SYS . 'token']	= $this->token;
					$_SESSION[PFX_SYS . 'email']	= $this->email;
					$_SESSION[PFX_SYS . 'user']		= $this->user; 
					$_SESSION[PFX_SYS . 'profile']	= $this->profile;*/
					
					
						$_SESSION[PFX_SYS . 'name']		= $user;
					$_SESSION[PFX_SYS . 'token']	= "3adadas";
					$_SESSION[PFX_SYS . 'email']	= "cservin@qqq.com";
					$_SESSION[PFX_SYS . 'user']		= $user; 
					$_SESSION[PFX_SYS . 'profile']	= "1";
					if ( $this->profile == 1 ){
						define('IS_ADMIN', TRUE  );
					} else {
						define('IS_ADMIN', FALSE );
					}
					session_write_close();
					$this->set_msg( "Login successful user $user." );
					return LOGIN_SUCCESS;
				/*} else {
					return LOGIN_BADLOGIN;
				} 
			} */
		} /*else {
			return LOGIN_BADLOGIN;
		}*/
	}
	
	private function set_from_session(){ 
		if (
			//isset($_SESSION[ PFX_SYS . 'email']) 	&& ($_SESSION[ PFX_SYS . 'email'] != "") &&
			isset($_SESSION[ PFX_SYS . 'profile']) 	&& ($_SESSION[ PFX_SYS . 'profile'] != "") &&
			isset($_SESSION[ PFX_SYS . 'user']) 	&& ($_SESSION[ PFX_SYS . 'user'] != "") &&
			//isset($_SESSION[ PFX_SYS . 'token']) 	&& ($_SESSION[ PFX_SYS . 'token'] != "") &&
			isset($_SESSION[ PFX_SYS . 'name']) 	&& ($_SESSION[ PFX_SYS . 'name'] != "")
		) { 
            $this->name 	= $_SESSION[ PFX_SYS . 'name'];
			$this->profile 	= $_SESSION[ PFX_SYS . 'profile'];
			$this->user 	= $_SESSION[ PFX_SYS . 'user'];
			$this->token 	= $_SESSION[ PFX_SYS . 'token'];
			$this->email 	= $_SESSION[ PFX_SYS . 'email']; 
			if ( $this->profile == 1 ){
				define('IS_ADMIN', TRUE);
			} else {
				define('IS_ADMIN', FALSE);
			}
			return TRUE;
		} else { 
			return FALSE;
		}
	}
	
	private function set_from_cookie(){
		if (
			isset($_COOKIE[PFX_SYS . 'user'] ) && $_COOKIE[PFX_SYS . 'user']  != '' && 
			isset($_COOKIE[PFX_SYS . 'token']) && $_COOKIE[PFX_SYS . 'token'] != '' 
		) {
			$us_usuario	= $_COOKIE[PFX_SYS . 'usr'];
			$us_password= $_COOKIE[PFX_SYS . 'token'];
			if ($this->create_session($us_usuario , $us_password)){ 
				if (stripos( $_SERVER['SCRIPT_NAME'] , SYS_LOGIN ) > 0)  header('location: index.php');
				//cookie activa y la sesion válida
			}  else header('location: '.SYS_LOGIN); 
		} 
	}
	
	private function create_session( $user, $password ){
		
	}
	 
	public function get_user_client(){
		if ( $this->logged_in() ){
			$fiid = substr($this->user,0, 4); 
			$query = "SELECT id_client FROM " . PFX_MAIN_DB . "client WHERE cl_code = :code ";
			//$db = new oracle_db();
			$db = new PDOMySQL();
			$result = $db->query( $query, array(':code' => $fiid ) ); 
			if ( $result !== FALSE ){
				return $result[0]['ID_CLIENT'];
			} else {
				$this->set_error("Ocurrió un error al obtener el ID del Cliente del usuario ( " . $this->user . " ).", ERR_DB_QRY);
				return FALSE;
			} 
		} 
		else return FALSE;
	}
	
	public function logged_in() {
		return ($this->user != "");
	} 
	
	public function get_name() {
		return $this->name;
	}
	
	public function get_profile() {
		return $this->profile;
	}
	
	public function get_level() {
		return $this->profile;
	}

	public function get_user() {
		return $this->user;
	}

	public function get_email() {
		return $this->user;
	}
	
	public function get_token() {
		return $this->token;
	}
	
	public function get_id() {
		return $this->id;
	}

	public function get_var( $varname ) {
		return ( isset($_SESSION[$varname]) ? $_SESSION[$varname] : "" );
	}
	
	public function set_var( $varname, $value ) {
		$_SESSION[$varname] = $value;
	}
	
	public function is_admin(){
		//return ($this->profile == 1); //original
		return ($_SESSION[PFX_SYS . 'profile'] == 1);
	}
 
	public function end_session() {
		$_SESSION[PFX_SYS . 'name'] 	= "";
		$_SESSION[PFX_SYS . 'user'] 	= "";
		$_SESSION[PFX_SYS . 'email'] 	= "";
		$_SESSION[PFX_SYS . 'token'] 	= "";
		$_SESSION[PFX_SYS . 'profile'] 	= 0; 
		
		session_destroy();
		session_start();
		
		$this->user = "";
		$this->name = "";
		$this->profile = 0;
		$this->email = "";
	}
	
	public function get_services(){
		global $obj_bd;
		if ( count($this->services) == 0 ){
			if ( $this->logged_in() ){ 
				if ( $this->profile == 1 ){
					$query = "SELECT * FROM " . PFX_MAIN_DB . "service ORDER BY se_order " ;
					$params = FALSE;
				}else {
					$query = "SELECT * FROM " . PFX_MAIN_DB . "service "
							. " INNER JOIN " . PFX_MAIN_DB . "service_user ON su_se_id_service = id_service "
						. " WHERE su_user = :su_user ORDER BY se_order ";
					$params = array( ':su_user' => $this->user ); 
				} 
				//$db = new oracle_db(); 
				//$db = new PDOMySQL();
				$services = $obj_bd ->query( $query, $params ); 
				if ( $services ){
					$this->services = $services;
					return $services;
				} else {
					$this->services = array(); 
					$this->set_error( "Ocurrió un error al consultar la BBDD. " . $db->error[0], ERR_DB_QRY, 1 );
					return FALSE;
				}
			}
		}
		return $this->services;
	}
	
	public function build_menu(){
		
		$services = array(); 
		$menu = array(array( 	"cmd" => HOME, 		"prf" => array(1,2,3 ), 	"lbl" => "Inicio", 	"ico" => "fa-home", 	"lnk" => array() 	)); 
		$permit = array();
		$serv = $this->get_services();
		foreach ($serv as $k => $s) {
			$permit[] = $s['id_service'];
		}
		
		if ( in_array(7, $permit) )
			$services[] = array( "cmd" => SRV_POS, 					'prf' => array(1,2,3),	"lbl" => "POS", "ico" => "", 	"lnk" => array() );
		
		if ( in_array(8, $permit) )
			$services[] = array( "cmd" => SRV_ATM, 					'prf' => array(1,2,3),	"lbl" => "ATM", "ico" => "", 	"lnk" => array() );
		
		if ( in_array(6, $permit) )
			$services[] = array( "cmd" => SRV_CARGOS_AUTOMATICOS,	'prf' => array(1,2,3),	"lbl" => "Cargos Automáticos", 		"ico" => "",	"lnk" => array() );
		
		if ( in_array(9, $permit) )
			$services[] = array( "cmd" => SRV_MULTISERV, 			'prf' => array(1,2,3),	"lbl" => "Multiserv",			 	"ico" => "",	"lnk" => array() );
		
		if ( in_array(1, $permit) )
			$services[] = array( "cmd" => SRV_PAGOS_DIFERIDOS,		'prf' => array(1,2,3),	"lbl" => "Pagos Diferidos",			"ico" => "",	"lnk" => array() );
		
		if ( in_array(3, $permit) )
			$services[] = array( "cmd" => SRV_PAYWEAR_ONLINE,		'prf' => array(1,2,3),	"lbl" => "Payware Online",			"ico" => "",	"lnk" => array() );
		
		if ( in_array(9, $permit) )
			$services[] = array( "cmd" => SRV_PREA,		 			'prf' => array(1,2,3),	"lbl" => "Preautorizador (PREA)",	"ico" => "",	"lnk" => array() );
		
		if ( in_array(5, $permit) )
			$services[] = array( "cmd" => SRV_PROCOM,				'prf' => array(1,2,3),	"lbl" => "PROCOM",					"ico" => "",	"lnk" => array() );
		
		if ( in_array(10, $permit) )
			$services[] = array( "cmd" => SRV_SMS, 					'prf' => array(1,2,3),	"lbl" => "SMS", 					"ico" => "", 	"lnk" => array() );
		
		if ( in_array(10, $permit) )
			$services[] = array( "cmd" => SRV_SWITCH_ABIERTO,		'prf' => array(1,2,3),	"lbl" => "Switch Abierto",			"ico" => "",	"lnk" => array() );
		
		$menu = array(
			array( 	"cmd" => HOME, 		"prf" => array(1,2,3 ), 	"lbl" => "Inicio", 	"ico" => "fa-home", 	"lnk" => array() 	),
			array(  
				"cmd" => "#",
				"lbl" => "Servicios", 
				"prf" => array(1),
				"ico" => "fa-bar-chart-o",
				"lnk" => $services
			) 
		);
		
		if ( $this->is_admin() ){
			$menu[] =  array(  
				"cmd" => "#",
				"lbl" => "Notificaciones", 
				'prf' => array(1,2,3),
				'ico' => "fa-exclamation-circle", 
				"lnk" => array(
							array( "cmd" => NTF_FRM_ENVIO,	 	'prf' => array(1),		"lbl" => "Envío",			"ico" => "fa-envelope",		"lnk" => array() ),
							array( "cmd" => NTF_FRM_THRESHOLD, 	'prf' => array(1,)	,	"lbl" => "Umbrales",		"ico" => "fa-cogs",			"lnk" => array() ), 
							array( "cmd" => NTF_HISTORY,		'prf' => array(1,2,3),	"lbl" => "Historial",		"ico" => "fa-clock-o",	 	"lnk" => array() ) 
						 )
			);
			$menu[] = array( "cmd" => LST_CLIENT, 	'prf' => array(1),	"lbl" => "Clientes", 			"ico" => "fa-users",	"lnk" => array() );
			$menu[] = array( "cmd" => LST_ADM_USER,	'prf' => array(1),	"lbl" => "Usuarios PROSA", 		"ico" => "fa-user",		"lnk" => array() ); 
		}
		else {
			$menu[] = array( "cmd" => LST_USER, 		'prf' => array(1),	"lbl" => "Usuarios",  		"ico" => "fa-user",		 	"lnk" => array() );
		}
		
		$config = array( 'cmd' => 'root', 'lnk' => $menu );
		
		return $config;
	}
	
	public function has_service( $id_service ){ 
		if ( $this->profile == 1 )
			return TRUE;
		
		$services = $this->get_services();
		foreach ($services as $k => $s){
			if ( $id_service == $s['id_service'] )
				return TRUE;
		}
		return FALSE;
	}
	
	protected function set_error( $err, $type, $lvl = 1 ){
		global $Log;
		$this->error[] = $err;
		$Log->write_log(  " ERROR @ Class Session: " . $err , $type, $lvl);
	} 
	
	protected function set_msg( $msg , $echo = '' ){
		global $Log;
		global $mensaje;
		$Log->write_log( " MSG @ Class Session: " . $msg );
		if ( $echo != '') $mensaje .= $echo . " <br/> ";
	}
	
}
?>
