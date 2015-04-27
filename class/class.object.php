<?php
/**
* Object CLass
* 
* @package		SF Tracker			
* @since        19/06/2014
* @author		Manuel Fernández
*/ 

abstract class Object{
	
	protected 	$class;
	public 		$error = array(); 
	
	function Object(){
		$error = array();
	}
	
	/**
	* set_error()
	* Adds the error to the errors array and writes the error to Log
	* 
	* @param         $err String 
	* @param 		 $type Integer
	* @param 		 $lvl Integer (optional) 
	* 
	*/  
	protected function set_error( $err , $type, $lvl = 1, $id = 0 ){
		global $Log;
		$this->error[] = $err;
		$Log->write_log( " ERROR @ Class " . $this->class . ( $id > 0 ? " (" . $this->id_contact . "): " : " " ) . $err, $type, $lvl );
	} 
	
	/**
	* get_errors()
	* Adds the error to the errors array and writes the error to Log
	* 
	* @param        $html Boolean
	* 
	* @return		String of concatenated errors.  
	* 
	*/
	public function get_errors( $html = true ){
		$resp = ""; 
		foreach ($this->error as $key => $err) {
			$resp .= $err . ($html) ? "<br/>" : "\n";
		}
		return $resp;
	}
	
	/**
	* set_msg()
	* Writes the message to the Log, optionally adds the message to the global message string.
	* 
	* @param         $action String 
	* @param 		 $msg String
	* @param 		 $echo String (optional) if set adds message to global variable.  
	* 
	*/  
	protected function set_msg( $action , $msg , $echo = ''){
		global $Log;
		global $mensaje;
		$Log->write_log( $action . " @ Class " . $this->class . ( $id > 0 ? " (" . $this->id_contact . "): " : " " ) . $msg );
		if ( $echo != '') $mensaje .= $echo . " <br/> ";
	} 
}

?>