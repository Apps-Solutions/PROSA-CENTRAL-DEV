<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
		<meta content="width=device-width, initial-scale=1" name="viewport"/>
	    <title> ..:: <?php echo SYS_TITLE ?> ::..</title>
		<link rel="shortcut icon" href="img/favicon.png" />
	<!-- CSS -->
	    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" 	/> 
		<link rel="stylesheet" type="text/css" href="css/font-awesome.css" 	/>
		<link rel="stylesheet" type="text/css" href="css/estilo.css" 		/>
	<!-- /CSS -->
	<!-- JS -->
		<script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
		<script type="text/javascript" src="js/bootstrap.min.js"></script> 
	<!-- /JS -->
		<style>
			body {
				background: url('img/background.png') repeat;
			}
			h3 {
				border: none;
			}
			#page-login .box {
			    margin-top: 20%;
			} 
			.box {
			    background: none repeat scroll 0 0 rgba(0, 0, 0, 0);
			    border: 1px solid #F8F8F8;
			    border-radius: 3px; 
			    display: block;
			    margin-bottom: 60px;
			    position: relative;
			    z-index: 1999; 
			}
			.box-content {
			    background: url('img/bg_login.png') no-repeat top right #FCFCFC;
			    border-radius: 0 0 3px 3px;
			    padding: 10px;
			    position: relative;
			    height: 320px;
			    box-shadow: 7px 7px 9px #333;
			}
		</style>
	</head> 
	<body> 
		<div class="container-fluid">
			<div id="page-login" class="row">
				<div class="col-xs-12 col-md-6 col-md-offset-3">
					<form action="login.php" method="post" > 
						<div class="box ">
							<div class="box-content row">
								<div class="col-xs-6" style="margin-top: 80px;">
									<div class="text-center">
										<h3 ><img src='img/logo.png' alt="<?php echo SYS_TITLE ?>"  class="img-responsive"/></h3> 
									</div>
								</div> 
								<div class="col-xs-6" style="margin-top: 80px;">
									<div class="text-center">
										<span class='error'> <?php echo $error ?> </span>
									</div>
									<div class="form-group"> 
										<input type="text" class="form-control" name="user" placeholder="Usuario" />
									</div>
									<div class="form-group"> 
										<input type="password" class="form-control" name="password" placeholder="Contraseña"/>
									</div>
									<div class="text-center">
										<input type='submit' class="btn btn-success" style="width: 100%; background: #444; color: #CCC; border:none;" value='Entrar'> 
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>