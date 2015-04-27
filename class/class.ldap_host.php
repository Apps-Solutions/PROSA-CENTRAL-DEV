<?php
class LDAP_HOST{
	
	public $name 	= "";
	public $domain  = "";
	public $ips 	= array();
	public $port  	= "";
	public $host 	= ""; 
	public $baseDN 	= "";
	public $user 	= "";
	public $pwd  	= "";
	public $branch	= "";
	
	function LDAP_HOST( ){ 
		$this->domain 	= LDAP_DOMAIN;
		$this->ips 		= array( LDAP_HOST );
		$this->port 	= 389;
		$this->host		= LDAP_HOST;
		$this->baseDN 	= LDAP_BASEDN;
		$this->user 	= LDAP_USER;
		$this->pwd 		= LDAP_PASSWORD;  
		$this->branch	= LDAP_BASEDN;	 
	}
}
?>