<?php

if ( !IS_ADMIN ){
	header("Location: indexp.php?command=" . ERR_403 );
	die();
}
	require_once DIRECTORY_CLASS . "class.agenda.php"; 
	$agenda = new Agenda();

?>
<script>
	$(document).ready(function() {
		$.validate({
			form : '#frm_client',
			language : validate_language
		}); 
	});
</script>
	<div class="row" id="section-header">
		<div class="col-xs-12">
			<h1 style="text-align: center; border:none;"> <?php echo $Index->title ?> <i class="fa fa-users"> </i> </h1>
		</div>   
	</div>  
	<div id='section-content' class='row' style="margin-top: 20px;">
		<div id="div_list" class="col-xs-12" style="height: 40%; "> 
			<ul class="nav nav-tabs" role="tablist">
				<li><a href="#">Bancos</a></li>  
			</ul>
			<div class="row" style="overflow-y:auto;">  
				<div class="col-xs-12"> 
					<div class="tbl_srch_frm">
						<div class="row">
							<div class="col-xs-12 text-right"> 
								<button class='btn' onclick='edit_client(0);' > <i class="fa fa-plus"></i> Alta de Cliente </button>
							</div> 
						</div>
					</div>
				</div> 
				<div class='col-xs-12'>
					<div class="tbl_srch_frm">
						<div class='col-xs-8' >
							<input type='text' id='inp_cli_search' name='cli_search' class="form-control" />
						</div>
						<div class='col-xs-4 text-center' >
							<input type='button' id='inp_cli_srch_submit' name='cli_search_submit' value ='Buscar' class="btn" style=""
							onclick="search_client();"/>
						</div>
					</div>
				</div> 
				<div class='col-xs-12'>					
					<table class="table table-striped table-bordered clearfix" id="clients_table"> 
						<thead>
							<tr><td>FIID</td><td>Banco</td><td align="center">Acciones</td></tr>
						</thead>
						<tbody><?php echo $agenda->get_clients_table_edit(); ?></tbody> 
					</table> 
					<input type="hidden" id="inp_id_client_service" value="" />
				</div>
			</div>  
		</div> 
		 
	</div>   
</div>  
</div>  
</div>
	<div class="row"> &nbsp; </div> 
	<div style="display:none;" id="mdl_frm_client" class="modal fade" role="dialog" aria-labelledby="mdl_frm_client" aria-hidden="true">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="clean_form();"> × </button>
				<h4 id="mdl_frm_window_title" class="modal-title">Edición de Cliente</h4>
			</div>
			<form id="frm_client" class="form-horizontal has-validation-callback" role="form" method="post" action="client.php">
				<div class="modal-body "> 
					<fieldset class="col-xs-12"> 
						<div class="row">  
							<div class="col-xs-12">
								<div class="form-group"> 
									<div class="col-xs-12">
										<label class="control-label">Banco</label>
										<input id="inp_client" name="client" class="form-control" value="" required="required" data-validation="required" type="text" /> 
									</div>
								</div>
							</div> 
							<div  class="col-xs-12 col-md-6" > 
								<div class="form-group">
									<div class="col-xs-12">
										<label class="control-label">FIID</label>
										<input id="inp_code" name="code" class="form-control" value="" required="required" data-validation="required" type="text" />
									</div>
								</div>  
							</div>    
						</div>
					</fieldset> 
				</div>
				<div class="modal-footer">
					<input id="inp_id_client" name="id_client" value="0" type="hidden"> 
					<input id="inp_action" name="action" value="client_edition" type="hidden">
					<button type="button" class="btn btn-default" data-dismiss="modal" onclick="cancel_client_edition();">
						<i class="fa fa-times"></i> Cancelar
					</button>
					<button type="submit" class="btn btn-check" onclick="save_client();">
						<i class="fa fa-save"></i> Aceptar
					</button>
				</div>
			</form>
		</div>
	</div> 
</div> 
<div style="display:none;" id="mdl_client_services" class="modal fade" role="dialog" aria-labelledby="mdl_client_services" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div id="div_client_service_form" class="modal-content"> 
		</div>
	</div> 
</div>
