<?php 
session_start(); 
define("PFX_SYS", "pra_");

					$_SESSION[PFX_SYS . 'name']		= "Carlos Servín";
					$_SESSION[PFX_SYS . 'token']	= "123456";
					$_SESSION[PFX_SYS . 'email']	= "carlos.servin@gruposellcom.com";
					$_SESSION[PFX_SYS . 'user']		= "cservin"; 
					$_SESSION[PFX_SYS . 'profile']	= "1";
					
					
					echo $_SESSION[PFX_SYS . 'name']."<br/>";
					echo $_SESSION[PFX_SYS . 'token']."<br/>";
					echo $_SESSION[PFX_SYS . 'email']."<br/>";
					echo $_SESSION[PFX_SYS . 'user']."<br/>";
					echo $_SESSION[PFX_SYS . 'profile']."<br/>";
?>