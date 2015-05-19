<?php
require 'init.php';

    $action = ( isset($_REQUEST['action'] ) ? $_REQUEST['action']  : NULL );
    
    
    if( $action != NULL)
    {
		$filename = 'indicadores_';
		$id_client = ( isset($_REQUEST['id_client'] ) ? $_REQUEST['id_client']  : 0 );
		
		switch($action)
		{
			case 's1':
				require_once DIRECTORY_CLASS . 'class.PagosDiferidos.php';
				$Service = new PagosDiferidos( $id_client );
				$xls = $Service->get_indicators_xls();
				$filename .= 'PagosDiferidos';
			break;
				
			case 's2':
				require_once DIRECTORY_CLASS . 'class.PREA.php';
				$Service = new PREA( $id_client );
				$xls = $Service->get_indicators_xls();
				$filename .= 'PREA';
			break;
			
			case 's3':
				require_once DIRECTORY_CLASS . 'class.Payware.php';
				$Service = new Payware( $id_client );
				$xls = $Service->get_indicators_xls();
				$filename .= 'Payware';
			break;
			
			case 's4':
				require_once DIRECTORY_CLASS . 'class.SwitchAbierto.php';
				$Service = new SwitchAbierto( $id_client );
				$xls = $Service->get_indicators_xls();
				$filename .= 'SwitchAbierto';
			break;
			
			case 's5':
				require_once DIRECTORY_CLASS . 'class.PROCOM.php';
				$Service = new PROCOM( $id_client );
				$xls = $Service->get_indicators_xls();
				$filename .= 'PROCOM';
			break;
			
			case 's6':
				require_once DIRECTORY_CLASS . 'class.CargosAutomaticos.php';
				$Service = new CargosAutomaticos( $id_client );
				$xls = $Service->get_indicators_xls();
				$filename .= 'CargosAutomaticos';
			break;
			
			case 's7':
				require_once DIRECTORY_CLASS . 'class.POS.php';
				$Service = new POS( $id_client );
				$xls = $Service->get_indicators_xls();
				$filename .= 'POS';
			break;
			
			case 's8':
				require_once DIRECTORY_CLASS . 'class.ATM.php';
				$Service = new ATM( $id_client );
				$xls = $Service->get_indicators_xls();
				$filename .= 'ATM';
			break;
			
			case 's9':
				require_once DIRECTORY_CLASS . 'class.Multiserv.php';
				$Service = new Multiserv( $id_client );
				$xls = $Service->get_indicators_xls();
				$filename .= 'Multiserv';
			break;
			
			case 's10':
				require_once DIRECTORY_CLASS . 'class.SMS.php';
				$Service = new SMS( $id_client );
				$xls = $Service->get_indicators_xls();
				$filename .= 'SMS';
			break; 
			
			default:
				require_once DIRECTORY_CLASS . "class.datatable.php";
				$tabla = new DataTable($action);
				$filename = $action;
				
				if( isset($_REQUEST['date_srch']) && $_REQUEST['date_srch'] != '' )
				{
					if( isset($_REQUEST['date_start']) && $_REQUEST['date_start'] != '' )
					{
						$tabla->set_filter( $_REQUEST['date_srch'], strtotime( $_REQUEST['date_start'] ), '>=' );
					}
					
					if( isset($_REQUEST['date_end']) && $_REQUEST['date_end'] != '' )
					{
						$tabla->set_filter( $_REQUEST['date_srch'], strtotime( $_REQUEST['date_end'] ), '<=' );
					}
				}
				
				$xls = $tabla->get_list_xls();
			break;
		}
		
		header("Content-Type: application/vnd.ms-excel");
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header('Content-Disposition: attachment; filename="'.$filename.'_'.date("Y-m-d").'.xls"');
		header("Content-Transfer-Encoding: binary");
		header("Pragma: no-cache");
		header("Expires: 0");
				
		echo $xls;
    }
?>