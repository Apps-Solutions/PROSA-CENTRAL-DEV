<?php 
require 'init.php';
include_once(DIRECTORY_CLASS.'class.login.php');

$login 		= new Login();
$user		= isset($_POST["user"]) 		? ($_POST["user"]) 		: "";
$password	= isset($_POST["password"]) 	? ($_POST["password"]) 	: "";
$error 		= false;
 
if(empty($user)){
    $error .=  "Favor de llenar el campo Usuario\n"; 
}
if(empty($password)){
    $error .=  "Favor de llenar el campo Contraseña\n"; 
}

if($error == false){
	 
	$logged = $login->log_in( $user, $password );  
    if( $logged == LOGIN_SUCCESS ) {  
        $location ="index.php";
    }
    else if ( $logged === LOGIN_SSH_FAILURE ){ 
        $location = "index.php?command=" . LOGIN . "&err=" . urlencode( "Ocurrió un error al verificar las credenciales. " );
	}
	else {
        $error .= "Usuario y/o Contraseña incorrectos ";
        $location = "index.php?command=" . LOGIN . "&err=" . urlencode( $error );
    }
	
}
else{
    $location = "index.php?command=" . LOGIN;
} 

header("HTTP/1.1 302 Moved Temporarily");
header("Location: $location");