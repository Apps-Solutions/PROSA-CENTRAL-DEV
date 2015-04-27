<?php
include_once("class.template.php");

class Login {
	var $bd;
	var $user;
	var $name; 
	var $token; 
	var $profile;
	var $email;
	var $id;
	var $plantilla;

	function Login(){
		$this->init();
	}

	function init(){  
		$this->template = new Template;
	}
 
	function get_user(){
		return $this->user;
	}
            
    function get_name(){
        return $this->name;
    }
            
	function get_profile(){
		return $this->profile;
	}

	function get_email(){
		return $this->email;
	}

	function get_id() {
		return $this->user;
	}

	function log_in($user, $password){ 
		global $Session;
		$logged = $Session->login($user, $password); 
		if ( $logged == LOGIN_SUCCESS ){
			return LOGIN_SUCCESS;
		} else {
			return LOGIN_BADLOGIN;
		}
		
	}
}
?>
