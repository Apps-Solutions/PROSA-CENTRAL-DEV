<?php
ini_set('display_errors', true);
require_once 'init.php';

echo "<pre>";

$ldap = new LDAP();


echo "....<p>";
if ($ldap){
	var_dump( $ldap );
} else {
	echo $ldap->error[0];
}

echo "**************************************<p>";


$groups = $ldap->get_group_members( 'Administrador' );

var_dump( $groups );

echo "*************************************<p> ";

$member  = $ldap->is_member( 'bhguevar'  );

var_dump( $member );

echo "</pre>";
?>
