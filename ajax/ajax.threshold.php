<?php 
global $response; 
global $Session;
global $Settings;

if ( $Session->is_admin() )
{
    switch ( $action )
    {
	    case 'get_maintenance_window':
		    require_once DIRECTORY_CLASS . "class.threshold.php";
		    $id_main = ( isset($_POST['id_main']) && is_numeric($_POST['id_main']) && $_POST['id_main'] > 0 ) ? $_POST['id_main'] : 0;
		    
		    if($id_main > 0 )
		    {
			$maint = new Threshold();
			$info = $maint->info_maintenance($id_main);
			
			if($info)
			{
			    $response['success'] = TRUE;
			    $response['info'] = $info;
			}
			else
			{
			    $response['error'] = 'Ocurrio un error en la BBDD al intentar obtener la informacion.';
			}
			
		    }
		    else
		    {
			$response['error'] = 'Mantenimiento invalido.';
		    }
		    
		    
		break;
	    
	    case 'srch_maintenance':
		    require_once DIRECTORY_CLASS . "class.threshold.php";
		    
		    if ( isset($_POST['filtros']) && $_POST['filtros'] != '' && isset($_POST['valores']) && $_POST['valores'] != ''
			&& isset($_POST['oprs']) && $_POST['oprs'] != '' && isset($_POST['format']) && $_POST['format'] != '' )
		    {
			
			$maint = new Threshold();
			$html = $maint->filter_list_maintainance_html($_POST['filtros'], $_POST['valores'], $_POST['oprs'], $_POST['format']);
			
			if($html)
			{
			    $response['html'] = $html;
			    $response['success'] = TRUE;
			}
			
		    }
		    else
		    {
			$response['error'] = 'Parametros invalidos.';
		    }
		    
		break;
	    
	    
	    default:
		$response['error'] = "Invalid action.";
	        break;
    }
}
else
{
    $response['error'] = "Restricted action.";
}
