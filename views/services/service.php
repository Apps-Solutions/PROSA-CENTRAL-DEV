<?php

if ($Session->is_admin()){
	require_once DIRECTORY_CLASS . 'class.agenda.php';
	$agenda = new Agenda(); 
	if ( isset( $_POST['serv_id_client'])  && $Validate->is_integer( $_POST['serv_id_client']) && $_POST['serv_id_client'] > 0 ){
		$id_client = $_POST['serv_id_client'];
	} else {
		$id_client = 0;
	}
	
} else { 
	$ids = substr($Index->command, 1); 
	if ( $Session->has_service( $ids )){ 
		$id_client = $Session->get_user_client();
	} else {
		require_once DIRECTORY_VIEWS. 'base/403.php';
		die();
	}
}

switch ( $Index->command ) {
	case 's1':
		require_once DIRECTORY_CLASS . 'class.PagosDiferidos.php';
		$Service = new PagosDiferidos( $id_client ); 
		break; 
	case 's2':
		require_once DIRECTORY_CLASS . 'class.PREA.php';
		$Service = new PREA( $id_client ); 
		break; 
	case 's3':
		require_once DIRECTORY_CLASS . 'class.Payware.php';
		$Service = new Payware( $id_client ); 
		break; 
	case 's4':
		require_once DIRECTORY_CLASS . 'class.SwitchAbierto.php';
		$Service = new SwitchAbierto( $id_client ); 
		break; 
	case 's5':
		require_once DIRECTORY_CLASS . 'class.PROCOM.php';
		$Service = new PROCOM( $id_client ); 
		break; 
	case 's6':
		require_once DIRECTORY_CLASS . 'class.CargosAutomaticos.php';
		$Service = new CargosAutomaticos( $id_client ); 
		break; 
	case 's7':
		require_once DIRECTORY_CLASS . 'class.POS.php';
		$Service = new POS( $id_client ); 
		break; 
	case 's8':
		require_once DIRECTORY_CLASS . 'class.ATM.php';
		$Service = new ATM( $id_client ); 
		break; 
	case 's9':
		require_once DIRECTORY_CLASS . 'class.Multiserv.php';
		$Service = new Multiserv( $id_client ); 
		break; 
	case 's10':
		require_once DIRECTORY_CLASS . 'class.SMS.php';
		$Service = new SMS( $id_client ); 
		break; 
	default:
		require_once DIRECTORY_VIEWS. 'base/404.php';
		die();
		break;
}

if ( isset( $_GET['dbg'])){
echo "<pre>";
var_dump( $Service );
echo "</pre>";
}
?>   
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
	google.load("visualization", "1", {packages:["corechart"]}); 
	function change_graf( cual, idx ){ 
		if ( cual != 'ring' ){
			$('#grf-ring-' + idx ).hide();
			$('#grf-top-' + idx ).show();
		} else {
			$('#grf-ring-' + idx ).show();
			$('#grf-top-' + idx ).hide(); 
		} 
	}
</script>
<script>
	function export_indicators()
	{
		var url = 'export.php?action=<?php echo $Index->command.'&id_client='.$id_client; ?>';
		this.location = url;
	}
</script>
	<div class="row" id="section-header">
		<div class="col-xs-12">
			<h1 style="text-align: center; border:none;"> Servicios <i class="fa fa-bar-chart-o"> </i> </h1>
		</div>   
	</div> 
	<div class="row"> 
		<div class="col-xs-12 col-md-6">
			<h2> <?php echo $Index->title ?> </h2>
		</div>


		<?php if ($Service->description != '' ){ ?>
		<div class="col-xs-12">
			<p>  <?php echo $Service->description; ?> </p>
		</div>
		<?php } ?>

		<?php if ($Session->is_admin()){ ?>	
			<div class="col-xs-12">
				<form id="frm_serv_filter" method="post" role="form">
					<div class="row">
						<div class="col-xs-12 col-md-4">
							<select id="inp_serv_id_client" name="serv_id_client" class="form-control">
								<?php
								echo $agenda->get_clients_options( $id_client ); 
								?>
							</select>
						</div>
						<div class="col-xs-12 col-md-4">
							<input type="submit" class="btn" value="Filtrar" /> 
						</div>
						<div class="col-xs-12 col-md-4">
							<input type="button" class="btn pull-right" value="Exportar" onclick="export_indicators()" /> 
						</div>
					</div>
				</form>
			</div>
		<?php 
			}
			if ( $Service->has_state )
				$Service->get_state_html(); 
		?> 
	</div>
	<div id='section-content' class='row' style="margin-top: 20px;">
		<?php $Service->get_indicators_html() ?> 
	</div> 
	<div class="row"> &nbsp; </div>
	<div class="row"> 
		<div class="col-xs-12 text-right">
			<span> <?php echo "Última actualización  " . date('Y:m:d H:i:s', $Service->last_timestamp) ?> </span>
		</div>
	 </div>
	<div class="row"> &nbsp; </div> 
	<div class="row"> &nbsp; </div> 
