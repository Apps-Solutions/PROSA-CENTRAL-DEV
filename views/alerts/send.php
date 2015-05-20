<?php
  if (!IS_ADMIN)
  {
      header("Location: index.php?command=" . ERR_403);
      die();
  }

  global $Session;

  require_once DIRECTORY_CLASS . "class.agenda.php";
  $agenda = new Agenda();
?>

<script>

    $(document).ready(function ()
    {
        $("#lbl_num_caract").html(max_msg_length);
    });
</script>

<div class="row" id="section-header">
    <div class="col-xs-12">
        <h1 style="text-align: center; border:none;"> Notificaciones <i class="fa fa-exclamation-circle"> </i> </h1>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 col-md-6">
        <h2> <?php echo $Index->title ?> </h2>
    </div>
</div>


<div class="row"> &nbsp; </div>
<div class="row">
    <div class="col-xs-12">
        <label id="lbl_num_caract"></label> caracteres disponible(s).
        <textarea id='inp_message' name='mesage' placeholder='Mensaje' style="padding: 15px; width:100%;" onkeyup="validate_msg_length();"></textarea>
    </div>
</div>
<div class="row">
    <div class="col-xs-1 col-md-4">
    </div>
    <div class="col-xs-6 col-md-4">
        <center>
            <img id="ajax-load" />
        </center>
    </div>
    <div class="col-xs-5 col-md-4 text-right">
        <input type="button" id="inp_submit" name="submit" value="Enviar" class="btn" style="width: 180px;" onclick="send_notification();" />
    </div>
</div>

<style>
  #div_loader
  {
    display: none; 
    z-index:1001;
    position: absolute; 
    top: 0%; 
    left: 0%; 
    width: 100%; 
    height: 100%; 
    background-color: rgba(119, 119, 119, 0.77);
  }
</style>

<div class="row">
  <div id="div_loader" style="text-align: center; vertical-align: middle; " >
    <br/><br/>
    <div>
      <h1>Enviando notificacion, espere por favor...</h1>
    </div>
  </div>
</div>

<div id='section-content' class='row' style="margin-top: 20px;">
    <div class="col-xs-12 col-md-6">
        <div class="col-xs-12">
            <div class="row">
                <ul class="nav nav-tabs" role="tablist">
                    <li><a href="#">Servicios</a></li>
                </ul>
                <div class="row" style="height: 280px;">
                    <div class='col-xs-12'>
                        <table class="table table-striped table-bordered clearfix" >
                            <thead>
                                <tr><td>Servicio</td><td align="center"> <input type="checkbox" id='inp_serv_all' value='' onchange=""/></td></tr>
                            </thead>
                            <tbody>
                                <?php
                                  $services = $Session->get_services();
                                  foreach ($services as $k => $service)
                                  {
                                      $record = array();
                                      $record['id_service'] = $service['id_service'];
                                      $record['service'] = utf8_decode($service['se_service']);

                                      $record['checked'] = FALSE;
                                      $record['pfx'] = 'notif_user_';
                                      $record['function'] = "service_row('" . $service['id_service'] . "','service_client')";

                                      ob_start();
                                      require DIRECTORY_VIEWS . "/lists/lst.service_chk.php";
                                      echo ob_get_clean();
                                  }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row"> &nbsp; </div>
    </div>

    <div class="col-xs-12 col-md-6">
        <div class="col-xs-12">
            <div class="row">
                <ul class="nav nav-tabs" role="tablist"> 
                    <li><a href="#">Bancos</a></li> 
                </ul>

                <div class="row" style="min-height: 280px; ">
                    <div class='col-xs-12'>
                        <div class="tbl_srch_frm">
                            <div class='col-xs-8' >
                                <input type='text' id='inp_cli_search' name='cli_search' class="form-control" onkeyup="search_notificatios('cli_search', 'clientes_table');"  />
                            </div>
                            <div class='col-xs-4 text-center' >
                                <!--<input type='button' id='inp_cli_srch_submit' name='cli_search_submit' value ='Buscar' class="btn" onclick=""/>-->
                                <button class='btn' id='inp_cli_srch_submit' name='cli_search_submit' style="background: #990D17; color: #FFFFFF; "
							onclick=""/> Buscar</button>
                            </div>
                        </div>
                    </div>
                    <div class='col-xs-12' style="overflow: auto; height: 500px;">
                        <table class="table table-striped table-bordered clearfix" id="clientes_table" >
                            <thead>
                                <tr><td>FIID</td><td>Banco</td><td align="center"> <input type="checkbox" id='inp_cli_all' value='' onchange=""/></td></tr>
                            </thead>
                            <tbody><?php echo $agenda->get_clients_table(); ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row"> &nbsp; </div>
    </div>

    <div class="col-xs-12 col-md-6">
        <div class="row">
            <div class="col-xs-12">
                <ul class="nav nav-tabs" role="tablist">
                    <li><a href="#">Usuarios</a></li>
                </ul>

                <div class="row" style="height: 280px;">
                    <div class='col-xs-12'>
                        <div class="tbl_srch_frm">
                            <div class='col-xs-8' >
                                <input type='text' id='inp_cli_search_us' name='cli_search_us' class="form-control" onkeyup="search_notificatios('cli_search_us', 'table_users');" />
                            </div>
                            <div class='col-xs-4 text-center' >
                                <!--<input type='button' id='inp_cli_srch_submit' name='cli_search_submit' value ='Buscar' class="btn" />-->
                                <button class='btn' id='inp_cli_srch_submit' name='cli_search_submit' style="background: #990D17; color: #FFFFFF; "
							onclick=""/> Buscar</button>
                            </div>
                        </div>
                    </div>
                    <div class='col-xs-12'>
                        <table class="table table-striped table-bordered clearfix" id="table_users" >
                            <tr> <td> Seleccionar Todos </td> 	<td align="center"> <input type="checkbox" id='inp_usr_all' value='0' class='.inp_usuario' /></td></tr>
                            <!--<tbody><?php echo $agenda->get_users_table(); ?></tbody>  Para prueba mysql si se deja se conecta al ldap-->
                            <tr> 
	<td> Alvaro Ontiveros H.  7725 (alvaro.ontiveros@prosa.com.mx) </td> 
	<td align="center"> 
		<input type="checkbox" id='inp_usr_aontiver' name='users[]' value='aontiver' class='inp_user_chk' />
	</td> 
</tr><tr> 
	<td> Beatriz Elena Huesca Guevara (beatriz.huesca@prosa.com.mx) </td> 
	<td align="center"> 
		<input type="checkbox" id='inp_usr_bhguevar' name='users[]' value='bhguevar' class='inp_user_chk' />
	</td> 
</tr><tr> 
	<td> Claudio Copernico Avila Luna  7774 (claudio.avila@prosa.com.mx) </td> 
	<td align="center"> 
		<input type="checkbox" id='inp_usr_cavila' name='users[]' value='cavila' class='inp_user_chk' />
	</td> 
</tr><tr> 
	<td> Hector Daniel Cedillo Garibay (hector.cedillo@prosa.com.mx) </td> 
	<td align="center"> 
		<input type="checkbox" id='inp_usr_hcgariba' name='users[]' value='hcgariba' class='inp_user_chk' />
	</td> 
</tr><tr> 
	<td> Jesus Urbina Alcantara (jesus.urbina@prosa.com.mx) </td> 
	<td align="center"> 
		<input type="checkbox" id='inp_usr_jualcant' name='users[]' value='jualcant' class='inp_user_chk' />
	</td> 
</tr><tr> 
	<td> Alfonso Altamirano Plasencia (alfonso.altamirano@prosa.com.mx) </td> 
	<td align="center"> 
		<input type="checkbox" id='inp_usr_aaplasen' name='users[]' value='aaplasen' class='inp_user_chk' />
	</td> 
</tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row"> &nbsp; </div>
    </div>
</div>


<div class="row"> &nbsp; </div>
<div class="row"> &nbsp; </div>
<div class="row"> &nbsp; </div>


</form>

