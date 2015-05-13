<?php
if ( !class_exists('LDAP_HOST'))
	require_once 'class.ldap_host.php';

class LDAP{
	
	protected $host = FALSE; 
	protected $connection; 
	protected $groups 		= array();
	protected $admin_groups = array();
	protected $bind = FALSE;
	
	protected $dns = array();
	
	public 	  $error = array();
	
	function LDAP(){
		 if (!function_exists('ldap_connect')) {
		 	$this->set_error("No LDAP Support on server.", LOG_PRC_DOWN, 3 );
            throw new Exception("No LDAP Support on server.");
			die(); 
        } 
		 
	//	$this->groups = array( LDAP_GROUP_EMPLEADOS, LDAP_GROUP_BECARIOS, LDAP_GROUP_CATERING, LDAP_GROUP_COMUNICACION, LDAP_GROUP_GENERAL, LDAP_GROUP_PROPERTY, LDAP_GROUP_EXTERNOS);
	//	$this->admin_groups = array( LDAP_GROUP_CATERING, LDAP_GROUP_COMUNICACION, LDAP_GROUP_GENERAL, LDAP_GROUP_PROPERTY, LDAP_GROUP_EXTERNOS );
		
		$this->dns = array(
							'profiles'	=> "ou=APP_MNO,ou=Aplicaciones,o=prosa.com.mx,o=isp",
							'prosa'		=> "o=GenteProsa,o=prosa.com.mx,o=isp",
							'clients'	=> "o=Bancos,o=clientes,o=prosa.com.mx,o=isp"
						);
		
		$this->connect(); 
	} 
	
	protected function connect(){ 
		try {
			$host = new LDAP_HOST();
			$this->connection = ldap_connect( $host->host , $host->port);
			if ( $this->connection ){
				ldap_set_option($this->connection, LDAP_OPT_PROTOCOL_VERSION,  	3);
				ldap_set_option($this->connection, LDAP_OPT_REFERRALS, 		 	0);
				ldap_set_option($this->connection, LDAP_OPT_NETWORK_TIMEOUT,  	10);
				ldap_set_option($this->connection, LDAP_OPT_TIMELIMIT, 			20); 
				$this->host			= $host; 
				//$this->bind($this->host->user, $this->host->pwd);
				return TRUE;
			} else {
				$this->set_error("LDAP Server unreacheble.", LOG_PRC_DOWN, 3 );
				return FALSE; 
			} 
		} catch (Exception $e){
		 	$this->set_error("Could not stablish a connection with the LDAP Server.", LOG_PRC_DOWN, 3 ); 
			return FALSE;
		}
	}

	protected function bind( $user , $pwd ){  
		return ldap_bind($this->connection, $user, $pwd); 
	} 
	 
	
	public function get_user_info( $user, $dn = FALSE ){
		$filter	="(uid=" . $user . ")";
		$dn = $dn ? $dn : $this->host->baseDN;
		$attr 	= array("dn", "uid", "cn", "mail", "sn" );
		$result = array();
		try {  
			if ($this->bind(  $this->host->user, $this->host->pwd ) ){
				$sr		= ldap_search( $this->connection,  $dn, $filter, $attr );
				$result = ldap_get_entries($this->connection, $sr );  
				if ( $result && count($result) > 1 ){ 
					$response = $result[0]; 
					$info = $this->format_user_info($response); 
					return $info; 
				 } 
				 else return FALSE; 
			} else {
				$this->set_error( "LDAP Error: Unable to bind to LDAP Server." , LOG_INFO_ERR, 1 ); 
				return FALSE;
			}
		} catch (Exception $e ){
			$this->set_error( "LDAP Error: " . $e, LOG_INFO_ERR, 1 ); 
			return FALSE;
		} 
	}
	
	public function login( $user, $pwd ){
		try {
			$info = $this->get_user_info( $user );
			
			
			if ( $info ){
				return TRUE;  
				$success = $this->bind( $info->dn, $pwd ); 
				if ($success) {  
					return TRUE;
				} else {
					$this->set_error("Invalid User (" . $user . ") / Password.", LOG_SESS_ERR, 2 );
					return FALSE;
				}
			}  else {
				$this->set_error("Invalid User (" . $user . ").", LOG_SESS_ERR, 2 );
				return FALSE;
			}
		}  catch (Exception $e){
			$this->set_error("An error occured when trying to login to LDAP with User: " . $user . ". " . $e, LOG_SESS_ERR, 2 );
			return FALSE;
		}
	}
	
	
	private function is_member( $user ){
		try { 
			$members = $this->get_unique_members();
			if ( $members ){ 
				foreach ($members as $k => $uid) {
					if ( $k != 'count' ){
						if ( strpos($uid, "uid=" . $user .",") !== FALSE ){
							return $uid;
						}
					}
				} 
				return FALSE; 
			} else {
				$this->set_error(" LDAP Error while querying for members. " . $e, LOG_SESS_ERR, 2 );
				return FALSE;
			}
			
		} catch (Exception $e){
			$this->set_error(" LDAP Error validating user membership. " . $e, LOG_SESS_ERR, 2 );
			return FALSE;
		}
	} 
	
	private function get_unique_members(){
	 
		$filter_name = "(uniquemember=*)";  
		$attr 	= array();
		$result = array(); 
		
		try {
			if ( !$this->bind ){
				$this->connect(); 
				$this->bind	= $this->bind( $this->host->user, $this->host->pwd );
			}
			if ( $this->bind ){
				$sr			= ldap_search($this->connection, "ou=APP_MNO,ou=Aplicaciones," . $this->host->branch, $filter, $attr);
				$result 	= ldap_get_entries($this->connection, $sr);
				
			} else { 
				$this->set_error(" Could not bind to server. ", LOG_PRC_DOWN ,2);
				return FALSE;
			}
		} catch (Exception $e ){
			$this->set_error(" Could not search in LDAP. ", LOG_PRC_DOWN ,2);
			return FALSE;
		}
		
		if ( $result && count( $result ) > 0 ){
			$members = $result[0]['uniquemember']; 
			return is_array($members) ? $members : FALSE;
		} else {
			return FALSE;
		} 
		 
		return $response; 
	}
	
	public function format_user_info( $info ){
		$user = new stdClass; 
		$user->user 		=  $info['uid'][0]; 
		$user->uid 			=  $info['uid'][0]; 
		$user->displayname	=  $info['cn'][0];
		$user->name			=  $info['cn'][0];
		$user->email 		=  isset($info['mail']) ? $info['mail'][0] : '';  
		$user->dn			= $info['dn']; 
		$user->profile		= $this->set_profile( $info['uid'][0] ); 
		if ( !$user->profile ){
			$user->profile = 1;
			//return FALSE;
		}
	}
	
	protected function set_profile( $uid = "" ){
		if ( !$uid || $uid == "" ){
			$this->set_error(" Invalid uid. ", LOG_PRC_DOWN ,2);
			return FALSE; 
		} 
		$filter = "(uniquemember=uid=" . $uid . "*)"; 
		$result = array(); 
		try {
			if ( !$this->bind ){
				$this->connect(); 
				$this->bind	= $this->bind( $this->host->user, $this->host->pwd );
			}
			if ( $this->bind ){
				$sr			= ldap_search($this->connection, $this->dns['profiles'], $filter );
				$result 	= ldap_get_entries($this->connection, $sr);
				if ( $result['count'] > 0 ){
					$dn = $result[0]['dn']; 
					switch ($dn) {
						case 'cn=Administrador,ou=APP_MNO,ou=Aplicaciones,o=prosa.com.mx,o=isp':
						case 'cn=BCH_Lider,ou=APP_MNO,ou=Aplicaciones,o=prosa.com.mx,o=isp': 
							return 1; 
						case 'cn=Administrador_Banco,ou=APP_MNO,ou=Aplicaciones,o=prosa.com.mx,o=isp':  
							return 2; 
						case 'cn=Operador_Banco,ou=APP_MNO,ou=Aplicaciones,o=prosa.com.mx,o=isp':  
							return 3; 
						default:
							$this->set_error(" Invalid profile . ", LOG_PRC_DOWN ,2);
							return FALSE; 
					}
				} else { 
					$this->set_error(" Invalid profile. ", LOG_PRC_DOWN ,2);
					return FALSE; 
				}  
			} else {
			}
		} catch (Exception $e ){
			$this->set_error(" Could not search in LDAP. ", LOG_PRC_DOWN ,2);
			return FALSE;
		} 
		 
		if ( $result && count( $result ) > 0 ){
			$members = $result[0]['uniquemember']; 
			return is_array($members) ? $members : FALSE;
		} else {
			return FALSE;
		}
		return FALSE;
	}
	
	public function search( $niddle = '', $dn = '', $field = "" ){
		
		//$filter_name ="(|(sn=$niddle*)(givenname=$niddle*)(mail=$niddle*)(samaccountname=$niddle*))";
		if ( $dn == '' )
			$dn = $this->host->baseDN;
		if ( $field != ''){
			$filter_name = "(" . $field . "=" . $niddle . "*)";
			$filter = "(&(objectClass=user)(objectCategory=person)" . $filter_name . ")";
		} else {
			$filter ="(|(sn=" . $niddle . "*)(uid=" . $niddle . "*)(mail=" . $niddle . "*))";
		} 		
		$attr 	= array("cn", "mail", "uid", "dn" );
		$result = array(); 
		
		try {
			if ( !$this->bind ){
				$this->connect(); 
				$this->bind	= $this->bind( $this->host->user, $this->host->pwd );
			}
			if ( $this->bind ){
				$sr		= ldap_search($this->connection, $dn, $filter, $attr);
				$result 	= ldap_get_entries($this->connection, $sr);
			} else { 
				$this->set_error(" Could not bind to server. ", LOG_PRC_DOWN ,2);
				return FALSE;
			}
		} catch (Exception $e ){
			$this->set_error(" Could not search in LDAP. ", LOG_PRC_DOWN ,2);
			return FALSE;
		}
		
		return $this->clean_result($result);
	}
	
	public function get_group_members( $group ){
		$filter = "(&(cn=" . $group . ")(uniqueMember=*))";
		$attr 	= array("cn", "mail", "uid", "dn" );
		$attr 	= array();
		$result = array();
		$response = array();
		try {
			if ( !$this->bind ){
				$this->connect(); 
				$this->bind	= $this->bind( $this->host->user, $this->host->pwd );
			}
			if ( $this->bind ){
				$sr		= ldap_search($this->connection, $this->dns['profiles'], $filter, $attr);
				$result 	= ldap_get_entries($this->connection, $sr ); 				
				if ( $result['count'] > 0 ){
					$members = $result[0]['uniquemember'];
					foreach ($members as $k => $mem) {
						if ( $k != 'count' || !is_numeric($mem) ){
							$user_dn = explode(",",$mem);
							$uid 	 = str_replace("uid=", "", $user_dn[0]);
							$user 	 = $this->get_user_info( $uid );
							$response[] = $user;  
						}
					}
				} 				
			} else { 
				$this->set_error(" Could not bind to server. ", LOG_PRC_DOWN ,2);
				return FALSE;
			}
		} catch (Exception $e ){
			$this->set_error(" Could not search in LDAP. ", LOG_PRC_DOWN ,2);
			return FALSE;
		} 
		return $response;
	}
	
	public function search_client_users( $client, $needle ){
		//ou=People,o=PA21,o=Internacionales,o=Bancos,o=clientes,o=prosa.com.mx,o=isp
		$filter ="(|(sn=" . $niddle . "*)(uid=" . $niddle . "*)(mail=" . $niddle . "*))";
		$attr 	= array("cn", "mail", "uid", "dn" );
		$result = array(); 
		
		$dn_ntl = "ou=People,o=" . $client . ",o=Nacionales,o=Bancos,o=clientes,o=prosa.com.mx,o=isp";  
		$dn_intl= "ou=People,o=" . $client . ",o=Internacionales,o=Bancos,o=clientes,o=prosa.com.mx,o=isp";
		try {
			if ( !$this->bind ){
				$this->connect(); 
				$this->bind	= $this->bind( $this->host->user, $this->host->pwd );
			}
			if ( $this->bind ){
				$sr_ntl		= ldap_search($this->connection, $dn_ntl, $filter, $attr);
				$result_ntl	= ldap_get_entries($this->connection, $sr_ntl );
				
				$sr_intl	= ldap_search($this->connection, $dn_intl, $filter, $attr);
				$result_intl= ldap_get_entries($this->connection, $sr_intl );
				
				$result = array_merge( $result_ntl, $result_ntl );
				
			} else { 
				$this->set_error(" Could not bind to server. ", LOG_PRC_DOWN ,2);
				return FALSE;
			}
		} catch (Exception $e ){
			$this->set_error(" Could not search in LDAP. ", LOG_PRC_DOWN ,2);
			return FALSE;
		} 
		 
		return $this->clean_result($result);
	} 

	protected function clean_result( $result ){
		$response = array();
		foreach ( $result as $key => $inf ){
			if ( is_numeric($key) 
					&& isset( $inf['mail'] ) 
					&& $inf['mail'][0] != '' 
			){
				$user = $this->format_user_info( $inf ); 
				if ( $user ){
					$response[] = $user;
				}
			}
		} 
		return $response; 
	}
 
	protected function unbind(){ 
		return ldap_unbind( $this->connection );  
	}
	
	protected function set_error( $err, $type, $lvl = 1 ){
		global $Log;
		$this->error[] = $err;
		$Log->write_log(  " ERROR @ Class LDAP: " . $err , $type, $lvl);
	} 
	
	protected function set_msg( $msg , $echo = '' ){
		global $Log;
		global $mensaje;
		$Log->write_log( " MSG @ Class LDAP: " . $msg );
		if ( $echo != '') $mensaje .= $echo . " <br/> ";
	}
}
?>
