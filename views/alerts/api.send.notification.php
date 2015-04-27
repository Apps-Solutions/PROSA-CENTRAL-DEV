<?php 
  if (count($users) > 0 && $mesage != '')
  { 
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
		

		$curl_post_data = array(  'request' => 'send_alert', 'token' => $tokens, 'message' => $mesage );
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
?>