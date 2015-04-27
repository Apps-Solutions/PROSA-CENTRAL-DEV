<?php 
global $Session; 
if ( $this->class = "Client" && $Session->is_admin() ){
?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="clean_form();"> × </button>
	<h4 class="modal-title">Edición de Servicios para <?php echo $this->client ?> </h4>
</div>
<form id="frm_client_services" class="form-horizontal has-validation-callback" role="form" method="post" >
	<div class="modal-body "> 
		
		<h2> <?php echo $this->client ?> </h2>
		
		<div class="col-xs-12"> 
			<div class="col-xs-12 col-md-6">
				<div class="row">
					<div class="col-xs-12">
						<ul class="nav nav-tabs" role="tablist">
							<li><a href="#">Servicios</a></li> 
						</ul> 
						<div class="row" style="height: 280px;">  
							<div class='col-xs-12'>
								<table class="table table-striped table-bordered clearfix" > 
									<?php echo $this->get_client_services_table(); ?>
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
						<div class="row" style="height: 200px;"> 
							<div class='col-xs-12'>
								<div class="tbl_srch_frm">
									<div class='col-xs-8' >
										<input type='text' id='inp_cli_search' name='cli_search' class="form-control" />
									</div>
									<div class='col-xs-4 text-center' >
										<input type='button' id='inp_cli_srch_submit' name='cli_search_submit' value ='Buscar' class="btn" />
									</div>
								</div>
							</div>
							<div class='col-xs-12'>
								<ul class="list-group" > 
									<?php echo $this->get_client_users_list(); ?> 
								</ul> 
							</div>
						 </div>
					</div>
				</div>
				<div class="row"> &nbsp; </div>
			</div> 
		</div> 
	</div>
	<div class="modal-footer">
		<input id="inp_id_client_services" name="id_client" value="" type="hidden"> 
		<input id="inp_action" name="action" value="client_edition" type="hidden">
		<button type="button" class="btn btn-default" data-dismiss="modal" onclick="cancel_client_edition();">
			<i class="fa fa-times"></i> Cancelar
		</button>
		<button type="submit" class="btn btn-check" onclick="save_client();">
			<i class="fa fa-save"></i> Aceptar
		</button>
	</div>
</form>
<?php } ?>