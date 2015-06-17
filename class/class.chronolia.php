<?php
/*
 * Carlos Servín
 * Esta clase simula la entrada masiva de datos a cada una de las tablas de servicios,
 * para generar los datos de las gráficas. Solo es con fines de desarrollo no de producción.
 */

 class Chronolia extends Object{
			
	public function __construct() {    
    	$this->db = new PDOMySQL(); 
	}
	
	public function run_all(){
		$this->insert_POS();
		$this->insert_ATM();
		$this->insert_multiserv();
		$this->insert_pagos_diferidos();
		$this->insert_payware();
		$this->insert_prea();
		$this->insert_procom();
		$this->insert_SMS();
		$this->insert_switch();
		return TRUE;
	}

	//Inserta datos en el Servicio de POS S7
	public function insert_POS(){
		global $obj_bd; 
		
		
		$values=array(	":dia" 		=> date("d"),
						":total" 	=> rand(1,9999),
						":code" 	=> rand(1,12),
						":medio"	=> 2 );
		$values2=array( ":dia" 		=> date("d"),
						":total" 	=> rand(1,9999),
						":code" 	=> rand(1,12),
						":medio" 	=> 2 );
										
		$query = "INSERT INTO ". PFX_SRV_DB . "TBL_MON_HORA_POS_NAC (DIA, TOTAL, CODIGO_RESPUESTA, KQ2_ID_MEDIO_ACCESO ) ".
				 "VALUES(:dia, :total, :code, :medio)";
		$result = $obj_bd->query( $query, $values );
	
		$query1 = "INSERT INTO ". PFX_SRV_DB . "TBL_MON_HORA_POS_INT (DIA, TOTAL, CODIGO_RESPUESTA, KQ2_ID_MEDIO_ACCESO ) ".
				 "VALUES(:dia, :total, :code, :medio)";
		$result1 = $obj_bd->query( $query1, $values2 );
		return TRUE;
		
	}
 	
	//inserta datos en ATM  S8
	public function insert_ATM(){
		global $obj_bd; 
		
		
		$values=array(	":dia" 		=> date("d"),
						":total" 	=> rand(1,9999),
						":tarj"  => "PEMI",
						":code" 	=> rand(1,12));
		$values2=array( ":dia" 		=> date("d"),
						":total" 	=> rand(1,9999),
						":tarj"  => "PEMI",
						":code" 	=> rand(1,12));
										
		$query = "INSERT INTO ". PFX_SRV_DB . "TBL_MON_HORA_ATM_NAC (DIA, TOTAL, LN_TARJ, CODIGO_RESPUESTA) ".
				 "VALUES(:dia, :total, :tarj, :code";
		$result = $obj_bd->query( $query, $values );
		
		$query1 = "INSERT INTO ". PFX_SRV_DB . "TBL_MON_HORA_ATM_INT (DIA, TOTAL, LN_TARJ, CODIGO_RESPUESTA) ".
				 "VALUES(:dia, :total, :tarj, :code)";
		$result1 = $obj_bd->query( $query1, $values2 );
		return TRUE;
	}
	
	//cargos automáticos retoma info de las tablas de POS
	
	//Inserta datos de Multiserv S9
	public function insert_multiserv(){
		global $obj_bd; 
		
		
		$values=array(	":dia" 		=> date("d"),
						":total" 	=> rand(1,9999),
						":code" 	=> rand(1,12),
						":lncomer" 	=> "PROI");
		$values2=array( ":dia" 		=> date("d"),
						":total" 	=> rand(1,9999),
						":code" 	=> rand(1,12),
						":lncomer" 	=> "PROI");
										
		$query = "INSERT INTO ". PFX_SRV_DB . "TBL_MON_HORA_POS_NAC (DIA, TOTAL, CODIGO_RESPUESTA, LN_COMER) ".
				 "VALUES(:dia, :total, :code, :lncomer)";
		$result = $obj_bd->query( $query, $values );			
		$query1 = "INSERT INTO ". PFX_SRV_DB . "TBL_MON_HORA_POS_INT (DIA, TOTAL, CODIGO_RESPUESTA, LN_COMER) ".
				 "VALUES(:dia, :total,:code, :lncomer)";
		$result1 = $obj_bd->query( $query1, $values2 );
		return TRUE;
	}
	
	//Inserta datos para Pagos Diferidos S1
	public function insert_pagos_diferidos(){
		global $obj_bd; 
		
		
		$values=array(	":dia" 		=> date("d"),
						":total" 	=> rand(1,9999),
						":code" 	=> rand(1,12),
						":id_token" => "01");

										
		$query = "INSERT INTO ". PFX_SRV_DB . "TBL_MON_HORA_POS_NAC (DIA, TOTAL, CODIGO_RESPUESTA, KQ6_ID_TOKEN) ".
				 "VALUES(:dia, :total, :code, :id_token)";
		$result = $obj_bd->query( $query, $values );
		//echo $query;
		
		return TRUE;
	}
	
	//Inserta datos para Payware s3
	public function insert_payware(){
		global $obj_bd; 
		
		
		$values=array(	":dia" 		=> date("d"),
						":total" 	=> rand(1,9999),
						":code" 	=> rand(1,12),
						":lntarj" => "PEMI");

		$values2=array(	":dia" 		=> date("d"),
						":total" 	=> rand(1,9999),
						":code" 	=> rand(1,12),
						":lntarj" => "PEMI");
		$bin=rand(1,4);	
		$values4=array(	":dia" 		=> date("d"),
						":total" 	=> rand(1,9999),
						":code" 	=> rand(1,12),
						":bin" 		=> $bin );								
		$values5=array(	":dia" 		=> date("d"),
						":total" 	=> rand(1,9999),
						":code" 	=> rand(1,12),
						":bin" 		=> $bin );								
										
		$query = "INSERT INTO ". PFX_SRV_DB . "TBL_MON_HORA_POS_NAC (DIA, TOTAL, CODIGO_RESPUESTA, LN_TARJ) ".
				 "VALUES(:dia, :total, :code, :lntarj)";
		$query2 = "INSERT INTO ". PFX_SRV_DB . "TBL_MON_HORA_POS_INT (DIA, TOTAL, CODIGO_RESPUESTA, LN_TARJ) ".
				 "VALUES(:dia, :total, :code, :lntarj)";
		$query3="INSERT INTO ". PFX_SRV_DB ."BINES_EMISOR_PROSA (BIN) VALUES(:bin)";		 
		$query4 = "INSERT INTO ". PFX_SRV_DB . "TBL_MON_BIN_POS_INT (DIA, TOTAL, CODIGO_RESPUESTA, BIN) ".
				 "VALUES(:dia, :total, :code, :bin)";		
		$query5 = "INSERT INTO ". PFX_SRV_DB . "TBL_MON_BIN_POS_NAC (DIA, TOTAL, CODIGO_RESPUESTA, BIN) ".
				 "VALUES(:dia, :total, :code, :bin)";	 
		$result = $obj_bd->query( $query, $values );
		$result = $obj_bd->query( $query2, $values2 );
		$result = $obj_bd->query( $query3, array(":bin" =>$bin) );
		$result = $obj_bd->query( $query4, $values4 );
		$result = $obj_bd->query( $query5, $values5 );
		//echo $query;
		
		return TRUE;
		
	}
	//insert data PREA s2
	public function insert_prea(){
		global $obj_bd; 
		
		$bin=rand(1,99);	
		
		$values=array(	":dia" 		=> date("d"),
						":total" 	=> rand(1,9999),
						":code" 	=> rand(1,12),
						":bin" => $bin);
		$values2=array(	":dia" 		=> date("d"),
						":total" 	=> rand(1,9999),
						":code" 	=> rand(1,12),
						":bin" => $bin);

		$query = "INSERT INTO ". PFX_SRV_DB . "TBL_APP_PAYWARE (BIN) VALUES(:bin)";
		$result = $obj_bd->query( $query, array(":bin"=>$bin) );
										
		$query2 = "INSERT INTO ". PFX_SRV_DB . "TBL_MON_BIN_POS_NAC (DIA, TOTAL, CODIGO_RESPUESTA, BIN) ".
				 "VALUES(:dia, :total, :code, :bin)";
		$result = $obj_bd->query( $query2, $values );
		
		$query2 = "INSERT INTO ". PFX_SRV_DB . "TBL_MON_BIN_POS_INT (DIA, TOTAL, CODIGO_RESPUESTA, BIN) ".
				 "VALUES(:dia, :total, :code, :bin)";
		$result = $obj_bd->query( $query2, $values2 );

		return TRUE;
	}
	
	public function insert_procom() {
		global $obj_bd; 
		
		
		$bin=rand(1,99);	
		
		$values=array(	":dia" 		=> date("d"),
						":total" 	=> rand(1,9999),
						":code" 	=> rand(1,12),
						":kq2" 		=> 9,
						":lncomer" 	=> "PROE",
						":kco"		=> 5	
						);
		$values2=array(	":dia" 		=> date("d"),
						":total" 	=> rand(1,9999),
						":code" 	=> rand(1,12),
						":kq2" 		=> 9,
						":lncomer" 	=> "PROE",
						":kco"		=> 6,
						"");
		$query = "INSERT INTO ". PFX_SRV_DB . "TBL_MON_HORA_POS_NAC (DIA, TOTAL, CODIGO_RESPUESTA,KQ2_ID_MEDIO_ACCESO, LN_COMER, KC0_INDICADOR_DE_COMERCIO_ELEC) ".
				 "VALUES(:dia, :total, :code, :kq2, :lncomer, :kco)";
		$query2 = "INSERT INTO ". PFX_SRV_DB . "TBL_MON_HORA_POS_INT (DIA, TOTAL, CODIGO_RESPUESTA,KQ2_ID_MEDIO_ACCESO, LN_COMER, KC0_INDICADOR_DE_COMERCIO_ELEC) ".
				 "VALUES(:dia, :total, :code, :kq2, :lncomer, :kco)";
				 
		$result = $obj_bd->query( $query, $values );
		$result = $obj_bd->query( $query2, $values2 );
		
		$values3=array(	":dia" 		=> date("d"),
						":total" 	=> rand(1,9999),
						":code" 	=> rand(1,12),
						":kq2" 		=> 9,
						":lncomer" 	=> "PROE",
						":kco"		=> 7	
						);
		$values4=array(	":dia" 		=> date("d"),
						":total" 	=> rand(1,9999),
						":code" 	=> rand(1,12),
						":kq2" 		=> 9,
						":lncomer" 	=> "PROE",
						":kco"		=> 7,
						"");
		$query3 = "INSERT INTO ". PFX_SRV_DB . "TBL_MON_HORA_POS_NAC (DIA, TOTAL, CODIGO_RESPUESTA,KQ2_ID_MEDIO_ACCESO, LN_COMER, KC0_INDICADOR_DE_COMERCIO_ELEC) ".
				 "VALUES(:dia, :total, :code, :kq2, :lncomer, :kco)";
		$query4 = "INSERT INTO ". PFX_SRV_DB . "TBL_MON_HORA_POS_INT (DIA, TOTAL, CODIGO_RESPUESTA,KQ2_ID_MEDIO_ACCESO, LN_COMER, KC0_INDICADOR_DE_COMERCIO_ELEC) ".
				 "VALUES(:dia, :total, :code, :kq2, :lncomer, :kco)";
				 
		$result = $obj_bd->query( $query3, $values3 );
		$result = $obj_bd->query( $query4, $values4 );
		return TRUE;
	}
	//Insert SMS s10
	public function insert_SMS(){
		global $obj_bd; 
		
		
		$values=array(	
						":total" 	=> rand(1,9999),
						":broker" 	=> "P",
						":localdate"=> date("Y-m-d h:i:s"),
						":time" => time());

										
		$query = "INSERT INTO PFX_SMS_DBLG_ROP_APPWEB_SMS_SMS (TOTAL, ID_BROKER, LOCAL_DATE, timestamp) ".
				 "VALUES(:total, :broker, :localdate, :time)";
		$result = $obj_bd->query( $query, $values );
	
		return TRUE;
	}
	//Insert Switch Aberto S4
 
 	public function insert_switch(){
 		global $obj_bd; 
		
		
		$bin=rand(1,99);	
		
		$strrand=rand(0,3);
		
		
		$arr=array( "PRSA", "SAHS", "SWDL", "PRSH");
		
		
		$values=array(	":dia" 		=> date("d"),
						":total" 	=> rand(1,9999),
						":code" 	=> rand(1,12),
						":lncomer" 	=> $arr[$strrand],
						);
		$values2=array(	":dia" 		=> date("d"),
						":total" 	=> rand(1,9999),
						":code" 	=> rand(1,12),
						":lncomer" 	=> $arr[$strrand],
						"");
		$query = "INSERT INTO ". PFX_SRV_DB . "TBL_MON_HORA_POS_NAC (DIA, TOTAL, CODIGO_RESPUESTA,LN_COMER) ".
				 "VALUES(:dia, :total, :code, :kq2, :lncomer)";
		$query2 = "INSERT INTO ". PFX_SRV_DB . "TBL_MON_HORA_POS_INT (DIA, TOTAL, CODIGO_RESPUESTA, LN_COMER) ".
				 "VALUES(:dia, :total, :code, :kq2, :lncomer)";
		echo $query;		 
		print_r($values);		 
		$result = $obj_bd->query( $query, $values );
		$result = $obj_bd->query( $query2, $values2 );
		return TRUE;
 	}
}
?>
