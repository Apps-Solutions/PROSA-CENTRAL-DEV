<?php
global $command;
?>	
	<div class="col-xs-2 col-sm-2" id="sidebar-left">
		<ul class="nav main-menu">
			<li>
				<a <?php echo ($command == HOME || $command == '') ? "class='active'" : ""; ?>
					href="index.php?command=<?php echo HOME ?>"> 
					<i class="fa fa-dashboard"></i> <span class="hidden-xs">Dashboard</span> 
				</a>
			</li>
			<li class="dropdown">
				<a href="#" class="dropdown-toggle 
						<?php echo ( 
								$command == LST_PROFILE || 
								$command == LST_USER
							) ? " active-parent active" : ""; 
						?>">  
					<i class="fa fa-bar-chart-o"></i> <span class="hidden-xs">Administración</span> 
				</a>
				<ul class="dropdown-menu"<?php echo ( 
								$command == LST_PROFILE || 
								$command == LST_USER
							) ? " style='display:block;' " : ""; 
						?>>
					<li>
						<a <?php echo ( $command == LST_PROFILE ) ? "class='active'" : ""; ?>
							href="index.php?command=<?php echo LST_PROFILE ?>"  ><i class="fa fa-group"></i> &nbsp; &nbsp;Perfiles</a>
					</li>
					<li>
						<a <?php echo ( $command == LST_USER ) ? "class='active'" : ""; ?>
							href="index.php?command=<?php echo LST_USER ?>"  ><i class="fa fa-male"></i> &nbsp; &nbsp;Usuarios</a>
					</li> 
					<li>
						<a <?php echo ( $command == FRM_CONTACT_META ) ? "class='active'" : ""; ?>
							href="index.php?command=<?php echo FRM_CONTACT_META ?>"  ><i class="fa fa-male"></i> &nbsp; &nbsp;Información de Contacto</a>
					</li> 
				</ul>
			</li>
			<li>
				<a <?php echo ( $command == LST_CONTACTS ) ? "class='active'" : ""; ?>
					href="index.php?command=<?php echo LST_CONTACTS ?>" > 
					<i class="fa fa-book"></i> <span class="hidden-xs">Agenda</span> 
				</a>
			</li>
			<li class="dropdown">
				<a href="#" class="dropdown-toggle 
						<?php echo ( 
								$command == LST_CLIENT || 
								$command == LST_INSTANCE || 
								$command == REP_CLIENT  ) ? " active-parent active " : ""; 
						?>"> 
					<i class="fa fa-table"></i> 
					<span class="hidden-xs">Clientes</span> 
				</a>
				<ul class="dropdown-menu"
						<?php echo ( 
								$command == LST_CLIENT || 
								$command == LST_INSTANCE || 
								$command == REP_CLIENT  ) ? " style='display:block;' " : ""; 
						?>">  
					>
					<li>
						<a <?php echo ( $command == LST_CLIENT ) ? "class='active'" : ""; ?>
							href="index.php?command=<?php echo LST_CLIENT ?>" >Clientes</a>
					</li>
					<li>
						<a <?php echo ( $command == LST_INSTANCE ) ? "class='active'" : ""; ?>
							href="index.php?command=<?php echo LST_INSTANCE ?>" >Instancias</a>
					</li>
					<li>
						<a <?php echo ( $command == REP_CLIENT ) ? "class='active'" : ""; ?>
							href="index.php?command=<?php echo REP_CLIENT ?>" >Reportes</a>
					</li>
				</ul>
			</li> 
			<li class="dropdown">
				<a href="#" class="dropdown-toggle 
						<?php echo ( 
								$command == FRM_APPEARANCE || 
								$command == FRM_VERSION_APP ||
								$command == FRM_VERSION_CTRL || 
								$command == FRM_MESSAGING  
							) ? " active-parent active " : ""; 
						?>"> 
					<i class="fa fa-gear"></i> <span class="hidden-xs">Configuración</span> 
				</a>
				<ul class="dropdown-menu" <?php echo ( 
								$command == FRM_APPEARANCE || 
								$command == FRM_VERSION_APP ||
								$command == FRM_VERSION_CTRL || 
								$command == FRM_MESSAGING  
							) ? " style='display:block;' " : ""; 
						?>>
					<li>
						<a <?php echo ( $command == FRM_APPEARANCE ) ? "class='active'" : ""; ?>
							href="index.php?command=<?php echo FRM_APPEARANCE ?>" ><i class="fa fa-tint"></i> &nbsp; &nbsp; Apariencia</a>
					</li>
					<li>
						<a <?php echo ( $command == FRM_VERSION_APP ) ? "class='active'" : ""; ?>
							href="index.php?command=<?php echo FRM_VERSION_APP ?>" ><i class="fa fa-android"></i> &nbsp; &nbsp;Aplicación </a>
					</li>
					<li>
						<a <?php echo ( $command == FRM_VERSION_CTRL ) ? "class='active'" : ""; ?>
							href="index.php?command=<?php echo FRM_VERSION_CTRL ?>" ><i class="fa fa-cloud"></i> &nbsp; Backend</a>
					</li>
					<li>
						<a <?php echo ( $command == FRM_MESSAGING ) ? "class='active'" : ""; ?>
							href="index.php?command=<?php echo FRM_MESSAGING ?>" ><i class="fa fa-envelope-o"></i> &nbsp; Mensajería</a>
					</li> 
				</ul>
			</li>
		</ul>
	</div> 