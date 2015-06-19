<?php
die();
ini_set('display_errors',TRUE);
date_default_timezone_set("America/Mexico_City");

define( 'PATH', dirname(__FILE__) . "/" );

define("DIRECTORY_CONFIG", PATH . "config/");
require_once(DIRECTORY_CONFIG . 'config.php'); 

include_once(DIRECTORY_CLASS  . 'class.object.php');
include_once(DIRECTORY_CLASS  . 'class.log.php');
include_once(DIRECTORY_CLASS  . 'class.oracle_db.php');  
include_once DIRECTORY_CLASS .  "class.ldap.php";

require_once DIRECTORY_CLASS . 'class.service.php';
require_once DIRECTORY_CLASS . 'class.admin.PagosDiferidos.php';
require_once DIRECTORY_CLASS . 'class.admin.PREA.php';
require_once DIRECTORY_CLASS . 'class.admin.Payware.php';
require_once DIRECTORY_CLASS . 'class.admin.switch.php';
require_once DIRECTORY_CLASS . 'class.admin.PROCOM.php';
require_once DIRECTORY_CLASS . 'class.admin.cargosautomaticos.php';
require_once DIRECTORY_CLASS . 'class.admin.pos.php';
require_once DIRECTORY_CLASS . 'class.admin.atm.php';
require_once DIRECTORY_CLASS . 'class.admin.multiserv.php';
require_once DIRECTORY_CLASS . 'class.SMS.php';
require_once DIRECTORY_CLASS . "class.notification.php";

require_once DIRECTORY_CLASS . "class.agenda.php";
//require DIRECTORY_CLASS . 'ApnsPHP/Autoload.php';


function get_users_prosa_config(){
	global $db; 
	global $Ldap;
	$config = array();
	$branch = "OU=GenteProsa,OU=prosa.com.mx"; 
	
	$admins = $Ldap->get_group_members( 'Administrador' );
	$bchs 	= $Ldap->get_group_members( 'BCH_Lider' );
	
	$users = array();
	foreach ($admins as $k => $us) {
		var_dump( $us );
		$users[] = $us->uid;
	}
	foreach ($bchs as $k => $us) {
		$users[] = $us->uid;
	}

	foreach ($users as $j => $usr) {
		
		$q_token = "SELECT tk_token_apple FROM " . PFX_MAIN_DB . "token WHERE tk_user = :tk_user AND tk_timestamp > 0 ";
		$aplicable = $db->query( $q_token , array( ':tk_user' => $usr ));
		if ( count( $aplicable ) > 0 ){
		
			$config[$usr] = array( '', FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE );
			$query = "SELECT su_se_id_service FROM " . PFX_MAIN_DB . "service_user WHERE su_user = :su_user";
			$servs = $db->query( $query, array( ':su_user' => $usr ) );
			foreach ($servs as $k => $srv) {
				$config[$usr][0] = $aplicable[0][ 'TK_TOKEN_APPLE' ] ;
				$config[$usr][$srv['SU_SE_ID_SERVICE']] = TRUE ;
			}
				
		}
		 
	} 
var_dump( $config );
	return $config;
}
 

function check_alert_sent( $id_service, $id_client = 0, $since = FALSE ){
	
	global $db;
	$query = "SELECT COUNT(id_alert) as sent FROM " . PFX_MAIN_DB . "alert " 
			. " WHERE al_se_id_service = :id_service AND al_timestamp > :since "
				. (( $id_client > 0 ) ? " AND al_cl_id_client = :id_client " : " AND al_cl_id_client IS NULL ");
	if ( !$since )
		$since = time() - (60 * 30);
	
	$alert = $db->query( $query , array( ':id_service' => $id_service, ':since' => $since,  ':id_client' => $id_client ) );
	if ( count( $alert ) > 0 ){
		return $alert[0]['SENT'];
	} else {
		return FALSE;
	}	
}

function get_users_notification( $id_service, $cod ){
	global $db;
	global $Log;
	if ( !$cod || $cod == 0)
		$cod = "";
	$qusrs = "SELECT su_user, tk_token_apple
			FROM " . PFX_MAIN_DB . "service_user
				INNER JOIN " . PFX_MAIN_DB . "token ON tk_user = su_user 
			WHERE su_se_id_service = :id_service AND tk_timestamp > :since AND su_user LIKE '" . $cod . "%'";
	$usrs = $db->query( $qusrs, array( ':id_service' => $id_service, ':since' => (time() - (86400 * 7) ) ) );
	$users = array();
	if ( $usrs ){
		$users = array();
		foreach ($usrs as $ku => $us) { 
			$users[] = array( 'u' => $us['SU_USER'] , 't' => $us['TK_TOKEN_APPLE'] );
		}
		$Log->write_log("To notify. " . print_r( $users , TRUE)); 
	}
	else{
		$Log->write_log("Error retrieving users for notifications from the DataBase. "); 
	}
	return $users;
}

function send_notification( $users, $service, $message){
	  
	global $Log;
	
	$service_url = 'http://187.237.42.162:8880/prosa/send_api.php';
	$curl = curl_init($service_url);
	
	$curl_post_data = array(  'request' => 'send_alert', 'token' => $users, 'message' => $message );
	
	$curl_post_data = http_build_query($curl_post_data); 
	
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
	$curl_response = curl_exec($curl);
	$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	 
	curl_close($curl);
	 
	echo "\n Response: " . $curl_response . "<<<";
	$Log->write_log($curl_response); 
	
	$data = json_decode( $curl_response );
	return $data->success;
}

function save_alert( $id_client, $id_service, $message, $th = 0 ){
	global $script_user;
	global $db;
	
	$query = "INSERT INTO " . PFX_MAIN_DB . "alert (id_alert,al_cl_id_client,al_timestamp,al_se_id_service,al_text,al_status)" //,al_user) "
                          . "VALUES(:id_alert,:al_cl_id_client,:al_timestamp,:al_se_id_service,:al_text,:al_status) "; //,:al_user)";
	
	$id_alert = $db->get_id(PFX_MAIN_DB . "alert", "id_alert"); 
	$values = array(':id_alert' => $id_alert,
			       ':al_cl_id_client' 	=> $id_client,
			       ':al_timestamp' 		=> time(),
			       ':al_se_id_service' 	=> $id_service,
			       ':al_text' 		=> $message,
			       ':al_status'		=> 1,
			       ':al_user' 		=> $script_user
				);

	$result = $db->execute( $query, $values );
	if ( $result )
		return TRUE;
	else {
		global $Log;
		$Log->write_log("Error saving alert '$message'.");
		return FALSE;
	}
	
}


$Log = new Log( PATH . LOG_DIR . "monitor_log_" . date( 'Ymd' ));
//$db = new oracle_db();
$db = new PDOMySQL();
$Ldap = new LDAP();
$agenda = new Agenda();

$script_user = "System Monitor";
$query = " SELECT * FROM " . PFX_MAIN_DB . "service INNER JOIN " . PFX_MAIN_DB . "threshold ON th_se_id_service = id_service ";

$services = $db->query( $query );

$notification = new NOTIFICATION();
$prosa_users = get_users_prosa_config(); 

var_dump( $prosa_users );

/*
$push = new ApnsPHP_Push( ApnsPHP_Abstract::ENVIRONMENT_PRODUCTION, PATH . 'certificados_pem/' . SERVER_CERTIFICATE );
$push->setRootCertificationAuthority(PATH . 'certificados_pem/' . ROOT_AUTORITY);
$push->connect();
*/
$sent = 0;

foreach ($services as $k => $srv) {
	
	$ids  	= $srv['ID_SERVICE']; 
	$serv 	= utf8_decode($srv['SE_SERVICE']);
	$tprosa	= $srv['TH_TIME_PROSA'];
	$tcl 	= $srv['TH_TIME_CLIENT'];
	$th 	= $srv['TH_THRESHOLD']; 
	
	$qcl = "SELECT ID_CLIENT, CL_CODE FROM " . PFX_MAIN_DB . "service_client " 
			. " INNER JOIN " . PFX_MAIN_DB . "client ON id_client = sc_cl_id_client AND cl_status > 0 " 
		. " WHERE sc_se_id_service = :ids ";
	
	$clients = $db->query( $qcl, array( ':ids' => $ids ) );
	$Service = FALSE;
	foreach ($clients as $j => $cli) {
		
		switch ( $ids ) {
			case 1: $Service = new AdminPagosDiferidos( $cli['ID_CLIENT'] ); break; 
			case 2: $Service = new AdminPREA( $cli['ID_CLIENT']  ); 			break; 
			case 3: $Service = new AdminPayware( $cli['ID_CLIENT']  ); 		break; 
			case 4: $Service = new AdminSwitchAbierto( $cli['ID_CLIENT']  ); break; 
			case 5: $Service = new AdminPROCOM( $cli['ID_CLIENT']  ); 		break; 
			case 6: $Service = new AdminCargosAutomaticos( $cli['ID_CLIENT']  ); break; 
			case 7: $Service = new AdminPOS( $cli['ID_CLIENT']  ); 			break; 
			case 8: $Service = new AdminATM( $cli['ID_CLIENT']  ); 			break; 
			case 9: $Service = new AdminMultiserv( $cli['ID_CLIENT']  ); 	break; 
			case 10: $Service = new SMS( $cli['ID_CLIENT']  ); 			break; 
		}
		
		$sendto = array(); 
		if ($Service){ 
			if ( !$Service->has_maintenance()){
				
				if ( $j == 0 && $Service->has_state && $Service->state == 0 ){ 
					
					if ( $Service->last_timestamp < time() - ($Service->time_prosa * 60)  ){
						 // last update time exceeded  
						if ( $Service->last_timestamp < time() - ($Service->time_client * 60)  ){
							//last update client time exceeded  
							$sent = check_alert_sent( $ids , 0 ); 
							if ( $sent > 1 ) { //checks if alert to clients was sent	
								$service_users = get_users_notification( $ids, 0 ); //get all users for service
								foreach ($service_users as $kcl => $us) { 
									$sendto[] = $us['t']; 
								} 
							} 
							
						}
						
							if ( !check_alert_sent( $ids , 0 ) ){ //check if an alert was sent to prosa already
								 
								foreach ($prosa_users as $usr => $config) { //add prosa users with service configured 
									echo $usr . print_r($config, TRUE). ' >> ' .  $ids ;

									if ( $config[$ids] ){
										$sendto[] = $config[0];
										
									}
								}
								 
							} 
						
						  
						$message 	= "El servicio " . $Service->service . " se encuentra caído. "; 
						
						echo "<p> To send " . $Service->service . "(" . $Service->id_service . "): $message <br/> ";
						print_r( $sendto );
						echo "</p>";
						
						if ( send_notification($sendto, $ids, $message) ){
							$Log->write_log( $message );
							$sent++; 
							save_alert( 0, $ids, $message  );
						}
					}  
				}  else if ( $th > 0 ){
					foreach ($Service->indicators as $ki => $ind) {
						
						if ( $ind['total_transactions'] > 0 ){
							$state = $ind['total_rejected'] * 100 / $ind['total_transactions']; 
						} else {
							$state = 0;
						}
						
						if ( $th > $state ){ 
							if ( !check_alert_sent( $ids , $cli['ID_CLIENT']  ) ){ //check if an alert was sent to prosa already
									 
								$client_users 	= get_users_notification( $ids, $cli['CL_CODE'] );
								foreach ($client_users as $kcl => $us) { 
									$sendto[] = $us['t']; 
								} 
								foreach ($prosa_users as $usr => $config) { //add prosa users with service configured 
									if ( $config[$ids] )
										$sendto[] = $config[0];
								} 
								$message 	= "Se ha superado el umbral de rechazos para el servicio " . $Service->service . ". ";
								
								echo "<p> To send " . $Service->service . "(" . $Service->id_service . "): $message <br/> ";
								print_r( $sendto );;
								echo "</p>";
								
								if ( send_notification($sendto, $ids, $message) ){
									$sent++; 
									$Log->write_log( $message . " ( " . $cli['CL_CODE'] . " ) " );
									save_alert( $cli['ID_CLIENT'], $ids, $message  );
								}
							}
						} else {
							$Log->write_log(" " . $Service->service . " Threshold OK."); 
						}
					} 
				} else {
					//Nothing to do.
					$Log->write_log(" " . $Service->service . " OK.");
				}
			} else {
					$Log->write_log(" " . $Service->service . " has maintenance."); 
			}
		}
	}	
}

try{
/*	if ( $sent > 0 ){
		$push->send();
		$push->disconnect();
	}

	$errors = $push->getErrors();
	foreach ($errors as $ke => $err) {
		$Log->write_log("Push Error:" . print_r( $err , TRUE));
	}
*/
} catch (Exception $e ){
	$Log->write_log( $e );
}

echo "</pre>";

?>
