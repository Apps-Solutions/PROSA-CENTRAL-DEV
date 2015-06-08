<?php
/*****************	WEB Options Definitions ******************/
define("SYS_TITLE",				"Proapp");
define("SYS_URL",				"http://");
define("WEB_MAIL",				"admin@gruposellcom.com");
define('SYS_MAIL', 				'admin@gruposellcom.com');
define('PFX_SYS', 				'pra_');

  
/****************** Main DB Configuration *****************
define ("ORCL_HOST",            '192.168.105.89');
define ("ORCL_PORT",            '1527');
define ("ORCL_SERVICE_NAME",    'ROP');
define ("ORCL_USER_NAME",       'AppWeb');
define ("ORCL_PASSWORD",        'Apps_mo1');

define ("PFX_MAIN_DB", 			'APP.pra_');
define ("PFX_SRV_DB", 			'SED.');
define ("PFX_SMS_DB", 			'SMS.');*/ 

define("DB_HOST", 				'localhost');
define("DB_USERNAME", 			'root');
define("DB_PASSWORD", 			'root');
define("DB_NAME", 				'prosa_dev');
define("PFX_MAIN_DB", 			'pra_');

/****************** Time intervals Configuration ******************/ 
define ("TIME_DB_UPDATE",		10); 	// Intervalo de tiempo de actualización de tablas de BD de transacciones

/****************** LDAP Configuration ******************/ 
define("LDAP_DOMAIN", 			"prosa.com.mx");
define("LDAP_HOST", 			"ldapApp.prosa.com.mx");
define("LDAP_PORT", 			389);
define("LDAP_BASEDN", 			"o=prosa.com.mx,o=isp");
//define("LDAP_USER", 			'appmnoadm');
define("LDAP_USER", 			'uid=appmnoadm,o=prosa.com.mx,o=isp');
define("LDAP_PASSWORD", 		'xbh8R65I');

define("LDAP_GRP_ADMIN"		, 	"GenteProsa");
define("LDAP_GRP_CLI_ADM"	, 	"Cliente");
define("LDAP_GRP_CLI"		, 	"Cliente");

/**************** 	Paths Definitions	 ******************/
define("DIRECTORY_CLASS",		PATH . "class/");
define("DIRECTORY_VIEWS", 		PATH . "views/");
define("DIRECTORY_BASE", 		PATH . "base/");
define("DIRECTORY_TEMPLATES",	PATH . "templates/"); 
define("DIRECTORY_UPLOADS",		PATH . "uploads/"); 
define("DIRECTORY_FUNCS",		PATH . "funcs/");  
define("DIRECTORY_AJAX",		PATH . "ajax/"); 
define("DIRECTORY_IMAGES",		PATH . "img/"); 

/**************** 	Errors Definitions	 ****************/
$error_num = 23;
define("LOGIN_SUCCESS", 		$error_num++);
define("LOGIN_BADLOGIN",  		$error_num++);
define("LOGIN_SSH_FAILURE", 	$error_num++);

$error_num = 100;
define("ERR_DB_CONN",			$error_num++);
define("ERR_DB_EXEC",			$error_num++);
define("ERR_DB_QRY",			$error_num++);
define("ERR_DB_NOT_FOUND",		$error_num++);

$error_num = 200;
define("SES_RESTRICTED_ACTION", $error_num++);
define("SES_RESTRICTED_ACCESS", $error_num++);
define("SES_INVALID_ACTION", 	$error_num++);
define("SES_INVALID_ACCESS", 	$error_num++);

// Validation
$error_num = 300;
define("ERR_VAL_EMPTY",			$error_num++);
define("ERR_VAL_INVALID",		$error_num++);
define("ERR_VAL_NOT_UNIQUE",	$error_num++);
define("ERR_VAL_NOT_INT",		$error_num++);
define("ERR_VAL_NOT_DATE",		$error_num++);
define("ERR_VAL_NOT_EMAIL",		$error_num++); 

$error_num = 400;
define("ERR_FILE_INVALID",		$error_num++);
define("ERR_FILE_UPLOAD",		$error_num++);
define("ERR_FILE_PERMISSION",	$error_num++);
define("ERR_FILE_NOT_FOUND",	$error_num++);

/************** 	Views Configuration 	****************/
$_command=1001;

/**************		LOGGING Definitions 	****************/
define('LOG_DIR', 'log/');
define('LOG_FILE', 'pra_log');
define('LOG_TMPLT', '[%s] %s @ %s: %s');
define('LOG_MAX_SIZE', '1073741824'); // 1G = 1073741824 bytes

define('LOG_PRC_DOWN',  1);
define('LOG_TRANS_ERR', 2);
define('LOG_DB_ERR',  	3);
define('LOG_SESS_ERR',  4);
define('LOG_INFO_ERR',  5);
define('LOG_API_ERR',   6);

define('COLOR1_DEFAULT', '#EFE7E3');
define('COLOR2_DEFAULT', '#fafafa');
define('COLOR3_DEFAULT', '#454545');
define('COLOR4_DEFAULT', '#CECECE');
define('COLOR5_DEFAULT', '#A80000');

define('MAX_MSG_LENGTH', '250');


/*CERTIFICADOS NOTIFICACION*/
define('SERVER_CERTIFICATE','server_certificates_bundle_production.pem');
define('ROOT_AUTORITY',     'entrust_root_certification_authority.pem');

?>