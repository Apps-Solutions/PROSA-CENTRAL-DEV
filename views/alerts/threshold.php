<?php
require DIRECTORY_CLASS . "class.threshold.php";
$threshold = new Threshold();
?>   
<script>
	function showTab( which ){
		$('.tab-content').hide();
		$(which).show();
	}
	
	$(document).ready(function() {
		$.validate({
			form : '#frm_threshold',
			language : validate_language 
		});
		$('#inp_win_start, #inp_win_end, #inp_srch_start, #inp_srch_end').datetimepicker({ pick12HourFormat: false, 
				showToday: true,  
				icons: {
                    time: "fa fa-clock-o",
                    date: "fa fa-calendar",
                    up: "fa fa-arrow-up",
                    down: "fa fa-arrow-down"
                } });
		$('#inp_win_start, #inp_win_end').data("DateTimePicker").setMinDate(new Date());
		
		$("#reset_srch").hide();
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
<div id='section-content' class='row' style="margin-top: 20px;"> 
	<div class="col-xs-12">
		<div class="row">  
			<div class='col-xs-12'>
				<ul class="nav nav-tabs" role="tablist">
					<li <?php echo ( isset($_GET['tab']) ) ? "" : "class='active'" ?>><a href="#cont-threshold" role="tab" data-toggle="tab" onclick="showTab('#cont-threshold');">Umbrales</a></li> 
					<li <?php echo ( isset($_GET['tab']) && $_GET['tab'] == 'mnt' ) ? "class='active'" : "" ?>><a href="#cont-maintainance" role="tab" data-toggle="tab" onclick="showTab('#cont-maintenance');">Mantenimiento</a></li> 
				</ul>  
				<div id="cont-threshold" class='tab-content col-xs-12' style="<?php echo ( isset($_GET['tab']) ) ? "display: none;" : "" ?>">
					<div class="row">
					<form id="frm_threshold" action="threshold.php" method="post">
						
							<table id='tbl_threshold' class="table table-striped table-bordered clearfix" >
								<thead>
									<tr>
										<th style="width: 55%;"> Servicio</th>
										<th style="width: 15%;"> Umbral </th>
										<th style="width: 15%;"> T. PROSA</th>
										<th style="width: 15%;"> T. Cliente</th>
									</tr>
								</thead>
								<tbody>
								<?php echo $threshold->get_list_hmtl(); ?> 
								</tbody>
							</table> 
						<div class="row"> &nbsp; </div> 
						<div class="col-xs-12 text-right">
							<input type="hidden" id="inp_action" name="action" value="threshold_edition" />
							<input type="submit" id="inp_submit" name="submit" value="Guardar" class="btn" style="width: 180px; background: #990d17 !important; color: #EEE;" />
						</div>
					</form>
					</div>    
				</div>
				<div id="cont-maintenance" class='tab-content' style="<?php echo ( isset($_GET['tab']) && $_GET['tab'] == 'mnt') ? "" : "display: none;" ?>">
					<div class="col-xs-12 "> 
						<div class="row">
							<div class="col-xs-12  tbl_srch_frm ">
				
								<div class="row">
									<div class="col-xs-12 text-right"> 
										<button class='btn' onclick='edit_window(0);' > <i class="fa fa-plus"></i> Nueva ventana de mantenimiento </button>
										<button class='btn' style="background: #990d17; color: #FFF;" onclick='click_export();' > <i class="fa fa-cloud-download"></i> Exportar </button>
										<!--
										
										<input id="inp_srch_start" name="srch_start" placeholder="Desde" class="form-control" value="" required="required" data-validation="required" data-date-format="YYYY/MM/DD HH:mm" type="datetime" style="width:15%;float: left;margin-left: 5px;"/>
										
										<input id="inp_srch_end" name="srch_end" placeholder="Hasta" class="form-control" value="" required="required" data-validation="required" data-date-format="YYYY/MM/DD HH:mm" type="datetime" style="width:15%;float: left;margin-left: 5px;"/>
										
										<select id="inp_srch_id_service" name="srch_id_service" class="form-control" required="required" data-validation="required" style="width:30%;float: left;margin-left: 5px;"/>
											<option value="0" selected="selected">Elija un Servicio</option>
											<?php //echo $threshold->get_service_options(); ?>
										</select>
										
										<button class='btn' onclick='srch_service_threshold()' style="float:left; margin-left: 5px;"> Buscar </button>
										<button class='btn' onclick='reset_srch_service_threshold()' id="reset_srch" style="float:left; margin-left: 5px;"> Ver Todos </button>
										-->
									</div> 
								</div>
							</div>
						</div>
						<div class="row">
							<table id='maintenance' class="table table-striped table-bordered " >
								
								<thead>
									<!--
									<tr>
										<th style="width: 55%;"> Servicio	</th>
										<th style="width: 15%;"> Inicio 	</th>
										<th style="width: 15%;"> Final 		</th>
										<th style="width: 10%;"> Status		</th>
										<th style="width: 10%;"> Acciones 	</th>
									</tr>
									-->
								</thead>
								<tbody>
								<?php  echo $threshold->get_list_threshold_html('maintenance'); ?> 
								</tbody>
							</table>
						</div> 
					</div>
				</div>
			</div>
		</div>  
	</div>  
</div>  
</div>  
</div>
	<div class="row"> &nbsp; </div> 
	<div style="display:none;" id="mdl_frm_window" class="modal fade" role="dialog" aria-labelledby="mdl_frm_window" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="clean_form();"> × </button>
				<h4 id="mdl_frm_window_title" class="modal-title">Edición de Ventana de Mantenimiento</h4>
			</div>
			<form id="frm_threshold" class="form-horizontal has-validation-callback" role="form" method="post" action="threshold.php" >
				<div class="modal-body "> 
					<fieldset class="col-xs-12"> 
						<div class="row">  
							<div class="col-xs-12">
								<div class="form-group"> 
									<div class="col-xs-12">
										<label class="control-label">Servicio</label>
										<select id="inp_win_id_service" name="win_id_service" class="form-control" required="required" data-validation="required" />
											<option value="0" selected="selected">Elija una opción</option>
											<?php echo $threshold->get_service_options(); ?>
										</select>
									</div>
								</div>
							</div> 
							<div  class="col-xs-12 col-md-6" > 
								<div class="form-group">
									<div class="col-xs-12">
										<label class="control-label">Inicio</label>
										<input id="inp_win_start" name="win_start" class="form-control" value="" required="required" data-validation="required" data-date-format="YYYY/MM/DD HH:mm" type="datetime" />
									</div>
								</div>  
							</div>  
							<div class="col-xs-12 col-md-6"> 
								<div class="form-group">
									<div class="col-xs-12">
										<label class="control-label">Final</label>
										<input id="inp_win_end" name="win_end" class="form-control" value="" required="required" data-validation="required" data-date-format="YYYY/MM/DD HH:mm" type="datetime" />
									</div>
								</div>
							</div>  
						</div>
					</fieldset> 
				</div>
				<div class="modal-footer">
					<input id="inp_id_window" name="id_window" value="0" type="hidden"> 
					<input id="inp_action" name="action" value="edit_maintenance_window" type="hidden">
					<button type="button" class="btn btn-default" data-dismiss="modal" onclick="cancel_window_edition();">
						<i class="fa fa-times"></i> Cancelar
					</button>
					<button type="button" class="btn btn-check" onclick="save_window();">
						<i class="fa fa-save"></i> Aceptar
					</button>
				</div>
			</form>
		</div>
	</div> 
<div class="row"> &nbsp; </div>
<div class="row"> &nbsp; </div>
