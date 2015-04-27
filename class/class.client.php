<?php

/**
 * Client Class
 */
class Client extends Object {
	
	public $id_client;
	public $client;
	public $code;
	
	public $users;
	public $services;
	
	private $db;
	
	function __construct( $id_client, $users = TRUE, $services = TRUE ) {
			
		$this->class = "Client";
		$this->db = new oracle_db();
		
		if ( is_numeric($id_client) && $id_client > 0 ){
			$this->id_client = $id_client;
			
			$query = "SELECT * FROM " . PFX_MAIN_DB . "client WHERE id_client = :id_client "; 
			$params = array(":id_client" => $this->id_client ); 
			$result= $this->db->query( $query, array( ":id_client" => $this->id_client) );
			
			if ( !$result ){
				$this->set_error( "Ocurrió un error al consultar la información de la BBDD. ", ERR_DB_QRY, 1 );
				return FALSE;
			} else if ( count($result) == 0 ){
				$this->set_error( "No se encontró el registro. ", ERR_DB_NOT_FOUND, 1 );
				$this->clean();
			} else {
				$info = $result[0];
				
				$this->client = $info['CL_CLIENT'];
				$this->code   = $info['CL_CODE'];
				
				if ($users) 	$this->get_users();
				if ($services) 	$this->get_services(); 
				
			}
			
		} else {
			$this->clean();
		}
	}
	
	private function get_users(){
		if ($this->code != ""){
		/*	$ldap = new LDAP();
			$branch = "OU=" . $this->code . ",OU=Nacional,OU=Cliente"; 
			$users = $ldap->search_branch( $branch ); 
			foreach ($users as $j => $usr) { 
				$this->users[] = $usr;  
			} 
			$branch = "OU=" . $this->code . ",OU=Internacional,OU=Cliente"; 
			$users = $ldap->search_branch( $branch ); 
			foreach ($users as $j => $usr) { 
				$this->users[] = $usr;  
			}
		*/}
	}
	
	private function get_services(){
		if ( $this->id_client > 0 ){
			$query = "SELECT * FROM " . PFX_MAIN_DB . "service_client "
						. " INNER JOIN " . PFX_MAIN_DB . "service ON id_service = sc_se_id_service "
					. " WHERE sc_cl_id_client = :id_client AND se_status = 1 ORDER BY se_order ";
			$params = array( ":id_client" => $this->id_client ); 
			$result= $this->db->query( $query, $params ); 
			if ( $result !== FALSE ){
				if ( count($result) > 0 ){
					foreach ($result as $k => $srv) {
						$s = new stdClass;
						$s->id_service 	= $srv['ID_SERVICE'];
						$s->service 	= utf8_decode($srv['SE_SERVICE']); 
						$s->comand 		= $srv['SE_COMMAND'];
						
						$this->services[] = $s;
					} 
				}
			} else {
				$this->set_error( "Ocurrió un error al consultar los servicios en la BBDD. ", ERR_DB_QRY, 1 );
			} 
		}
	}
	
	private function has_service( $id_service ){
		$k = 0;
		$found = FALSE;
		while ( $k < count( $this->services ) && !$found ){ 
			$found = ($this->services[$k]->id_service == $id_service);
			$k++;
		}
		return $found;
	}
	
	private function has_user( $user ){
		$k = 0;
		$found = FALSE;
		while ( $k < count( $this->users ) && !$found ){ 
			$found = ($this->users[$k]->user == $user);
			$k++;
		}
		return $found; 
	}
	
	
	private function validate(){ 
		if ( !$this->client != '' ){
			$this->set_error( 'Nombre de Cliente vacío. ', ERR_VAL_EMPTY );
			return FALSE;
		} 
		if ( !$this->code != '' ){
			$this->set_error( 'Código de Cliente vacío. ', ERR_VAL_EMPTY );
			return FALSE;
		} 
		return TRUE;  
	}
	
	public function save(){
		global $Session;
		if ( $Session->is_admin() ){
			if ( $this->validate() ){ 
				if ( $this->id_client > 0 ){ 
					$query = "UPDATE " . PFX_MAIN_DB . "client SET "
								. " cl_client = :cl_client, "  
								. " cl_code= :cl_code, "  
								. " cl_timestamp = :cl_timestamp " 
							. " WHERE id_client = :id_client ";  
					$data = array( 	":id_client" 	=> $this->id_client,
									":cl_client"	=> $this->client,
									":cl_code"		=> $this->code, 
									":cl_timestamp" => time()
								 );  
				} else { 
					$query = "INSERT INTO " . PFX_MAIN_DB . "client ( id_client, cl_client, cl_code, cl_status, cl_timestamp ) "
							. " VALUES  ( :id_client, :cl_client, :cl_code, :cl_status, :cl_timestamp ) ";
					
					$id_client = $this->db->get_id( PFX_MAIN_DB . "client", "id_client"  );
					$data = array( 	":id_client" 	=> $id_client,
									":cl_client"	=> $this->client,
									":cl_code"		=> $this->code, 
									":cl_status"	=> 1, 
									":cl_timestamp" => time()
								 );  
				} 
				$resp = $this->db->execute( $query, $data ); 
				if ( !$resp ){ 
					$errs = $this->db->error; 
					// $errs[(count($errs)-1)] 
					$this->set_error( "Ocurrió un error al intentar guardar la infomración. (save)" , ERR_DB_EXEC, 2);
					return FALSE;
				} 
				else return TRUE;
			}
		} else {
			$this->set_error( 'Acción restringida. ', SES_RESTRICTED_ACCESS );
			return FALSE; 
		}
	}

	public function delete(){ 
		global $Session;
		if ( $Session->is_admin()){
			$query = "UPDATE " . PFX_MAIN_DB . "client SET cl_status = 0 WHERE id_client = :id_client ";
			$data = array( ':id_client' => $this->id_client ); 
			$resp = $this->db->execute( $query, $data ); 
			if ( !$resp ){  
				$this->set_error( "Ocurrió un error al intentar borrar el registro. (delete)" , ERR_DB_EXEC, 2);
				return FALSE;
			} 
			else return TRUE;
		} else {
			$this->set_error( 'Acción restringida. ', SES_RESTRICTED_ACCESS );
			return FALSE; 
		}
	}

	public function set_service( $id_service, $state ){
		global $Session;
		if ( $Session->is_admin() ){ 
			if ( $id_service > 0 ){
				$found = $this->has_service( $id_service );
				
				if ( ($state && $found) || ( !$state && !$found ) ){ 
					return TRUE;
				} else {
					
					if ( $state === TRUE ){
						$query = "INSERT INTO " . PFX_MAIN_DB . "service_client ( sc_cl_id_client, sc_se_id_service, sc_timestamp ) "
							. " VALUES ( :id_client, :id_service, :timestamp ) ";
						$values = array( ":id_client" => $this->id_client, ":id_service" => $id_service, ":timestamp" => time());
						
					} else {
						$query = "DELETE FROM " . PFX_MAIN_DB . "service_client WHERE sc_cl_id_client = :id_client AND sc_se_id_service = :id_service ";
						$values = array( ":id_client" => $this->id_client, ":id_service" => $id_service );						
					}
					
					$result =  $this->db->execute( $query, $values );
						  
					if ( !$result ){ 
						$errs = $this->db->error; 
						// $errs[(count($errs)-1)] 
						$this->set_error( "Ocurrió un error al intentar guardar la infomración. (set_service) " , ERR_DB_EXEC, 2);
						return FALSE;
					} 
					else return TRUE;
					
				}
				
				
			} else {
				$this->set_error( 'Servicio inválido. ', SES_RESTRICTED_ACCESS );
				return FALSE; 
			}
			
		} else {
			$this->set_error( 'Acción restringida. ', SES_RESTRICTED_ACCESS );
			return FALSE; 
		}
	}


	public function get_array(){
		return array( "id_client" => $this->id_client, "client" => $this->client, "code" => $this->code ); 	
	}
	
	public function get_client_services_form(){
		global $Session;
		if ( $Session->is_admin() ){
			$response = "";   
			 
			ob_start();
			require DIRECTORY_VIEWS . "/client/frm.client_services.php"; 
			$response .= ob_get_clean(); 
			
			return $response;
		} else {
			$this->set_error("Acción restringida get_client_services_form ", SES_RESTRICTED_ACTION, 3 );
			ob_start();
			require DIRECTORY_VIEWS . "base/403.php"; 
			$response .= ob_get_clean(); 
			return $response;
		} 
	}
	
	public function get_client_services_table(){
		global $Session;
		if ( $Session->is_admin() ){
			
			$query = "SELECT id_service, se_service, "
						. " CASE WHEN NOT sc_cl_id_client IS NULL THEN 1 ELSE 0 END as checked "
					. " FROM " . PFX_MAIN_DB . "service "
						 . " LEFT JOIN " . PFX_MAIN_DB . "service_client ON sc_se_id_service = id_service AND sc_cl_id_client = :id_client "
					. " WHERE se_status = 1 "
					. " ORDER BY se_order ";
			$result = $this->db->query( $query , array( ':id_client' => $this->id_client ) );
			if ( $result ){ 
				foreach ($result as $key => $serv) { 
					$record = array();
					$record['id_service'] = $serv['ID_SERVICE'];
					$record['service']	= utf8_decode($serv['SE_SERVICE']);
					$record['checked'] 	= $serv['CHECKED'];
					$record['pfx'] 		= 'cl_' . $this->id_client . '_';
					$record['function']	= "set_client_service(" . $this->id_client . ", " . $serv['ID_SERVICE'] . ");";
					
					require DIRECTORY_VIEWS . "/lists/lst.service_chk.php";
				}
			}
			 
		} else {
			$this->set_error("Acción restringida get_client_services_form ", SES_RESTRICTED_ACTION, 3 ); 
			require DIRECTORY_VIEWS . "base/403.php";   
		} 
	}

	public function get_client_users_list(){
		global $Session;
		/*
		if ( $Session->is_admin() ){
		*/	
			if ( count($this->users) > 0 ){
				
				foreach ($this->users as $k => $user) {
					ob_start(); 
					require DIRECTORY_VIEWS . "/lists/lst.user_service_chk.php"; 
					$response .= ob_get_clean();  
				}
				return $response;
				
			} else {
				return "<tr> <td> No existen usuarios relacionados al cliente. </td> </tr>";
			}
		/*	
		} else {
			$this->set_error("Acción restringida get_client_services_form ", SES_RESTRICTED_ACTION, 3 ); 
			require DIRECTORY_VIEWS . "base/403.php";   
		}
			*/	
	}
	
	
	public function get_client_users_services_table( $user ){
		global $Session;
		global $Validate; 
		$user = $Validate->clean_input($user);  
		if ( $Session->is_admin()){ 
			if ( $this->has_user( $user ) ){ 
				if ( count($this->services) > 0 ){ 
					$query = "SELECT id_service, se_service, "
								. " CASE WHEN NOT su_user IS NULL THEN 1 ELSE 0 END as checked "
							. " FROM " . PFX_MAIN_DB . "service "
								 . " INNER JOIN " . PFX_MAIN_DB . "service_client ON sc_se_id_service = id_service AND sc_cl_id_client = :id_client "
								 . " LEFT JOIN " . PFX_MAIN_DB . "service_user ON sc_se_id_service = su_se_id_service AND su_user = :us_user "
							. " WHERE se_status = 1 "
							. " ORDER BY se_service "; 
					$result = $this->db->query( $query , array( ':id_client' => $this->id_client, ':us_user' => $user ) );
					$response = "";
					if ( $result ){
						foreach ($result as $key => $serv) { 
							$record = array();
							$record['id_service'] = $serv['ID_SERVICE'];
							$record['service']	= utf8_decode($serv['SE_SERVICE']);
							$record['checked'] 	= $serv['CHECKED'];
							$record['pfx'] 		= 'cl_' . $this->id_client . '_us_' . str_replace(".","_", strip_tags(trim($user))).'_';
							$record['function']	= "set_client_user_service(" . $this->id_client . ", '" . str_replace(".","_", strip_tags(trim($user))) . "', " . $serv['ID_SERVICE'] . ");";
							
							ob_start(); 
							require DIRECTORY_VIEWS . "/lists/lst.service_chk.php"; 
							$response .= ob_get_clean();
						}
						return $response;
					} else {
						$this->set_error("Ocurrió un error al cargar la información.",ERR_DB_QRY);
						return FALSE;
					} 
				} else {
					return "<tr> <td> No existen servicios relacionados al cliente. </td> </tr>";
				}
			} else {
				$this->set_error("Usuario ($user) no pertenece al cliente. get_client_users_services_table ", SES_RESTRICTED_ACTION, 3 );
				ob_start();
				require DIRECTORY_VIEWS . "base/403.php"; 
				$response .= ob_get_clean(); 
				return $response;   
			}
		} else {
			$this->set_error("Acción restringida get_client_users_services_table ", SES_RESTRICTED_ACTION, 3 );
			ob_start();
			require DIRECTORY_VIEWS . "base/403.php"; 
			$response .= ob_get_clean(); 
			return $response;   
		}
				
	}
	
	function clean(){
		
		$this->id_client = 0;
		$this->client = "";
		$this->code = "";
		$this->users = array();
		$this->services = array();
		$this->error = array();
		
	}
	
	private function user_has_service($user, $id_service)
	{
		$qry = "select * from ". PFX_MAIN_DB ."service_user where SU_SE_ID_SERVICE = :id_service AND SU_USER = :us_user";
		$params = array( ":id_service" => $id_service, ":us_user" => $user );
		
		$result =  $this->db->execute( $query, $params );
		
		if($result)
		{
			if( in_array($user, $result) )
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
		else
			return TRUE;
		
	}
	
	public function set_client_user_service($id_service, $us_user, $status)
	{
		global $Session;
		if ( $Session->is_admin() )
		{ 
			if ( $id_service > 0 )
			{
				$foun = $this->has_service( $id_service );
				
				$us_user = str_replace("_", ".", $us_user);
					
				if ( $status )
				{
					$query = "INSERT INTO " . PFX_MAIN_DB . "service_user ( SU_SE_ID_SERVICE, SU_USER, su_timestamp ) "
						. " VALUES (  :id_service, :us_user, :timestamp ) ";
					$values = array( ":us_user" => $us_user, ":id_service" => $id_service, ":timestamp" => time());
					
				}
				else
				{
					$query = "DELETE FROM " . PFX_MAIN_DB . "service_user WHERE SU_USER = :us_user AND SU_SE_ID_SERVICE = :id_service ";
					$values = array( ":us_user" => $us_user, ":id_service" => $id_service );						
				}
				
				$result =  $this->db->execute( $query, $values );
					  
				if ( !$result )
				{ 
					$errs = $this->db->error; 
					$this->set_error( "Ocurrió un error al intentar guardar la infomración. (set_client_service) " , ERR_DB_EXEC, 2);
					return FALSE;
				} 
				else
					return TRUE;
				
				
			}
			else
			{
				$this->set_error( 'Servicio inválido. ', SES_RESTRICTED_ACCESS );
				return FALSE; 
			}
			
		}
		else
		{
			$this->set_error( 'Acción restringida. ', SES_RESTRICTED_ACCESS );
			return FALSE; 
		}
	}
	
}
?>
