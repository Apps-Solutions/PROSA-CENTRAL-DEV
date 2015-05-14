<?php

  /**
   * Agenda CLass
   *
   * @package		Prosa
   */ 
class Agenda extends Object {

      public $clients = array();
      public $users = array();
      public $prosa = array();
 
      public $errors = array();

      /**
       * Agenda()
       * Creates a Agenda object from the DB.
       *
       * @param	$id_threshold (optional) If set populates values from DB record.
       *
       */
      function Agenda()
      {
          $this->load();
      }
 
	/**
	* load()
	*
	*/
	private function load() { 
		$this->load_clients();  
		/*
		$us1 = new stdClass;
		$us1->user = 'bhguevar';
		$us1->name = 'bhguevar';
		$us1->email = 'bhguevar';
		
		$us2 = new stdClass;
		$us2->user = 'aontiver';
		$us2->name = 'aontiver';
		$us2->email = 'aontiver';
		
		$us3 = new stdClass;
		$us3->user = 'mxbdcal1';
		$us3->name = 'mxbdcal1';
		$us3->email = 'mxbdcal1';
			
		$this->prosa = array(  $us1, $us2, $us3 );
		 * */  
	}
 

      /**
       * load_clients()
       */
      private function load_clients(){
      	global $obj_bd;
          //$db = new oracle_db();
		 // $db = new PDOMySQL();
          $query = "SELECT * FROM " . PFX_MAIN_DB . "client WHERE cl_status > 0 ";
          $clients = $obj_bd->query($query);
          if ($clients)
          {
              foreach ($clients as $k => $cli)
              {
                  $client = new stdClass;
                  $client->id_client = $cli['id_client'];
                  $client->client = $cli['cl_client'];
                  $client->code = $cli['cl_code'];
                  $this->clients[] = $client;
              }
          }
          else
          {

              $this->set_error("Ocurri� un error al obtener los clientes de la BBDD. ", ERR_DB_QRY, 1);
              return FALSE;
          }
      }
	  
	  /**
	   * get_prosa_users()
	   */
	
	public function get_prosa_users(){
		$this->prosa = array();
		if ( IS_ADMIN ){
			$ldap = new LDAP();
			$admins = $ldap->get_group_members( 'Administrador' );
			$bch = $ldap->get_group_members( 'BCH_Lider' );
			
			$this->prosa = array_merge( $admins, $bch );
			
		}
		return $this->prosa;
	}
	  
 
      /**
       * get_clients_table()
       *
       *
       */
      public function get_clients_table()
      {
          if (IS_ADMIN)
          {
              $response = "";
              if (count($this->clients) > 0)
              {
                  foreach ($this->clients as $k => $record)
                  {
                      ob_start();
                      require DIRECTORY_VIEWS . "/lists/lst.client.php";
                      $response .= ob_get_clean();
                  }
              }
              return $response;
          }
          else
          {
              return "";
          }
      }

      /**
       * get_clients_options
       */
      public function get_clients_options($selected)
      {
          $html = "<option value='0'> Cliente </option>";
          foreach ($this->clients as $k => $ops)
          {
              $html .= "<option value='" . $ops->id_client . "' "
                      . ( $selected == $ops->id_client ? "selected='selected'" : "" )
                      . "  >" . $ops->client
                      . "</option>";
          }
          return $html;
      }

      /**
<<<<<<< HEAD
       * get_users_table()
=======
       * get_clients_table()
>>>>>>> origin/master
       *
       *
       */
      public function get_users_table()
      {
          if (IS_ADMIN)
          {
              $response = "";

			  $this->get_prosa_users();
              if (count($this->prosa) > 0)
              {
                  foreach ($this->prosa as $k => $record)
                  {  
                      ob_start();
                      require DIRECTORY_VIEWS . "/lists/lst.user.php";
                      $response .= ob_get_clean();
                  }
              }
              return $response;
          }
          else
          {
              return "";
          }
      }

      
      /**
	   * get_prosa_users_list()
	   * 
	   */
      public function get_prosa_users_list(){
      	global $Session;
		if ( $Session->is_admin() ){
			$response = "";
			$this->get_prosa_users();
			if (count($this->prosa) > 0) {
    				foreach ($this->prosa as $k => $record){
	                	      ob_start();
        	        	      require DIRECTORY_VIEWS . "/lists/lst.user_prosa.php";
           		     	      $response .= ob_get_clean();
                  		}
              		}
              		return $response;
		} else {
			return "";
		}
      }
	  
	  public function get_services_users_prosa($user)
	  {
	  global $obj_bd;
		$qry = 	 "select id_service, se_service, su_user, ".
				 "CASE WHEN NOT su_user IS NULL THEN 1 ELSE 0 END as checked ".
				 "from ". PFX_MAIN_DB ."service ".
				 "left join ". PFX_MAIN_DB ."service_user ON id_service = su_se_id_service AND su_user = '".$user."' ".
				 "WHERE se_status = 1 ORDER BY se_order ";
				 
		//$values = array( ":su_user" => $user );
		//$db = new oracle_db();
		//$db = new PDOMySQL();
		
		$result = $obj_bd->query($qry);
		
		$response = "";
		if($result)
		{
				foreach ($result as $key => $serv)
				{ 
						$record = array();
						$record['id_service'] = $serv['id_service'];
						$record['service']	= utf8_decode($serv['se_service'] );
						$record['checked'] 	= $serv['CHECKED'];
						$record['pfx'] 		= 'serv_user_prosa_'  . str_replace(".","_", strip_tags(trim($user))).'_';
						$record['function']	= "set_user_prosa_service('" . str_replace(".","_", strip_tags(trim($user))) . "', " . $serv['ID_SERVICE'] . ");";
						
						ob_start(); 
						require DIRECTORY_VIEWS . "/lists/lst.service_chk.php"; 
						$response .= ob_get_clean();
				}
			return $response;
		}
		else
		{
			$this->set_error("Ocurrio un error al cargar la informacion",ERR_DB_QRY);
			return FALSE;
		} 
	  }
       
      /**
       * validate()
       * Validates the values before inputing to Data Base
       *
       * @return        Boolean TRUE if valid; FALSE if invalid
       */
      public function validate()
      {
          return TRUE;
      }

      /** 
       * save()
       * Inserts or Update the record in the DB.
       *
       * @return        Boolean TRUE if success; FALSE if failed
       */
      public function save($data = FALSE)
      {
      	global $obj_bd;
          if (IS_ADMIN)
          {
              if ($data && is_array($data))
              {
                  //$db = new oracle_db();
				   //$db = new PDOMySQL();
                  $query = "UPDATE " . PFX_MAIN_DB . "threshold SET "
                          . " th_threshold 	= :th_threshold , "
                          . " th_time_prosa 	= :th_time_prosa,  "
                          . " th_time_client 	= :th_time_client,  "
                          . " th_timestamp 	= :th_timestamp "
                          . " WHERE th_se_id_service = :id_service  ";
                  $response = TRUE;
                  foreach ($data as $k => $vals)
                  {
                      $params = array(":th_threshold" => number_format($vals['th_threshold'], 2),
                           ":th_time_prosa" => number_format($vals['th_time_prosa'], 0),
                           ":th_time_client" => number_format($vals['th_time_client'], 0),
                           ":id_service" => number_format($vals['id_service'], 0),
                           ":th_timestamp" => time()
                      );
                      $resp = $obj_bd->execute($query, $params);
                      if (!$resp)
                      {
                          $this->error[] = $db->get_error_msg();
                      }
                      $response = ( $response && $resp );
                  }
                  return $response;
              }
              else
                  return FALSE;
          } else
          {
              $this->set_error("Acción restringida.", SES_RESTRICTED_ACTION, 3);
              return FALSE;
          }
      }

      /**
>>>>>>> origin/master
       * clean()
       * Cleans all parameters and resets all objects
       *
       */
      public function clean()
      {
          $this->error = array();
          $this->clients = array();
      }

      /**
       * get_clients_table_edit()
       * tabla para editar clientes, y servicios del cliente
       *
       */
      public function get_clients_table_edit()
      {
          if (IS_ADMIN)
          {
              $response = "";
              if (count($this->clients) > 0)
              {
                  foreach ($this->clients as $k => $record)
                  {
                      ob_start();
                      require DIRECTORY_VIEWS . "/lists/lst.clientservice.php";
                      $response .= ob_get_clean();
                  }
              }
              return $response;
          }
          else
          {
              return "";
          }
      }

      public function get_users_attached($id_service)
      {
      	global $obj_bd;
          //$db = new oracle_db();
		  //$db = new PDOMySQL();

          $qry = "select * from " . PFX_MAIN_DB . "service_user where su_se_id_service = " . $id_service . " ";

          $result =$obj_bd->query($qry);
          $resp = array();

          if ($result)
          {

              $resp = '';
              foreach ($result as $k => $cod)
              {
                  $resp[] = $cod['SU_USER'];
              }
              return $resp;
          }
          else
          {
              return FALSE;
          }
      }
 
      public function get_service($id, $tipo){
          //$db = new oracle_db();
		  //$db = new PDOMySQL();
		  global $obj_bd;
          $qry = "SELECT * FROM " . PFX_MAIN_DB . "service_client WHERE " . ($tipo == 'service_client' ? "sc_se_id_service" : "sc_cl_id_client") . "=" . $id . " ";
          $result = $obj_bd->query($qry);
          $resp = array();

          if ($result)
          {
              $resp = '';

              foreach ($result as $key => $id_cliente)
              {
                  if (!empty($id_cliente))
                  {
                      $resp[] = ($tipo == 'service_client' ? $id_cliente['SC_CL_ID_CLIENT'] : $id_cliente['SC_SE_ID_SERVICE']);
                  }
              }

              if (count($resp) > 0)
              {
                  return $resp;
              }
              else
              {
                  return array();
              }
          }
          else
          {
              return FALSE;
          }
      } 
}
?>