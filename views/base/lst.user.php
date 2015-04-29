<?php

global $Session; 
//$id_client = $Session->get_user_client();
$id_client = 5;
if ( !$id_client ){ 
	require_once DIRECTORY_VIEWS. 'base/403.php';
	die();
} 

require_once DIRECTORY_CLASS . 'class.client.php';
$client = new Client( $id_client, TRUE, TRUE );
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
			<div class="row" style="height: 300px; overflow-y:auto;"> 
				
				<div class="col-xs-12 col-md-6">
					<div class="row">
						<div class="col-xs-12"> 
							<div class="row" style="height: 200px;">  
								<div class='col-xs-12'>
									<ul class="list-group" > 
										<?php echo $client->get_client_users_list(); ?> 
									</ul> 
								</div>
							 </div>
						</div>
					</div>
					<div class="row"> &nbsp; </div>
				</div> 
			</div>  
		</div> 
	</div>   
	