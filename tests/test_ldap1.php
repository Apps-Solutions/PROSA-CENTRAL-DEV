<?php
ini_set('display_errors', true);
require 'init.php';
 
try {
	$connection = ldap_connect(LDAP_HOST, LDAP_PORT);
	ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
	ldap_set_option($connection, LDAP_OPT_REFERRALS, 0); 
} catch (Exception $e){
	echo "Could not stablish a connection with the LDAP Server.";
	return FALSE;
}
echo "<pre>";
$uid = "uid=" . LDAP_USER . "," . LDAP_BASEDN;

/*
$uid = "cn=IdentitySSO,ou=People,o=isp";
$pwd = "La6kw.wvvp3";
*/

$uid = "uid=appmnoadm,o=prosa.com.mx,o=isp";
$pwd = "xbh8R65I";

echo "UID: " . $uid ."\n";

$bind = ldap_bind($connection, $uid , $pwd);
var_dump( $bind );
 
$dn =  LDAP_BASEDN;
$dn = "o=prosa.com.mx,o=isp";


//$dn = "ou=APP_MNO,ou=Aplicaciones,o=prosa.com.mx,o=isp";

//$search= "B003";
//$search = "dev_admin";

$search = "";
$filter_name = "(uid=" . $search . "*)"; 

$filter_name = "uniqueMember=*";
$filter = "" . $filter_name . "";

$filter = "(uniqueMember=*)";

$attr 	= array("uid");

echo "<p> Busqueda: " . $filter . " </p>";

$sr = ldap_search($connection, $dn, $filter);
echo "<br/>Reultado de la busqueda: ";
var_dump( $sr );


echo "<p> Errores: ";
echo " <<<<< <br>";
$err = ldap_error($connection);
var_dump( $err );
echo " <<<<< ";
echo "<p>";


$result = ldap_get_entries($connection, $sr);
echo "<br/>Elementos obtenidos ( " . $result['count']. " ): <pre>";
echo utf8_decode( print_r( $result , TRUE ) );

echo " /***************************************/";

$filter = "(" . ( isset( $_GET['srch']) ? $_GET['srch'] : "uniqueMember=uid=*" ) . ")";

$attr   = array("uid");

echo "<p> Busqueda: " . $filter . " </p>";

$sr = ldap_search($connection, $dn, $filter);
echo "<br/>Reultado de la busqueda: ";
var_dump( $sr );

echo "<p> Errores: ";
echo " <<<<< <br>";
$err = ldap_error($connection);
var_dump( $err );
echo " <<<<< ";
echo "<p>";


$result = ldap_get_entries($connection, $sr);
echo "<br/>Elementos obtenidos ( " . $result['count']. " ): <pre>";
echo utf8_decode( print_r( $result , TRUE ) );
echo "</pre>";

/*

$dn = "OU=" . $search . ",OU=Nacional,OU=Cliente,OU=prosa.com.mx," . $baseDN;
$dn = $baseDN;

$filter_name = "(memberOf=" . $group .")";
$filter_name = "";
$filter = "(&(objectClass=user)(objectCategory=person)" . $filter_name . ")";

/*
//$group = "";
//	
$bind 	= ldap_bind($connection, "" . $user .  "@{$domain}", $pwd);
$attr 	= array("samaccountname", "givenname", "title", "mail", "displayname", "memberof", "manager");
echo "Bind: ";
var_dump( $bind );

$sr	= ldap_search($connection, $dn, $filter, $attr);
echo "<br/>SR: ";
var_dump( $sr );

echo "<p>";
echo ">>>>>>>";

$err =  ldap_error($connection);
var_dump($err);
echo ">>>>>>>";
echo "<p>";

$result = ldap_get_entries($connection, $sr);
echo "<br/>Res: <pre>";
echo utf8_decode( print_r( $result , TRUE ) );
echo "</pre>";

//$unbind = ldap_unbind( $connection); 
 */
?>
