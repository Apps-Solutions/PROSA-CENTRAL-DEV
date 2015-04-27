<?php

  global $response;
  global $Session;
  global $Settings;

  if ($Session->is_admin())
  {
      require_once DIRECTORY_CLASS . "class.agenda.php";

      $agenda = new Agenda();

      switch ($action)
      {
          case 'get_clients_users':

              $id = ( isset($_REQUEST['id']) && is_numeric($_REQUEST['id']) && $_REQUEST['id'] > 0 ) ? $_REQUEST['id'] : 0;
              $tipo = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';

              if ($id > 0 && $tipo != '')
              {
                  $rows = $agenda->get_service($id, $tipo);

                  $users = $agenda->get_users_attached($id);

                  if (count($users) > 0 || count($rows) > 0)
                  {
                      $response['success'] = TRUE;
                      $response['clients'] = $rows;
                      $response['users'] = $users;
                  }
                  else
                  {
                      $response['error'] = FALSE;
                  }
              }
              else
              {
                  $response['error'] = "Invalid ID";
              }

              break;
          case 'get_services_clients':

              $id = ( isset($_REQUEST['id']) && is_numeric($_REQUEST['id']) && $_REQUEST['id'] > 0 ) ? $_REQUEST['id'] : 0;
              $tipo = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';

              if ($id > 0 && $tipo != '')
              {
                  $rows = $agenda->get_service($id, $tipo);

                  if (count($rows) > 0)
                  {
                      $response['success'] = TRUE;
                      $response['services'] = $rows;
                  }
                  else
                  {
                      $response['error'] = FALSE;
                  }
              }
              else
              {
                  $response['error'] = "Invalid ID Client";
              }
              break;

          case 'get_all_clients':

              $services = isset($_REQUEST['services']) ? $_REQUEST['services'] : '';
              $type = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';

              $services = explode(',', $services);

              if (count($services) > 0)
              {
                  $users = array();

                  $clientes = array();

                  foreach ($services as $k => $val)
                  {
                      if (!empty($val))
                      {
                          $resultado = $agenda->get_service($val, $type);

                          if (count($resultado) > 0)
                          {
                              $clientes[] = $resultado;
                          }
                      }
                  }

                  foreach ($services as $k => $val)
                  {
                      if (!empty($val))
                      {
                          $result = $agenda->get_users_attached($val);

                          if (count($result) > 0)
                          {
                              $users[] = ($result);
                          }
                      }
                  }

                  if (count($clientes) > 0 || count($users) > 0)
                  {
                      $response['success'] = TRUE;

//                      print_r($clientes);

                      $response['clients'] = ($clientes);
                      $response['users'] = ($users);
                  }
                  else
                  {
                      $response['error'] = FALSE;
                  }
              }
              else
              {
                  $response['error'] = "Servicios invalido";
              }

              break;
      }
  }
  else
  {
      $response['error'] = "Restricted action.";
  }