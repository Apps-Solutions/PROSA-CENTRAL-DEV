<?php
ini_set('display_errors', true);

 $baseDN 	= "DC=sellcom-solutions,DC=com,DC=mx";
 $user 		= "manuel.fernandez";
 $pwd 		= "abcde12345!";
 $port 		= 389; 
 $domain 	= "sellcom-solutions.com.mx";
 $host 		= "172.20.111.4";
   
 $connection; 

 
try {
	$connection = ldap_connect($host, $port);
	ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
	ldap_set_option($connection, LDAP_OPT_REFERRALS, 0); 
} catch (Exception $e){
	echo "Could not stablish a connection with the LDAP Server 1.";
	return FALSE;
}

//$search= "B003";
$search = "dev_admin";
$filter_name = "(|(samaccountname=" . $search . "*)(mail=" . $search . "*))"; 

$dn = "OU=" . $search . ",OU=Nacional,OU=Cliente,OU=prosa.com.mx," . $baseDN;
$dn = $baseDN;

//$group = "";
//$filter_name = "(memberOf=" . $group .")";
$filter = "(&(objectClass=user)(objectCategory=person)" . $filter_name . ")";
		
$bind 	= ldap_bind($connection, "" . $user .  "@{$domain}", $pwd);
$attr 	= array("samaccountname", "givenname", "title", "mail", "displayname", "memberof", "manager");
echo "Bind: ";
var_dump( $bind );
 
$sr		= ldap_search($connection, $dn, $filter, $attr);
echo "<br/>SR: ";
var_dump( $sr );
$result = ldap_get_entries($connection, $sr);
echo "<br/>Res: <pre>";
echo utf8_decode( print_r( $result , TRUE ) );
echo "</pre>";

//$unbind = ldap_unbind( $connection); 
 
 
?>
