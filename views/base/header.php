<?php

?>
<header class="navbar">
	<div class="container-fluid expanded-panel">
		<div class="row"> 
			<div id="logo" class="col-xs-6 ">
				<a href="#" class="show-sidebar">
					<div class="row">
						<div class="col-xs-1 hidden-sm hidden-md hidden-lg" style="color:#AAA;">
							<i class="fa fa-bars "> </i>
						</div>
						<div class="col-xs-10">
							<img src='img/logo.png' height='40' />
						</div>
					</div> 
				</a>
			</div>
			<div id="top-panel" class="col-xs-6 " style="padding-right: 10px;">
				<ul class="nav navbar-nav pull-right panel-menu">
					<li style="padding-right: 10px;" class="pull-right">
						<a href="logout.php" class="account">
							<div class="avatar">
								<img src="img/logout.png" class="img-rounded" alt="Salir">
							</div>
							<div class="user-mini pull-right">
								<h4 > Salir </h4>
							</div>
						</a>
					</li>
					<li style="padding-right: 10px;" class=" pull-right" >
						<a href="#" class="account" >
							<div class="avatar">
								<img src="img/user.png" class="img-rounded" alt="avatar">
							</div>
							<div class="user-mini pull-right">
								<span><?php echo $Session->get_name(); ?></span> 
								<span><?php echo $Session->get_email(); ?></span>
							</div> 
						</a> 
					</li>
				</ul> 
			</div>
		</div>
	</div>
</header>