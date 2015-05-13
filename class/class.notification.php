<?php

  /*
   * Esta clase contiene todos los metodos y propiedades del modulo NOTIFICACIONES
   * @author Marcos Mtz <marcos.martinez@gruposellcom.com>
   */

  class NOTIFICATION extends Object
  {

      public $tk_apple;
      private $db;

      function __construct()
      {
          $this->tk_apple = '';
          //$this->db = new oracle_db();
          $this->db = new PDOMySQL();
      }

      /* @function get_TokenApple
       * @return TRUE | FALSE
       * @param int $user
       */

       function get_TokenApple($user)
      {
          $query = "SELECT tk_token_apple FROM " . PFX_MAIN_DB . "token WHERE tk_timestamp > 0 AND tk_user = :tk_user ";

          $result = $this->db->query($query, array(':tk_user' => $user));
          
          if (count($result) > 0)
          {
              $info = $result[0];
              
              $this->tk_apple = $info['TK_TOKEN_APPLE'];
              
              return TRUE;
          }
          else if(count($result) == 0)
          {
              print_r($result);
              return FALSE;
          }
          else
          {
              return FALSE;;
          }
      }

      /*
       * INSERT INTO pra_alert
       * @return TRUE OR FALSE
       * @param array $users
       * @param int $id_cliente
       * @param int $id_servicio
       * @param string $mesage        
       */

      public function save_Alert($cliente, $servicio, $mesage, $usuario)
      {

          if (IS_ADMIN)
          {
              if (count($cliente) > 0 || count($servicio) > 0 && $mesage != '')
              {

                  $query = "INSERT INTO " . PFX_MAIN_DB . "alert (id_alert,al_cl_id_client,al_timestamp,al_se_id_service,al_text,al_status) "
                          . "VALUES(:id_alert,:al_cl_id_client,:al_timestamp,:al_se_id_service,:al_text,:al_status)";
 

                  $response = TRUE;

                  foreach ($servicio as $id_servicio)
                  {
                      if (!empty($id_servicio))
                      {
                          foreach ($cliente as $id_cliente)
                          {
                              if (!empty($id_cliente))
                              {
                                  $id_alert = $this->db->get_id(PFX_MAIN_DB . "alert", "id_alert");

                                  $values = array(':id_alert' => $id_alert,
                                       ':al_cl_id_client' => $id_cliente,
                                       ':al_timestamp' => time(),
                                       ':al_se_id_service' => $id_servicio,
                                       ':al_text' => $mesage,
                                       ':al_status' => 1,
                                       ':al_user' => $usuario);

                                  $result = $this->db->execute($query, $values);

                                  if (!$result)
                                  {
                                      $this->set_error("Error al guardar las alertas", ERR_DB_EXEC, 2);
                                      break;
                                  }

                                  $response = ( $response && $result );
                              }
                          }
                      }
                  } 
                  return $response;
              }
              else
              {
                  return FALSE;
              }
          }
          else
          {
              $this->set_error("Acceso restringido", SES_RESTRICTED_ACCESS);
              return FALSE;
          }
      }

      public function send_notification($users, $mesage, $cliente, $servicio)
      {
          global $Session;

          if (IS_ADMIN)
          {
                if (count($users) > 0 && $mesage != '') { 
				      foreach ($users as $user)
				      { 
				          if (!empty($user))
				          {
				              $tk = $this->get_TokenApple(($user)); 
				              if ($tk == true) {
				                  if ($this->tk_apple != '') 
				                  {
				                  		$tokens[] = $this->tk_apple; 
				                  }
				              }
				              else
				              {
				                  $this->error[] = "Error al consultar el token del usuario $user";
				                  $token = FALSE;
				              }			  
							  
				          }
				          $i++;
				      }
				 
					  	$service_url = 'http://187.237.42.162:8880/prosa/send_api.php';
						$curl = curl_init($service_url);
						
				
						$curl_post_data = array(  'request' => 'send_alert', 'token' => $tokens, 'message' => $mesage, 'id_service' => $servicio );
						$curl_post_data = http_build_query($curl_post_data); 
						
						curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($curl, CURLOPT_POST, true);
						curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
						$curl_response = curl_exec($curl);
						$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
						
						$this->set_msg( "INFO", print_r( $curl_response , TRUE) );
						
					if ( $curl_response ){
						
						try {
							$response = json_decode( $curl_response );
							if ($code == 200 && $response->success !== FALSE) {
								$result = $this->save_Alert($cliente, $servicio, $mesage, $Session->get_user()); 
								return TRUE;
							} else {
								$this->set_error( "Ocurrio un error al enviar la notificacion." );
								return FALSE; 
							}
						} catch ( Exception $e ){
							$this->set_error( $curl_response . " Exception: " . $e );
							return FALSE; 
						} 
						
					} else {
						$err     = curl_errno( $curl );
					    $errmsg  = curl_error( $curl );
						$this->set_error( $errmsg );
						return FALSE; 
					}
				}
          }
      }
  }
?>