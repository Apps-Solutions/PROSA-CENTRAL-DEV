<?php 


class DataTable{
		
		public $page = 1;
		public $rows;
		public $table_id;
		
		public $sord;
		public $sidx;
		
		public $fidx;
		public $fval;
		
		public $acciones;
		public $idioma = 1;
		public $title;
		
		protected $which;
		protected $query;
		protected $where;
		protected $group;
		protected $sort;
		protected $limit;
		 
		public $total_pages 	= 0;
		public $total_records	= 0;
		
		protected $columns = array();
		protected $template = "";
		protected $template_header = "";
		protected $template_foot = "";
		protected $template_xls = '';
		protected $date_fcn;
		protected $export = FALSE;
		protected $cols = array();
		
		
		//private $id_proyecto;
		protected $showing_template = " Mostrando %s - %s de %s registros. ";
		
		public $error = array();
		
		public function DataTable( $which = '', $table_id = '' ){
			
			if ( $which != ''){
				
				$this->which = $which;
				$this->page = isset($_REQUEST['page']) && is_numeric($_REQUEST['page']) ? $_REQUEST['page'] : 1;
				$this->rows = isset($_REQUEST['rows']) && is_numeric($_REQUEST['rows']) ? $_REQUEST['rows'] : 25;
				$this->sord = isset($_REQUEST['sord']) && $_REQUEST['sord'] != '' ? $_REQUEST['sord'] : "ASC";
				$this->sidx = isset($_REQUEST['sidx']) && $_REQUEST['sidx'] != '' ? $_REQUEST['sidx'] : "id";
				
				if ( $table_id != '')
					$this->table_id = $table_id;
				else 
					$this->table_id = $which ;
				
				$this->set_query(); 
				$this->set_search();
				$this->set_template(); 
				
			} else {
				$this->clean();
				$this->error[] = "Listado inválido.";
			} 
		}
		
		private function set_query() {
			switch ( $this->which ){
				
				case 'lst_threshold':
				        $this->query = "SELECT * FROM " . PFX_MAIN_DB . "maintenance INNER JOIN " . PFX_MAIN_DB . "service ON id_service = ma_se_id_service WHERE ma_status > 0 ";
				        $this->sidx = ( $this->sidx != 'id') ? $this->sidx : 'ma_se_id_service';
				        break;
				case 'lst_alert_history':

						$this->query = 	"select id_alert, al_timestamp, se_service, cl_client, al_text " . //, al_user ".


						"from ". PFX_MAIN_DB ."alert inner join ".PFX_MAIN_DB."service on id_service = al_se_id_service inner join ".PFX_MAIN_DB."client on id_client = al_cl_id_client";
						$this->sidx = ( $this->sidx != 'id') ? $this->sidx : 'cl_client';
						break;
			}
			$this->sort = " ORDER BY " . $this->sidx . " " . $this->sord . " ";
		}
		

		public function set_filter( $col , $val, $signo = '=', $modo = 'AND', $open = '', $close = '' ){
			if ($signo == 'LIKE')

				$this->where .= " " . $modo . " " . $open . " " . ($col) . " " . $signo . " '%" . ($val) . "%' " . $close . " ";
			elseif( in_array($signo, array('>', '>=', '<', '<=') ) === TRUE )
				$this->where .= " " . $modo . " " . $open . " " . ($col) . " " . $signo . "  " . ($val) . "  " . $close . " ";


			else
				$this->where .= " " . $modo . " " . $open . " " . ($col) . " " . $signo . "  '" . ($val) . "'  " . $close . " ";
		}

		public function set_title( $title ){
			$this->title = $title;
		}
		 
		private function set_template() {
			switch ( $this->which ){
				/**
				 *atributo export en las columnas indica que al momento de crear la cabecera de la hoja de calculo, tome solo las columnas que se exportan.
				 *atributo datatype sirve para identificar el tipo de dato, util sobre todo en el manejo de fechas ya que hay que formatearlas
				 **/
				case 'lst_threshold':
					$this->title = " Servicios en Mantenimiento ";
					$this->columns = array(
						array( 'idx' => 'se_service',	'lbl' => 'Servicio', 	'sortable' => TRUE, 	'searchable' => TRUE,	'export' => TRUE,	'datatype' => 'STRING'),
						array( 'idx' => 'ma_start',	'lbl' => 'Inicio',  	'sortable' => FALSE, 	'searchable' => FALSE,	'export' => TRUE,	'datatype' => 'DATETIME'),  
						array( 'idx' => 'ma_end',	'lbl' => 'Final', 	'sortable' => FALSE, 	'searchable' => FALSE,	'export' => TRUE,	'datatype' => 'DATETIME'),
						array( 'idx' => 'ma_status',	'lbl' => 'Status', 	'sortable' => FALSE, 	'searchable' => FALSE,	'export' => TRUE,	'datatype' => 'STRING'),
						array( 'idx' => 'actions',	'lbl' => 'Acciones', 	'sortable' => FALSE, 	'searchable' => FALSE,	'export' => FALSE,	'datatype' => '')				
								); 
					$this->template = DIRECTORY_VIEWS . "/lists/lst.maintainance.php";
					$this->template_xls = DIRECTORY_VIEWS . "/xls/xls.maintainance.php";
					$this->date_fcn = TRUE;
					$this->export = TRUE;
					
				        break;
				
				case 'lst_alert_history':
					$this->title = " Historial de Notificaciones ";//select id_alert, al_timestamp, se_service, cl_client, al_text, al_user
					$this->columns = array(
						array( 'idx' => 'al_timestamp',	'lbl' => 'Fecha', 	'sortable' => FALSE, 	'searchable' => FALSE,	'export' => TRUE,	'datatype' => 'DATETIME'  	),
						array( 'idx' => 'al_timestamp',	'lbl' => 'Hora',  	'sortable' => FALSE, 	'searchable' => FALSE,	'export' => TRUE,	'datatype' => 'DATETIME' 	),  
						array( 'idx' => 'se_servicio',	'lbl' => 'Servicio', 	'sortable' => FALSE, 	'searchable' => FALSE,	'export' => TRUE,	'datatype' => 'STRING'	),
						array( 'idx' => 'cl_client',	'lbl' => 'Cliente', 	'sortable' => TRUE, 	'searchable' => TRUE,	'export' => TRUE,	'datatype' => 'STRING'	),
						array( 'idx' => 'al_text',	'lbl' => 'Notificacion','sortable' => FALSE, 	'searchable' => FALSE,	'export' => TRUE,	'datatype' => 'STRING'	),
						array( 'idx' => 'al_user',	'lbl' => 'Usuario', 	'sortable' => FALSE, 	'searchable' => FALSE,	'export' => TRUE,	'datatype' => 'STRING'	)
								); 
					$this->template = DIRECTORY_VIEWS . "/lists/lst.alert.php";
					$this->template_xls = DIRECTORY_VIEWS . "/xls/xls.alert.php";
					$this->date_fcn = TRUE;
				        $this->export = TRUE;

					
				        break;
						
			}
		}
		
		public function set_search(){ 
			if (isset($_REQUEST['searchField']) && $_REQUEST['searchField'] != '' && isset($_REQUEST['searchString']) && $_REQUEST['searchString'] != '') { 
				$sfield = $_REQUEST['searchField'];
				$sstr 	= $_REQUEST['searchString'];
				$this->where .= " AND $sfield LIKE '%" . ($sstr) . "%' "; 
			}
		}
		
		public function get_list_html( $ajax = FALSE ){
			global $obj_bd;  //Usar para Mysql
			if (count($this->error) == 0 && $this->query != '' && $this->template != ''){ 
				
				//$obj_bd = new oracle_db(); //usar para oracle
				
				$query = $this->query 
							. " " . $this->where 
							. " " . $this->group
							. " " . $this->sort; 

			
				$q_cuantos =  "SELECT count(*) as RecordCount FROM (" . $query . ") as cuenta" ;
			
				$record = $obj_bd->query( $q_cuantos );
				
			
			
				print_r($record);
			
		
				if ( $record === FALSE ){
					$this->set_error( 'Ocurrió un error al contar los registros en la BD. ' , LOG_DB_ERR, 1);
					return FALSE;
				}
				
				$this->total_records = (int)$record[0]["RecordCount"];
				echo $this->total_records;
							
				
				$start = (($this->page - 1) * $this->rows);
				
				if ($this->total_records > 0)
				{
						$this->total_pages = ceil($this->total_records / $this->rows);
				}
				else
				{
						$this->total_pages = 0;
				}  
				//$limit = " LIMIT " . $start . ", " . $this->rows;
				
				$fin = $start + $this->rows;
				/*
				$queryF = 'select * from (select rownum rn, a.* from (' . $query. ' ) a ) WHERE rn BETWEEN '. $start . ' AND ' . $fin;
				 * Línea para oracle ****
				 */
				 $queryF = "select * from (select count(*) as rownum from (SELECT * FROM pra_maintenance INNER JOIN pra_service ON id_service = ma_se_id_service WHERE ma_status > 0 ORDER BY ma_se_id_service ASC ) a ) as test WHERE rownum BETWEEN 0 AND 25";
				

				//$result = $obj_bd->query( $query . $limit );
				$result = $obj_bd->query( $queryF); 
				
				//echo $query . $limit . '<br/>' ;
				//echo $queryF;
				
				if ( $result !== FALSE ){
					if ( !$ajax ) 
						$this->get_header_html();
					if ( count( $result ) > 0 ){
						$resp = "";
						foreach ($result as $k => $record)
						{
							ob_start();
							require $this->template; 
							$resp .= ob_get_clean(); 
						}
					}
					else
					{
						$resp =  "<tr> <td align='center' colspan='" . count($this->columns) . "'> No se encontraron registros. </td> <tr>";
					} 
					if ( $ajax )
					{
						return $resp;//."<tr> <td align='center' colspan='" . count($this->columns) . "'> ".$queryF." </td> <tr>";
					}
					else
					{
						echo $resp;//."<tr> <td align='center' colspan='" . count($this->columns) . "'> ".$queryF." </td> <tr>"; 
						$this->get_foot_functions();
					}
				}
				else {
					$this->set_error( 'Ocurrió un error al obtener los registros de la BD', LOG_DB_ERR, 2);
					return false;
				} 
			}
		}

		public function get_html_search(){
			/*$total_id=array();
	      	foreach ($this->columns as $k => $co){          
	              if ($co['searchable']){
	                  array_push($total_id,$co['lbl']);
	              }			
	          }		
			if(count($total_id)>1){*/
			?>
			<select id="inp_<?php echo $this->table_id ?>_srch_idx">
			<?php 
				foreach ($this->columns as $k => $col) {
					if ($col['searchable']){
						echo "<option value='" . $col['idx'] . "'>" . $col['lbl'] . "</option>";
					}
				}
			?>
			</select>
			<?php /*
          
          }else{

				echo $this->columns[0]['lbl']; 
				// Verificar búsquedas
				?>
			<!--<input type='hidden' id='inp_<?php echo $this->table_id_srch_idx ?>_srch_idx' value='<?php echo $this->columns[0]; ?>'/>-->
				<?php

				echo $this->columns[0]['lbl'];

			}*/
		  ?>
			<input type="text" id="inp_<?php echo $this->table_id ?>_srch_string">
			<button onclick="reload_table('<?php echo $this->table_id ?>')"><i class="fa fa-search"></i></button>
			<?php
		}
		
		protected function get_html_date_search()
		{
				if( $this->date_fcn )
				{
						
						?>
						
						<select id="inp_<?php echo $this->table_id ?>_date_srch_idx">
						<?php 
							foreach ($this->columns as $k => $col)
							{
								if ($col['datatype'] == 'DATETIME')
								{
									echo "<option value='" . $col['idx'] . "'>" . $col['lbl'] . "</option>";
								}
							}
						?>
						</select>
						<input type="text" placeholder="Desde" id="inp_<?php echo $this->table_id ?>_date_srch_start" data-validation="required" data-date-format="YYYY/MM/DD HH:mm" type="datetime">
						<input type="text" placeholder="Hasta" id="inp_<?php echo $this->table_id ?>_date_srch_end" data-validation="required" data-date-format="YYYY/MM/DD HH:mm" type="datetime">
						
						<script>
							$('#inp_<?php echo $this->table_id ?>_date_srch_start, #inp_<?php echo $this->table_id ?>_date_srch_end').datetimepicker({ pick12HourFormat: false, 
										showToday: true,  
										icons: {
								    time: "fa fa-clock-o",
								    date: "fa fa-calendar",
								    up: "fa fa-arrow-up",
								    down: "fa fa-arrow-down"
								} });
								//$('#inp_<?php echo $this->table_id ?>_date_srch_start, #inp_<?php echo $this->table_id ?>_date_srch_end').data("DateTimePicker").setMinDate(new Date());
						</script>
						
						<?php
						
				}
		}

		protected function get_header_html(){ 
			if ( is_array($this->columns) ){  ?>
				<thead>
					<tr >
						<td colspan="<?php echo count($this->columns) ?>">
							<div class="row">
								<div class="col-xs-12 text-center"> <h4 id='lbl_title'><?php echo $this->title ?></h4> </div>
								<div class="col-xs-6">
									Buscar 
									<?php $this->get_html_search(); ?>
								</div>
								
				
								
								<div class="col-xs-6 text-right"> 
									<span id='<?php echo $this->table_id ?>_lbl_foot' >
									<?php  echo $this->get_foot_records_label(); ?>
									</span>
									<select id="inp_<?php echo $this->table_id ?>_rows" name="<?php echo $this->table_id ?>_rows" onchange="reload_table('<?php echo $this->table_id ?>');">
										<option value="25"  <?php $this->rows == 25  ? "selected='selected'" : "" ?>>25</option>
										<option value="50"  <?php $this->rows == 50  ? "selected='selected'" : "" ?>>50</option>
										<option value="100" <?php $this->rows == 100 ? "selected='selected'" : "" ?>>100</option>   
									</select> 
									registros por página. 
									<button onclick="reload_table('<?php echo $this->table_id ?>');"><i class="fa fa-refresh"></i></button>
								</div>
							</div>
							
							<div class="row">
								
								<div class="col-xs-6"> 
									<?php $this->get_html_date_search(); ?>
								</div>
								
								<div class="col-xs-6 text-right">
										<?php
												if($this->export)
												{
										?>
												<button onclick="export_table_xls('<?php echo $this->table_id ?>');" style="visibility: hidden;"><i class="fa fa-cloud-download"></i>Exportar</button>
										<?php
												}
										?>
										<button onclick="clean_table('<?php echo $this->table_id ?>');"><i class="fa fa-list-alt"></i>Ver listado completo</button>
								</div>
							</div>
						</td>
					</tr>
					<tr>
				<?php foreach ($this->columns as $k => $col) {
					$sort_cls = ""; 
					$sort_func = "";
					if ( $col['sortable'] ){
						$sort_cls = "sortable sorting";
						$sort_dir = "ASC";
						if ( $this->sidx == $col['idx'] ){
							$sort_cls .= ( $this->sord == 'DESC') ? "_asc" : "_desc";
							$sort_dir  = ( $this->sord == 'DESC') ? "ASC"  : "DESC";
						}
						$sort_func = "onclick='sort_table(\"" . $this->table_id . "\", \"" . $col['idx'] . "\", \"" . $sort_dir . "\" )'";
					} 
					echo "<th id='" . $this->table_id . "_hd_" . $col['idx'] . "' class='" . $this->table_id . "_head " . $sort_cls . "' " . $sort_func . " > " . $col['lbl'] . "</th>";	
				?>  
				<?php } ?> 
					</tr>
				</thead> 
				<tbody id="<?php echo $this->table_id ?>_tbody" > 
			<?php
			}
		} 
		
		public function get_foot_records_label(){
			$start 	= (($this->page - 1) * $this->rows);
			$stop 	= $start + $this->rows;
			$stop 	= ( $stop <= $this->total_records ) ? $stop : $this->total_records;
			return sprintf( $this->showing_template, $start+1, $stop, $this->total_records );
		}
		
		protected function get_foot_functions(){ ?>
			</tbody>
			<tfoot>
				<tr> 
					<td colspan="<?php echo count($this->columns) ?>">
						<div class="row">
							<div class="col-xs-6" style="margin-top: 10px;" >
								
								<input type="hidden" id="inp_<?php echo $this->table_id ?>_sord" name="<?php echo $this->table_id ?>_sord" value="<?php echo $this->sord ?>" />
								<input type="hidden" id="inp_<?php echo $this->table_id ?>_sidx" name="<?php echo $this->table_id ?>_sidx" value="<?php echo $this->sidx ?>" />
								<input type="hidden" id="inp_<?php echo $this->table_id ?>_fval" name="<?php echo $this->table_id ?>_fval" value="<?php echo $this->fval ?>" />
								<input type="hidden" id="inp_<?php echo $this->table_id ?>_fidx" name="<?php echo $this->table_id ?>_fidx" value="<?php echo $this->fidx ?>" />
								<input type="hidden" id="inp_<?php echo $this->table_id ?>_rows" name="<?php echo $this->table_id ?>_rows" value="<?php echo $this->rows ?>" />
								<input type="hidden" id="inp_<?php echo $this->table_id ?>_list" name="<?php echo $this->table_id ?>_list" value="<?php echo $this->which ?>" />
								<input type="hidden" id="inp_<?php echo $this->table_id ?>_cols" name="<?php echo $this->table_id ?>_cols" value="<?php echo count($this->columns)  ?>" />
								<input type="hidden" id="inp_<?php echo $this->table_id ?>_tpages" name="<?php echo $this->table_id ?>_tpages" value="<?php echo $this->total_pages ?>" /> 
							</div> 
							<div class="col-xs-6 text-right">
								<div class="datatable-paginate">
									<ul class="pagination">
										<li <?php echo ( $this->page >= 1 ) ? "" : "class='disabled'"; ?> >
											<a href="#" onclick="reload_table_page('<?php echo $this->table_id."','1" ?>')"><i class="fa fa-angle-double-left"></i></a>
										</li>
										<li <?php echo ( $this->page >= 1 ) ? "" : "class='disabled'"; ?> >
											<a href="#" onclick="prev_table_page('<?php echo $this->table_id; ?>')" ><i class="fa fa-angle-left"></i></a>
										</li>
										<li>
											<a href="#">
												Página <input id="inp_<?php echo $this->table_id ?>_page" name="page" value="<?php echo $this->page; ?>"  />
												<button style='margin-left: -5px;' onclick="reload_table('<?php echo $this->table_id ?>');"><i class="fa fa-gear"></i></button> de 
												<span id="<?php echo $this->table_id ?>_lbl_tpages"><?php echo $this->total_pages; ?></span>  
											</a>
										</li> 
										<li <?php echo ( $this->page < $this->total_pages ) ? "" : "class='disabled'"; ?> >
											<a href="#" onclick="next_table_page('<?php echo $this->table_id; ?>');"><i class="fa fa-angle-right"></i></a>
										</li>
										<li <?php echo ( $this->page < $this->total_pages ) ? "" : "class='disabled'"; ?> >
											<a href="#" onclick="last_table_page('<?php echo $this->table_id ?>')" ><i class="fa fa-angle-double-right"></i></a>
										</li> 
									</ul>
								</div> 
							</div>
						</div>
					</td>
				</tr>
			</tfoot>
			<?php
		}
		
		public function get_list_xml(){
			if (count($this->error) == 0 && $this->query != '' && $this->template != ''){
				global $obj_bd;
				$consulta = $this->query 
							. " " . $this->where 
							. " " . $this->group
							. " " . $this->sort; 
				//echo $consulta;
				$cuantos = $obj_bd -> consulta_bd("SELECT count(*) as RecordCount FROM (" . $this->query. " " . $this->where  . ") as cuenta");
				$many = $cuantos[0];
				$total = (int)$many["RecordCount"];
				
				$start = (($this->page - 1) * $this->rows);
				if ($total > 0) { $total_pages = ceil($total / $this->rows);
				} else { $total_pages = 0; } 
				$limit = " LIMIT " . $start . ", " . $this->rows; 
				 
				//echo $consulta;
				$result = $obj_bd->consulta_bd( $consulta . $limit );
				if ( $result !== FALSE ){
					$this->set_template( true );
					$this->set_xml_header();  
					echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
					echo "<rows>\n";
					echo "<page>" . $this->page . "</page>\n";
					echo "<total>" . $total_pages . "</total>\n";
					echo "<records>" . $total . "</records>\n";
					foreach ($result as $k => $record) { 
						require $this->template; 
					}
					echo "</rows>";
				} 
			}
		}

		private function set_header_xml(){
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
			header("Cache-Control: no-cache, must-revalidate");
			header("Pragma: no-cache");
			header("Content-type: text/xml");
		}

		public function get_array(){
			if (count($this->error) == 0 && $this->query != ''){
				$this->bd = new IBD;
				$query = $this->query 
							. " " . $this->where 
							. " " . $this->group ;
				global $obj_bd;
				$result = $obj_bd->query( $query ); 
				if ( $result !== FALSE ){ 
					return $result;   
				}
				else {
					$this->set_error( 'Ocurrió un error al obtener los registros de la BD', LOG_DB_ERR, 2);
					return FALSE;
				}  
			}
		}
			
		public function clean(){ 
			$this->where = "";
			$this->columns = array();
			$this->error 	= array();
		}  
		
		public function get_errors( $break = "<br/>" ){
			$resp = "";
			if ( count ($this->error) > 0 ){
				foreach ( $this->error as $k => $err)
					$resp .= " ERROR @ Class DataTable: " . $err . $break;
			}
			return $resp;
		}
		
		protected function set_error( $err , $type, $lvl = 1 ){
			global $Log;
			$this->error[] = $err;
			$Log->write_log( " ERROR @ Class Listado : " . $err, $type, $lvl );
		}
		
		public function get_list_xls( )
		{
				if (count($this->error) == 0 && $this->query != '' && $this->template != '')
				{
						//global $obj_bd;
						//$obj_bd = new oracle_db();
						$obj_bd = new PDOMySQL();
						$query = $this->query 
									. " " . $this->where 
									. " " . $this->group
									. " " . $this->sort;
									
						$result = $obj_bd->query( $query ); 
						
						require DIRECTORY_CLASS . 'class.xlsmngr.php';
						$xls = new XlsMngr();
						$head_xls = array();
						foreach($this->columns as $k => $cols)
						{
								if( $cols['export'] === TRUE )
										$head_xls[] = $cols['lbl']; 
						}
						
						if ( $result !== FALSE )
						{
								
								
								$xls->set_header( $head_xls );
								
								if ( count( $result ) > 0 )
								{
										$resp = "";
										foreach ($result as $k => $record)
										{
												/*
												foreach( $this->format_xls as $k => $fto)
												{
													if( $fto['datatype'] == 'DATETIME' )
													{
														$row[$k] = date( $fto['format'], $record[ $fto['qry_col'] ] );
													}
													else
													{
														$row[$k] =  $record[ $fto['qry_col'] ];
													}
												}
												
												
												$xls->insert_row( $row );
												*/
												$resp = "";
												ob_start();
												require $this->template_xls; 
												$resp = ob_get_clean();
												$row = array();
												$row = explode('|', $resp);
												
												$xls->insert_row( $row );
												
										}
								}
						}
						
						$exp = $xls->finish_xls();
						
						return $exp;
				}
		}
	
}

?>
