<?php 
session_start();
if (!isset($_GET['dbg'])) 
	ini_set('display_errors',  FALSE);
else ini_set('display_errors', TRUE);

define( 'PATH', '' ); 
date_default_timezone_set("America/Mexico_City");
define("DIRECTORY_CONFIG", 		"config/"); 
require_once(DIRECTORY_CONFIG . 'config.php');
require_once(DIRECTORY_CONFIG . 'config_views.php');

include_once(DIRECTORY_CLASS  . 'class.object.php');
include_once(DIRECTORY_CLASS  . 'class.log.php');

//include_once(DIRECTORY_CLASS  . 'class.oracle_db.php');
include_once(DIRECTORY_CLASS  . 'class.pdo_mysql.php'); 

include_once(DIRECTORY_CLASS  . 'class.session.php');
include_once(DIRECTORY_CLASS  . 'class.index.php');

require_once DIRECTORY_CLASS . "class.datatable.php";
include_once(DIRECTORY_FUNCS . 'func.php');
include_once(DIRECTORY_CLASS . 'class.ldap.php');

$Log 		= new Log(); 

$Session 	= new Session();
$Index		= new Index();

include_once(DIRECTORY_CLASS  . 'class.validate.php');
$Validate 	= new Validate(); 
?>