<?php
if ( IS_ADMIN )
{ 
    //id_alert, al_timestamp, se_service, cl_client, al_text, al_user
    $fecha = date( 'Y-m-d', $record['AL_TIMESTAMP']);
    $hora = date( 'H:i:s', $record['AL_TIMESTAMP']);
    $servicio = $record['SE_SERVICE'];
    $cliente = $record['CL_CLIENT'];
<<<<<<< HEAD
    $notif = $record['AL_TEXT'];
=======
    $notif = urlencode( $record['AL_TEXT'] );
>>>>>>> origin/master
    $al_user = $record['AL_USER'];
    

    echo $fecha.'|'.$hora.'|'.$servicio.'|'.$cliente.'|'.$notif.'|'.$al_user;
}
?>