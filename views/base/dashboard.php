<?php

?> 
	<div class="row" id="dashboard-header"> 
		<h3 class="col-sm-12"> Bienvenido <?php //echo $Session->get_name(); ?> </h3>   
	</div> 
	<div id='dashboard-content'>
		<div class="row"> &nbsp; </div> 
		<div class="row"> 
			<div class="col-xs-12 text-center">
				 <img class="img-responsive dashboard-logo" src="img/logo-lg.png" />
			</div>
		</div>
		<div class="row">
			<div class="col-xs-1"> &nbsp; </div>
			<div class="col-xs-10 text-center">
				<div class="row">
					<?php 
					$services = $Session->get_services(); 
					foreach ($services as $k => $service) {
					?>
						<div class="col-xs-1"> &nbsp; </div> 
						<a href="index.php?command=<?php echo $service['SE_COMMAND'] ?>">
							<div class="col-xs-11 col-sm-5 col-md-3 col-lg-2 btn-service">
								<div class="row ">
									<div class="col-xs-12  ">
										<img src='img/ico_service.png' />
									</div>
									<span><?php echo utf8_decode($service['SE_SERVICE'])?></span>
								</div> 
							</div>
						</a>
					<?php
					}
					?> 
				</div>
			</div>
			<div class="col-xs-1"> &nbsp; </div>
		</div>
	</div>  