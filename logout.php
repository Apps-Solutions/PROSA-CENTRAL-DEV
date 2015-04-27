<?php
require_once 'init.php';
$Session->end_session();
header('Location: login.php')
?>