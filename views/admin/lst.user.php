<?php

global $Session; 
if ( !$Session->is_admin()){
	require_once DIRECTORY_VIEWS. 'base/403.php';
	die();
} 

require_once DIRECTORY_CLASS . 'class.agenda.php';
$agenda = new Agenda();

?>   
	<div class="row" id="section-header">
		<div class="col-xs-12">
			<h1 style="text-align: center; border:none;"> <?php echo $Index->title ?> <i class="fa fa-users"> </i> </h1>
		</div>   
	</div>  
	<div id='section-content' class='row' style="margin-top: 20px;">
		<div id="div_list" class="col-xs-12" style="height: 40%; "> 
			<ul class="nav nav-tabs" role="tablist">
				<li><a href="#">Usuarios</a></li> 
			</ul> 
			<div class="row" style="height:auto; overflow-y:auto;"> 
				
				<div class="col-xs-12 col-md-6">
					<div class="row">
						<div class="col-xs-12"> 
							<div class="row" style="height: 200px;">  
								<div class='col-xs-12'>
									<table id="users_table" class="table table-striped table-bordered clearfix" >
										<?php //echo $agenda->get_prosa_users_list(); 
												//Activar en 
										?>
										<!-- Remover rn central---> 	
											<tr> 
												<td>
												  <li class="list-group-item">
													  <div class="row" id="div_row_user_prosa_0">
														  <div class="col-xs-12" >
															  <div class="row" style="cursor:pointer;"  onclick='show_user_services( 0, "aontiver");'>
																  <span class="col-xs-12">Alvaro Ontiveros H.  7725 (alvaro.ontiveros@prosa.com.mx)</span>
															  </div>
															  <div id='div_user_services_0' class="row div_user_service" style="display:none;" >
																  <table class="table table-striped table-bordered clearfix" >
																	  <tr> <td> Cargando... </td> </tr>
																  </table> 
															  </div> 
														  </div>
													  </div>
												  </li>
												</td>
											</tr>
											
											<tr> 
												<td>
												  <li class="list-group-item">
													  <div class="row" id="div_row_user_prosa_1">
														  <div class="col-xs-12" >
															  <div class="row" style="cursor:pointer;"  onclick='show_user_services( 1, "bhguevar");'>
																  <span class="col-xs-12">Beatriz Elena Huesca Guevara (beatriz.huesca@prosa.com.mx)</span>
															  </div>
															  <div id='div_user_services_1' class="row div_user_service" style="display:none;" >
																  <table class="table table-striped table-bordered clearfix" >
																	  <tr> <td> Cargando... </td> </tr>
																  </table> 
															  </div> 
														  </div>
													  </div>
												  </li>
												</td>
											</tr>
											
											<tr> 
												<td>
												  <li class="list-group-item">
													  <div class="row" id="div_row_user_prosa_2">
														  <div class="col-xs-12" >
															  <div class="row" style="cursor:pointer;"  onclick='show_user_services( 2, "cavila");'>
																  <span class="col-xs-12">Claudio Copernico Avila Luna  7774 (claudio.avila@prosa.com.mx)</span>
															  </div>
															  <div id='div_user_services_2' class="row div_user_service" style="display:none;" >
																  <table class="table table-striped table-bordered clearfix" >
																	  <tr> <td> Cargando... </td> </tr>
																  </table> 
															  </div> 
														  </div>
													  </div>
												  </li>
												</td>
											</tr>
											
											<tr> 
												<td>
												  <li class="list-group-item">
													  <div class="row" id="div_row_user_prosa_3">
														  <div class="col-xs-12" >
															  <div class="row" style="cursor:pointer;"  onclick='show_user_services( 3, "hcgariba");'>
																  <span class="col-xs-12">Hector Daniel Cedillo Garibay (hector.cedillo@prosa.com.mx)</span>
															  </div>
															  <div id='div_user_services_3' class="row div_user_service" style="display:none;" >
																  <table class="table table-striped table-bordered clearfix" >
																	  <tr> <td> Cargando... </td> </tr>
																  </table> 
															  </div> 
														  </div>
													  </div>
												  </li>
												</td>
											</tr>
											
											<tr> 
												<td>
												  <li class="list-group-item">
													  <div class="row" id="div_row_user_prosa_4">
														  <div class="col-xs-12" >
															  <div class="row" style="cursor:pointer;"  onclick='show_user_services( 4, "jualcant");'>
																  <span class="col-xs-12">Jesus Urbina Alcantara (jesus.urbina@prosa.com.mx)</span>
															  </div>
															  <div id='div_user_services_4' class="row div_user_service" style="display:none;" >
																  <table class="table table-striped table-bordered clearfix" >
																	  <tr> <td> Cargando... </td> </tr>
																  </table> 
															  </div> 
														  </div>
													  </div>
												  </li>
												</td>
											</tr>
											
											<tr> 
												<td>
												  <li class="list-group-item">
													  <div class="row" id="div_row_user_prosa_5">
														  <div class="col-xs-12" >
															  <div class="row" style="cursor:pointer;"  onclick='show_user_services( 5, "aaplasen");'>
																  <span class="col-xs-12">Alfonso Altamirano Plasencia (alfonso.altamirano@prosa.com.mx)</span>
															  </div>
															  <div id='div_user_services_5' class="row div_user_service" style="display:none;" >
																  <table class="table table-striped table-bordered clearfix" >
																	  <tr> <td> Cargando... </td> </tr>
																  </table> 
															  </div> 
														  </div>
													  </div>
												  </li>
												</td>
											</tr>	
											<!-- Remover rn central--->							
									</table> 
								</div>
							 </div>
						</div>
					</div>
					<div class="row"> &nbsp; </div>
				</div> 
			</div>  
		</div> 
	</div>
