<?php
ini_set('display_errors', TRUE);
ini_set('default_socket_timeout', 3000); 
//$service_url = 'http://172.20.111.69:8880/prosa/api.php';
$service_url = 'http://187.237.42.162:8880/prosa_demo/api.php'; 
$curl = curl_init($service_url);
$user = 'bhguevar';
switch($_GET['cual']){
	case 'set_apple_token':
		$curl_post_data = array( 
			"request"		=> 'set_apple_token',
			"user" 			=> $user,
			"token" 		=> $_GET['token'], 
			"apple_token" 	=> isset($_GET['apple_token']) ? $_GET['apple_token'] : md5( "#apple_token#" . time() . "#" )
		);
		break;
	case 'logout':
		$curl_post_data = array(
			"request"	=> 'logout',
			"user" 		=> $user ,
			"token" 	=> $_GET['token']
		);
		break;
	case 'services':
		$curl_post_data = array(
			"request"	=> 'get_services',
			"user" 		=> $user ,
			"token" 	=> $_GET['token']
		);
		break;
	case 'clients':
		$curl_post_data = array(
			"request"	=> 'get_clients',
			"user" 		=> $user ,
			"token" 	=> $_GET['token']
		);
		break;
	case 'indicator':
		$curl_post_data = array(
			"request"	=> 'get_indicator',
			"user" 		=> $user ,
			"token" 	=> $_GET['token'],
			"id_service"=> isset($_GET['id_service']) ? $_GET['id_service'] : 1,
			"id_client" => 0
		);
		break;
	case 'alerts':
		$curl_post_data = array(
			"request"	=> 'get_alerts',
			"user" 		=> $user ,
			"token" 	=> $_GET['token'],
			"id_service"=> isset($_GET['id_service']) ? $_GET['id_service'] : 1 
		);
		break;
	default:
		$curl_post_data = array(
			"request"	=> 'login',
			"user" 		=> $user,
			"password" 	=> 'D3vapps123!',
		);
		break;
}

curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
$curl_response = curl_exec($curl);
$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

echo "<pre>";
echo "<b>Function: </b> " . $curl_post_data['request'] . "<p> </p>";
echo "<b>Parameters: </b> " . print_r($curl_post_data, true) . "<p>";
echo "<b>Response: </b>" . $curl_response ;

echo "<p> <b>Code: </b> " . $code; 
echo "</pre>";
?> 