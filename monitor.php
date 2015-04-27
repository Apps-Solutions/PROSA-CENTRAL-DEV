<?php
ini_set('display_errors',TRUE);
date_default_timezone_set("America/Mexico_City");

define( 'PATH', dirname(__FILE__) . "/" );

define("DIRECTORY_CONFIG", PATH . "config/");
require_once(DIRECTORY_CONFIG . 'config.php'); 

include_once(DIRECTORY_CLASS  . 'class.object.php');
include_once(DIRECTORY_CLASS  . 'class.log.php');
include_once(DIRECTORY_CLASS  . 'class.oracle_db.php');  
//require_once DIRECTORY_CLASS .  "class.ldap.php";

require_once DIRECTORY_CLASS . 'class.service.php';
require_once DIRECTORY_CLASS . 'class.PagosDiferidos.php';
require_once DIRECTORY_CLASS . 'class.PREA.php';
require_once DIRECTORY_CLASS . 'class.Payware.php';
require_once DIRECTORY_CLASS . 'class.SwitchAbierto.php';
require_once DIRECTORY_CLASS . 'class.PROCOM.php';
require_once DIRECTORY_CLASS . 'class.CargosAutomaticos.php';
require_once DIRECTORY_CLASS . 'class.POS.php';
require_once DIRECTORY_CLASS . 'class.ATM.php';
require_once DIRECTORY_CLASS . 'class.Multiserv.php';
require_once DIRECTORY_CLASS . 'class.SMS.php';
require_once DIRECTORY_CLASS . "class.notification.php";

require_once DIRECTORY_CLASS . "class.agenda.php";
require DIRECTORY_CLASS . 'ApnsPHP/Autoload.php';


function get_users_prosa_config(){
//	global $Ldap;
	global $db; 
	$config = array();
	$branch = "OU=GenteProsa,OU=prosa.com.mx"; 
	//$users 	= $Ldap->search_branch( $branch );
	$users = array( 'bhguevar', 'aontiver', 'mxbdcal1');


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
		
	}
	else{
		$Log->write_log("Error retrieving users for notifications from the DataBase."); 
	}
	return $users;
}

function send_notification( $users, $service, $message){
	  
	global $push;
	foreach ($users as $kus => $user) {
		
		$msg = new ApnsPHP_Message($user); 
		$msg->setCustomIdentifier(sprintf("Message-Badge-%03d", $kus + 1));
		$msg->setBadge($kus + 1);

		$msg->setText($message);
		$msg->setSound('prosa.aiff');

		$push->add($msg); 
		
		$Log->write_log( "Sending " . $user . ": " . $message );
	}  
}

function save_alert( $id_client, $id_service, $message, $th = 0 ){
	global $script_user;
	global $db;
	
	$query = "INSERT INTO " . PFX_MAIN_DB . "alert (id_alert,al_cl_id_client,al_timestamp,al_se_id_service,al_text,al_status) "
                          . "VALUES(:id_alert,:al_cl_id_client,:al_timestamp,:al_se_id_service,:al_text,:al_status)";
	
	$id_alert = $db->get_id(PFX_MAIN_DB . "alert", "id_alert"); 
	$values = array(':id_alert' => $id_alert,
			       ':al_cl_id_client' 	=> $id_client,
			       ':al_timestamp' 		=> time(),
			       ':al_se_id_service' 	=> $id_service,
			       ':al_text' 			=> $message,
			       ':al_status'			=> 1,
			       ':al_user' 			=> $script_user
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
$db = new oracle_db();
//$Ldap = new LDAP();
$agenda = new Agenda();

$script_user = "System Monitor";
$query = " SELECT * FROM " . PFX_MAIN_DB . "service INNER JOIN pra_threshold ON th_se_id_service = id_service ";

$services = $db->query( $query );

$notification = new NOTIFICATION();
$prosa_users = get_users_prosa_config(); 

$push = new ApnsPHP_Push( ApnsPHP_Abstract::ENVIRONMENT_PRODUCTION, PATH . 'certificados_pem/' . SERVER_CERTIFICATE );
$push->setRootCertificationAuthority(PATH . 'certificados_pem/' . ROOT_AUTORITY);
$push->connect();

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
			case 1: $Service = new PagosDiferidos( $cli['ID_CLIENT'] ); break; 
			case 2: $Service = new PREA( $cli['ID_CLIENT']  ); 			break; 
			case 3: $Service = new Payware( $cli['ID_CLIENT']  ); 		break; 
			case 4: $Service = new SwitchAbierto( $cli['ID_CLIENT']  ); break; 
			case 5: $Service = new PROCOM( $cli['ID_CLIENT']  ); 		break; 
			case 6: $Service = new CargosAutomaticos( $cli['ID_CLIENT']  ); break; 
			case 7: $Service = new POS( $cli['ID_CLIENT']  ); 			break; 
			case 8: $Service = new ATM( $cli['ID_CLIENT']  ); 			break; 
			case 9: $Service = new Multiserv( $cli['ID_CLIENT']  ); 	break; 
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
						else {
							if ( !check_alert_sent( $ids , 0 ) ){ //check if an alert was sent to prosa already
								 
								foreach ($prosa_users as $usr => $config) { //add prosa users with service configured 
									if ( $config[$ids] )
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
	if ( $sent > 0 ){
		$push->send();
		$push->disconnect();
	}

	$errors = $push->getErrors();
	foreach ($errors as $ke => $err) {
		$Log->write_log("Push Error:" . print_r( $err , TRUE));
	}

} catch (Exception $e ){
	$Log->write_log( $e );
}

echo "</pre>";

?>
