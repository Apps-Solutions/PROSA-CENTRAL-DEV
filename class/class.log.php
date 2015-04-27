<?php

class Log{
	
	private $name = "";
	private $file = "";
	protected $handle;
	
	
	function Log( $file = FALSE){
		$this->file = ( $file ) ? $file : LOG_DIR . LOG_FILE;
		$this->name = "";
		$this->check_file(); 
	}
	
	private function check_file(){
		try {
			if ( file_exists( $this->file ) ){
				if ( filesize( $this->file ) >= LOG_MAX_SIZE ){
					$success = rename($this->file, $this->file . "_" . date('YmdHis')); 
					if ( !$success ){
						throw new Exception("ERROR: No se ha podido respaldar el archivo de Log");
						die("ERROR: No se ha podido respaldar el archivo de Log");
					}
				} 
			} 
			$this->handle = fopen( $this->file , 'a'); //implicitly creates file  	
		}
		catch (Exception $e){
			throw new Exception("ERROR: No se ha podido crear el archivo de Log. ". $e );
			die( "ERROR: No se ha podido crear el archivo de Log. ". $e ); 
		} 
	}
	 
	public function write_log( $string, $type = 0, $level = 1, $user = FALSE){
		try {
			$formatted 	=  sprintf( LOG_TMPLT , date('Y-m-d H:i:s') , ( $user ? $user : ( isset($_SESSION) ? $_SESSION[ PFX_SYS . 'user'] : "" ) ), $_SERVER['REMOTE_ADDR'] , $string . "\n"); 
			$success 	= fwrite($this->handle, $formatted );
			
			if ( !$success )
				die("ERROR: No se ha podido escribir en el archivo de Log. ". $success ); 
		} 
		catch (Exception $e){
			throw new Exception("ERROR: No se ha podido escribir en el archivo de Log. ". $e );
			die("ERROR: No se ha podido escribir en el archivo de Log. ". $e ); 
		} 
	}
	 
}

?>