<?php
session_start(); 
define("PFX_SYS", "pra_");

					
					echo $_SESSION[PFX_SYS . 'name']."<br/>";
					echo $_SESSION[PFX_SYS . 'token']."<br/>";
					echo $_SESSION[PFX_SYS . 'email']."<br/>";
					echo $_SESSION[PFX_SYS . 'user']."<br/>";
					echo $_SESSION[PFX_SYS . 'profile']."<br/>";
					
?>