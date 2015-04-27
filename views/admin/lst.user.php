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
										<?php echo $agenda->get_prosa_users_list(); ?>										
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
