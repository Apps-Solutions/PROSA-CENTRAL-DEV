<?php 
session_start();
if (!isset($_GET['dbg']))
	ini_set('display_errors',  FALSE);
else ini_set('display_errors', TRUE);
define ( 'PATH', ''); 
define("DIRECTORY_CONFIG", 		"config/"); 
require_once(DIRECTORY_CONFIG . 'config.php'); 
include_once(DIRECTORY_CLASS  . 'class.oracle_db.php'); 
echo "<pre>";
$database = new oracle_db();

//$query = "SELECT * FROM APP.TBL_SMS_LOG WHERE  rownum <= 10 ";


echo " Inicio: " .  time() . "<p>";
$query =  "SELECT COUNT(*) AS TOTAL FROM " . PFX_SMS_DB . "TBL_SMS_LOG@LG_ROP_APPWEB_SMS_SMS "
	. " WHERE ID_BROKER = 'P' AND LOCAL_DATE > TO_DATE(:lcl_date, 'yyyy-mm-dd hh24:mi') " ;

$query = " SELECT * FROM " . PFX_MAIN_DB . "token ";

$query = "SELECT *  FROM " . PFX_MAIN_DB . "service_user "; //WHERE su_user = :su_user";

$result = $database->query( $query ); //, array(':su_user' => 'cavila') ); // , array( ':lcl_date' => date('Y-m-d 00:00') ) );
var_dump( $result );

echo "<p> " . time();
die();

/*
$query = "SELECT * FROM SED.TBL_MON_HORA_POS_NAC_" . date('Ym') . " WHERE (KQ2_ID_MEDIO_ACCESO = '9' OR LN_COMER = 'PROE') AND KC0_INDICADOR_DE_COMERCIO_ELEC IN (5,6)  AND rownum < 6";

$query = "SELECT DIA, SUM(TOTAL) AS TOTAL,  
	  			SUM(CASE WHEN CODIGO_RESPUESTA < 11 THEN TOTAL ELSE 0 END ) AS ACCEPTED,  
	  			SUM(CASE WHEN CODIGO_RESPUESTA > 10 THEN TOTAL ELSE 0 END ) AS REJECTED  ";
$query = "SELECT DIA FROM SED.TBL_MON_HORA_POS_NAC_" . date('Ym') . "
	  		WHERE (KQ2_ID_MEDIO_ACCESO = '9' OR LN_COMER = 'PROE')  AND DIA = :dia  AND KC0_INDICADOR_DE_COMERCIO_ELEC IN ('5','6') 
		GROUP BY DIA  ";

$cod = "";
$query = "SELECT su_user, tk_token_apple
                        FROM " . PFX_MAIN_DB . "service_user
                                INNER JOIN " . PFX_MAIN_DB . "token ON tk_user = su_user
                        WHERE su_se_id_service = :id_service AND tk_timestamp > :since AND su_user LIKE '" . $cod . "%'";
*/
$query = "SELECT su_user, tk_token_apple
                        FROM " . PFX_MAIN_DB . "service_user
                                INNER JOIN " . PFX_MAIN_DB . "token ON tk_user = su_user
                        WHERE su_se_id_service = :id_service AND tk_timestamp > :since AND su_user LIKE 'bhguevar%'";
echo $query;
        $usrs = $database->query( $query, array( ':id_service' => 2, ':since' => (time() - (86400 * 7) ) ) );
var_dump( $usrs );

die();

/*
//$query = "SELECT *  FROM " . PFX_MAIN_DB . "service_user";

//echo $query;
//$result = $database->query( $query , array( ':id_service' => 1, ':since' => 9999 ));

$query = " SELECT * FROM " . PFX_MAIN_DB . "alert WHERE rownum < 10  ORDER BY id_alert DESC  ";
$result = $database->query( $query , array( ':id_service' => 1, ':since' => 9999 ));
*/

$query = "SELECT * FROM " . PFX_MAIN_DB . "lastupdate " ;

echo "<p>";
echo "<p>";

echo "<p> POS_NAC_" . date('Ym') . " <br/> ";

$query = "SELECT * FROM SED.TBL_MON_HORA_POS_NAC_" . date('Ym') . " WHERE rownum = 1 AND DIA = :dia ";
$result = $database->query( $query , array( ':dia' => 1 ));

var_dump( $result );

echo "<p>";
echo "<p>";
echo "<p>";
echo "<p> POS_INT_" . date('Ym') . " <br/> ";

$query = "SELECT * FROM SED.TBL_MON_HORA_POS_INT_" . date('Ym') . " WHERE rownum = 1 AND DIA = :dia ";
$result = $database->query( $query , array( ':dia' => 1 ));
var_dump( $result);



echo "<p>";
echo "<p>";
echo "<p>";

echo "<p> ATM_NAC_" . date('Ym') . " <br/> ";

$query = "SELECT * FROM SED.TBL_MON_HORA_ATM_NAC_" . date('Ym') . " WHERE rownum = 1 AND DIA = :dia ";
$result = $database->query( $query , array( ':dia' => 1 )); 

var_dump( $result );

echo "<p>";
echo "<p>";
echo "<p>";
echo "<p> ATM_INT_" . date('Ym') . " <br/> ";

$query = "SELECT * FROM SED.TBL_MON_HORA_ATM_INT_" . date('Ym') . "  WHERE rownum = 1 AND DIA = :dia ";
$result = $database->query( $query , array( ':dia' => 1 )); 


var_dump( $result);


echo "<p>";
echo "<p>";
echo "<p>";

echo "DATABASE: <br/>";
var_dump( $database );

echo "</pre>";
?>
