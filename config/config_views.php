<?php

define("HOME", 					"dashboard");
define("LOGIN",	 				"login");

/*** SERVICIOS ***
1	Pagos Diferidos
2	Preautorizador (PREA)
3	Payware Online
4	Switch Abierto
5	PROCOM
6	Cargos Automáticos
7	POS
8	ATM
9	Multiserv
10	SMS
**/
define("SRV_PAGOS_DIFERIDOS",	"s1"	);
define("SRV_PREA", 				"s2"	);
define("SRV_PAYWEAR_ONLINE",	"s3"	);
define("SRV_SWITCH_ABIERTO",	"s4"	);
define("SRV_PROCOM", 			"s5"	);
define("SRV_CARGOS_AUTOMATICOS","s6"	);
define("SRV_POS", 				"s7"	);
define("SRV_ATM",				"s8"	);
define("SRV_MULTISERV", 		"s9"	);
define("SRV_SMS",				"s10"	);

define("LST_CLIENT", 			"lst_cl");
define("LST_USER", 				"lst_us");
define("LST_ADM_USER",			"lst_adm_us");
 
define("NTF_FRM_ENVIO",			"ntf_frm_env");
define("NTF_FRM_THRESHOLD",		"ntf_frm_thr");
define("NTF_HISTORY", 			"ntf_hst"	 );

/** ERROR Pages **/
define("ERR_401", 			"err_401");	// Unauthorized
define("ERR_403", 			"err_403");	// Forbidden
define("ERR_404", 			"err_404"); // Not found  


$uiCommand = array(); 	//			permisos			Titulo						PHP													JS						CSS				AJAX

$uiCommand[ERR_401]		= array( 	array(1,2,3),  		"Unauthorized", 			DIRECTORY_VIEWS.DIRECTORY_BASE."401.php", 			"",						"",				""		);
$uiCommand[ERR_403]		= array( 	array(1,2,3),  		"Forbidden", 				DIRECTORY_VIEWS.DIRECTORY_BASE."403.php", 			"",						"",				""		);
$uiCommand[ERR_404]		= array( 	array(1,2,3),	  	"Not Found", 				DIRECTORY_VIEWS.DIRECTORY_BASE."404.php", 			"",						"",				""		);

$uiCommand[LOGIN]		= array( 	array(1,2,3),	 	"Iniciar Sesion", 			"frm.login.php",									"",						"",				""		);
$uiCommand[HOME]		= array( 	array(1,2,3),	  	"Inicio", 					DIRECTORY_VIEWS.DIRECTORY_BASE."dashboard.php", 	"",						"",				""		);

$uiCommand[LST_USER]	= array( 	array( 1 ),			"Usuarios", 				DIRECTORY_VIEWS."base/lst.user.php",  			array("user.js"),			"",				""		); 
$uiCommand[LST_ADM_USER]= array( 	array( 1 ),			"Usuarios PROSA", 			DIRECTORY_VIEWS."admin/lst.user.php",  			array("admin.user.js"),		"",				""		); 
$uiCommand[LST_CLIENT]	= array( 	array( 1 ),			"Clientes", 				DIRECTORY_VIEWS."admin/lst.client.php",  		array("admin.client.js"),	"",				""		);
/*** Notificaciones ***/
$uiCommand[NTF_FRM_ENVIO]		= 	array( array(1 ),		"Envío", 				DIRECTORY_VIEWS."alerts/send.php",  			array("alert.send.js"),		"",				""		); 
$uiCommand[NTF_FRM_THRESHOLD]	= 	array( array(1 ),		"Umbrales", 			DIRECTORY_VIEWS."alerts/threshold.php",  		array("moment.js","bootstrap-datetimepicker.js", "alert.threshold.js"),"",				""		); 
$uiCommand[NTF_HISTORY]			= 	array( array(1,2 ),		"Historial", 			DIRECTORY_VIEWS."alerts/lst.history.php",		array("alert.history.js", "moment.js","bootstrap-datetimepicker.js"),	"",				""		); 

/*** Servicios ***/
$uiCommand[SRV_PAGOS_DIFERIDOS] = 	array(	array(1,2,3 ),	"Pagos Diferidos", 		DIRECTORY_VIEWS."services/service.php", 	/*array("service.js")*/"",	array("srv.charts.css"),	""); 
$uiCommand[SRV_PREA] 			= 	array(	array(1,2,3 ), 	"Preautorizador (PREA)",DIRECTORY_VIEWS."services/service.php",  	/*array("service.js")*/"",	array("srv.charts.css"),	"");
$uiCommand[SRV_PAYWEAR_ONLINE] 	= 	array(	array(1,2,3 ), 	"Payware Online",		DIRECTORY_VIEWS."services/service.php",  	/*array("service.js")*/"",	array("srv.charts.css"),	""); 
$uiCommand[SRV_SWITCH_ABIERTO]  =	array(	array(1,2,3 ), 	"Switch Abierto", 		DIRECTORY_VIEWS."services/service.php", 	/*array("service.js")*/"",	array("srv.charts.css"),	"");
$uiCommand[SRV_PROCOM]			=	array(  array(1,2,3 ),	"PROCOM",				DIRECTORY_VIEWS."services/service.php",		/*array("service.js")*/"",	array("srv.charts.css"),	"");
$uiCommand[SRV_CARGOS_AUTOMATICOS]=	array(	array(1,2,3 ),  "Cargos Automáticos", 	DIRECTORY_VIEWS."services/service.php", 	/*array("service.js")*/"",	array("srv.charts.css"),	"");
$uiCommand[SRV_POS]		 		=	array(  array(1,2,3 ),	"POS",					DIRECTORY_VIEWS."services/service.php",		/*array("service.js")*/"",	array("srv.charts.css"),	""); 
$uiCommand[SRV_ATM] 	 		=	array(	array(1,2,3 ),  "ATM", 					DIRECTORY_VIEWS."services/service.php", 	/*array("service.js")*/"",	array("srv.charts.css"),	"");
$uiCommand[SRV_MULTISERV]		=	array(  array(1,2,3 ),	"Multiserv",			DIRECTORY_VIEWS."services/service.php",		/*array("service.js")*/"",	array("srv.charts.css"),	"");
$uiCommand[SRV_SMS]		 		=	array(  array(1,2,3 ),	"SMS",					DIRECTORY_VIEWS."services/service.php",		/*array("service.js")*/"",	array("srv.charts.css"),	"");

$config_menu = array( 'cmd' => 'root', 'lnk' => array(
	array( 	"cmd" => HOME, 		"prf" => array(1,2,3 ), 	"lbl" => "Inicio", 	"ico" => "fa-home", 	"lnk" => array() 	),
	array(  
		"cmd" => "#",
		"lbl" => "Servicios", 
		"prf" => array(1),
		"ico" => "fa-bar-chart-o",
		"lnk" => array(  
					array( "cmd" => SRV_POS, 				'prf' => array(1,2,3),	"lbl" => "POS", 					"ico" => "", 	"lnk" => array() ), 
					array( "cmd" => SRV_ATM, 				'prf' => array(1,2,3),	"lbl" => "ATM", 					"ico" => "", 	"lnk" => array() ),
					 
					array( "cmd" => SRV_CARGOS_AUTOMATICOS,	'prf' => array(1,2,3),	"lbl" => "Cargos Automáticos", 		"ico" => "",	"lnk" => array() ), 
					array( "cmd" => SRV_MULTISERV, 			'prf' => array(1,2,3),	"lbl" => "Multiserv",			 	"ico" => "",	"lnk" => array() ),
					array( "cmd" => SRV_PAGOS_DIFERIDOS,	'prf' => array(1,2,3),	"lbl" => "Pagos Diferidos",			"ico" => "",	"lnk" => array() ),
					array( "cmd" => SRV_PAYWEAR_ONLINE,		'prf' => array(1,2,3),	"lbl" => "Payware Online",			"ico" => "",	"lnk" => array() ),
					array( "cmd" => SRV_PREA,		 		'prf' => array(1,2,3),	"lbl" => "Preautorizador (PREA)",	"ico" => "",	"lnk" => array() ),
					array( "cmd" => SRV_PROCOM,			'prf' => array(1,2,3),	"lbl" => "PROCOM",					"ico" => "",	"lnk" => array() ), 
					array( "cmd" => SRV_SMS, 				'prf' => array(1,2,3),	"lbl" => "SMS", 					"ico" => "", 	"lnk" => array() ),
					array( "cmd" => SRV_SWITCH_ABIERTO,		'prf' => array(1,2,3),	"lbl" => "Switch Abierto",			"ico" => "",	"lnk" => array() )
				 )
	),
	array(  
		"cmd" => "#",
		"lbl" => "Notificaciones", 
		'prf' => array(1,2,3),
		'ico' => "fa-exclamation-circle", 
		"lnk" => array(
					array( "cmd" => NTF_FRM_ENVIO,	 	'prf' => array(1),		"lbl" => "Envío",			"ico" => "fa-envelope",		"lnk" => array() ),
					array( "cmd" => NTF_FRM_THRESHOLD, 	'prf' => array(1,)	,	"lbl" => "Umbrales",		"ico" => "fa-cogs",			"lnk" => array() ), 
					array( "cmd" => NTF_HISTORY,		'prf' => array(1,2,3),	"lbl" => "Historial",		"ico" => "fa-clock-o",	 	"lnk" => array() ) 
				 )
	),
	array( "cmd" => LST_CLIENT, 	'prf' => array(1),	"lbl" => "Clientes", 		"ico" => "fa-users",		"lnk" => array() )
//	array( "cmd" => LST_USER, 		'prf' => array(1),	"lbl" => "Usuarios",  		"ico" => "fa-user",		 	"lnk" => array() ) 
 )
);
?>