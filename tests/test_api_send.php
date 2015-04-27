<?php
if ( isset($_GET['token'])){
	
	$service_url = 'http://187.237.42.162:8880/prosa/send_api.php';
	$curl = curl_init($service_url);
	
	$tokens = $_GET['token'];
	
	$curl_post_data = array(  'request' => 'send_alert', 'token' => array($tokens), 'message' => "TEST " . date('Y:m:d H:i:s') );
	
	$curl_post_data = http_build_query($curl_post_data); 
	
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
	$curl_response = curl_exec($curl);
	$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	
		
	if ( $curl_response ){
		echo "<pre>";
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		
		
		echo "<pre>";
		echo "<b>Function: </b> " . $curl_post_data['request'] . "<p> </p>";
		echo "<b>Parameters: </b> " . print_r($curl_post_data, true) . "<p>";
		echo "<b>Response: </b>" . $curl_response ;
	} else {
		
	    $err     = curl_errno( $curl );
	    $errmsg  = curl_error( $curl );
		
		
		echo "Error: " . $err . " <br> Msg: " . $errmsg . "<p>"; 
	}
	
	curl_close($curl);
	
	echo "<p> <b>Code: </b> " . $code; 
	echo "</pre>";

} else {
	echo "invalid token";
}
	
	
	
?>