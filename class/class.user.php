<?php
/**
* User CLass
* 
* @package		Meta Tracker			
* @since        18/05/2014
* @author		Manuel Fern�ndez
*/ 

class User{
	
	public $id_user;
	public $user;
	
	public $password;
	
	public $id_profile;
	public $profile; 
	public $lastlogin;
	public $permissions;
	
	public $contact;
	public $instance;
	
	public $error = array();
	
	/**
	* User()    
	* Creates a User object from the DB.
	*  
	* @param	$id_user (optional) If set populates values from DB record. 
	* 
	*/  
	function User(  ){
		 
	}
	
	
	/**
	* validate()    
	* Validates the values before inputing to Data Base 
	*  
	* @return        Boolean TRUE if valid; FALSE if invalid
	*/ 
	public function validate(){
		
		global $Validate; 
		if ( !$this->user != '' ){
			$this->set_error( 'User value empty. ', ERR_VAL_EMPTY );
			return FALSE;
		}
		if ( !$Validate->is_unique( 'user', 'us_user', $this->user, 'id_user', $this->id_user ) ){
			$this->set_error( 'User not unique. ', ERR_VAL_NOT_UNIQUE );
			return FALSE;
		} 
		if ( !$this->id_profile > 0 || !$Validate->exists( 'profile', 'id_profile', $this->id_profile)){
			$this->set_error( 'Invalid profile. ', ERR_VAL_EMPTY );
			return FALSE;
		}
		
		return TRUE;
		
	}
	 
	public function get_client_users_services_table( $id_client, $user )
	{
		global $obj_bd;			
		
		$query = "SELECT id_service, se_service, "
				. " CASE WHEN NOT su_user IS NULL THEN 1 ELSE 0 END as checked "
				. " FROM " . PFX_MAIN_DB . "service "
				. " INNER JOIN " . PFX_MAIN_DB . "service_client ON sc_se_id_service = id_service AND sc_cl_id_client = :id_client "
				. " LEFT JOIN " . PFX_MAIN_DB . "service_user ON sc_se_id_service = su_se_id_service AND su_user = :us_user "
				. " WHERE se_status = 1 "
				. " ORDER BY se_order ";
				
		//$obj_bd = new oracle_db();
	//	$obj_bd = new PDOMySQL();
		$result = $obj_bd->query( $query , array( ':id_client' => $id_client, ':us_user' => $user ) );
		$response = "";
		
		if ( $result )
		{
				
		
				foreach ($result as $key => $serv)
				{ 
					$record = array();
					$record['id_service'] = $serv['ID_SERVICE'];
					$record['service']	= $serv['SE_SERVICE'];
					$record['checked'] 	= $serv['CHECKED'];
					$record['pfx'] 		= 'cl_' . $id_client . '_us_' . str_replace(".","_", strip_tags(trim($user))).'_';
					$record['function']	= "set_client_user_service(" . $id_client . ", '" . str_replace(".","_", strip_tags(trim($user))) . "', " . $serv['ID_SERVICE'] . ");";
					
					ob_start(); 
					require DIRECTORY_VIEWS . "/lists/lst.service_chk.php"; 
					$response .= ob_get_clean();
				}
				
				return $response;
		}
		else
		{
				$this->set_error("Ocurrio un error al cargar la informacion.",ERR_DB_QRY);
				return FALSE;
		} 
			
	}
	
	public function set_client_user_service($id_service, $us_user, $status)
	{
		
		if ( $id_service > 0 )
		{
			//$foun = $this->has_service( $id_service );
			
			$us_user = str_replace("_", ".", $us_user);
		    //$obj_bd = new oracle_db();
			global $obj_bd;
			
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
			
			$result =  $obj_bd->execute( $query, $values );
				  
			if ( !$result )
			{ 
				$errs = $obj_bd->error; 
				$this->set_error( "Ocurri� un error al intentar guardar la infomraci�n. (set_client_service) " , ERR_DB_EXEC, 2);
				return FALSE;
			} 
			else
				return TRUE;
			
			
		}
		else
		{
			$this->set_error( 'Servicio inv�lido. ', SES_RESTRICTED_ACCESS );
			return FALSE; 
		}
			
		
	}
}

?>
