<?php

  global $response;
  global $Session;
  global $Settings;

  if ($Session->is_admin())
  {
      switch ($action)
      {
          case 'send_notification':

              require_once DIRECTORY_CLASS . "class.notification.php";

              $mesage = isset($_REQUEST['mesage']) ? addslashes($_REQUEST['mesage']) : '';
              $users = isset($_REQUEST['users']) ? ($_REQUEST['users']) : array();
              $cliente = isset($_REQUEST['clients']) ? $_REQUEST['clients'] : '';
              $servicio = isset($_REQUEST['services']) ? $_REQUEST['services'] : '';


              if ($users != '' > 0 && $mesage != '' && $cliente != '' && $servicio != '')
              {
                  $cliente = explode(",", $cliente);
                  $servicio = explode(",", $servicio);
                  $users = explode(",", $users);

                  $notification = new NOTIFICATION();

                  $result = $notification->send_notification($users, $mesage, $cliente, $servicio);
                  
                  if ($result)
                  {
                      $response['success'] = TRUE;
                      //$response['users'] = $users;
                  }
                  else {
                  
	                  if (count($notification->error) > 0)
	                  {
	                      $response['error'] = $notification->error[0];
	                  }
	                  else
	                  {
	                      $response['error'] = "Ocurrió un error al enviar la notificación. ";
	                  }
				}
              }
              else
              {
                  $response['error'] = "Escribe los datos obligatorios.";
              }

              break;
      }
  }
  else
  {
      $response['error'] = "Restricted action.";
  }