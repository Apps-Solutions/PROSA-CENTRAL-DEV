<?php
ini_set('display_errors', TRUE);
ini_set('default_socket_timeout', 3000);  
//$service_url = 'http://localhost:/GitHub/PROSA-CENTRAL-DEV/api_local.php';   
$service_url = 'http://54.187.219.128/prosa_dev/api_local.php';

$curl = curl_init($service_url);
$user = 'hbguevar';
$pass="BeXw3QMN";
$id_service='1';
$tokenprosa="f738083b5bbdfe7489d5280600b52678";
$tokenapple="ebd345021f5ef663b58d7cd05ee6c92dd2646a02042df5eded4167e4eccf0ccd";
//$tokenapple="";
	switch($_GET['cual']){ 
	case 'logout': 
		$curl_post_data = array(
			"request"	=> 'logout',
			"user" 		=> $user 						
		);
		break;
	case 'check_token':
		$curl_post_data = array(
			"request"	=> 'check_token',
			"user" 		=> $user,
			"token" 	=> $tokenprosa
		);
		break;
	case 'set_apple_token':
		$curl_post_data = array(
			"request"	=> 'set_apple_token',
			"user" 		=> $user			
		);
		break;
		case 'get_alerts':
		$curl_post_data = array(
			"request"	=> 'get_alerts',
			"user" 		=> $user,
			"password" 	=> $pass,	
			"token" 	=> $tokenprosa,
			"id_service" 		=> 1		
		);
		break;
		case 'get_services':
		$curl_post_data = array(
			"request"	=> 'get_services',
			"user" 		=> $user			
		);
		break;
		case 'get_indicator':
		$curl_post_data = array(
			"request"	=> 'get_indicator',
			"id_service" 		=> 1			
		);
		break;
		case 'get_clients':
		$curl_post_data = array(
			"request"	=> 'get_clients',
			"user" 		=> $user			
		);
		break;
		case 'get_user_client':
		$curl_post_data = array(
			"request"	=> 'get_user_client',
			"user" 		=> $user			
		);
		break;
		case 'chronolia':
		$curl_post_data = array(
			"request"	=> 'chronolia',
			"user" 		=> $user			
		);
		break;
	/**/default:
		$curl_post_data = array(
			"request"	=> 'login',
			"user" 		=> $user,
			"password" 	=> $pass
		);
		break;
	
}
echo "<pre>";
echo "<b>Function: </b> " . $curl_post_data['request'] . "<p> </p>";
echo "<b>Parameters: </b> ";
	var_dump($curl_post_data ); 
echo  "<p>";
$curl_post_data = http_build_query($curl_post_data);
echo "<b>HttpQuery: </b> ";
	var_dump($curl_post_data); 
echo  "<p>";
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
$curl_response = curl_exec($curl);
$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);
echo "<b>Code: </b> " . $code . "<p>"; 
echo "<b>Response: </b>" . $curl_response ;
echo "<p> <b> Response object: </b> <br>";
var_dump( json_decode( $curl_response ) );
echo "</pre>";
echo md5('gramos');
?>