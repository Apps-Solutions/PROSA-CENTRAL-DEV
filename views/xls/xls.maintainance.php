<?php
if ( IS_ADMIN )
{ 
    //ID_MAINTENANCE, SE_SERVICE, MA_START, MA_END  
    $servicio = $record['SE_SERVICE'];
    $inicio = date( 'Y-m-d H:i:s', $record['MA_START']);
    $fin = date( 'Y-m-d H:i:s', $record['MA_END']);
    
    if ( $record['MA_END'] < time() )
    { 
	    $status = "Finalizado";
    }
    else if ( $record['MA_START'] < time() && $record['MA_END'] > time() )
    {
	    $status = "Iniciado";
    }
    else if ( $record['MA_START'] >= time() - 600 )
    {
	    $status = "Por comenzar"; 
    }
    else
    {
	    $status = "Pendiente";
    }

    echo $servicio.'|'.$inicio.'|'.$fin.'|'.$status;
}
?>