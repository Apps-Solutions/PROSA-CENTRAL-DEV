<?php


  /**
   * extends Class
   *
   * @package		Prosa
   */ 
class Stepping extends Object {


	 public function get_stepping_matrix(){
	 		global $obj_bd;  //usar para  Mysql
          //$db = new oracle_db();   //usar para Oracle
          $query = "SELECT * FROM " . PFX_MAIN_DB . "step WHERE ps_status =1 ";
          $step= $obj_bd->query($query);
	 	
              if (count($step) > 0)
              {
                  foreach ($step as $k => $record)
                  {
                      ob_start();
                      require DIRECTORY_VIEWS . "/lists/lst.stepping.php";
                     
                  }
              }
             
         
      }

}

?>
