/**
 * Funciones para Listados
 *   
 * */


/**
 * reload_table
 * Sets column and order values and calls reload_table()
 * @param String table The table ID
 */
function reload_table( table ){
	
	if (table != ''){
		
		show_loader( table );
		
		var sord = $('#inp_' + table + '_sord').val();
		var sidx = $('#inp_' + table + '_sidx').val();
		var page = $('#inp_' + table + '_page').val();
		var rows = $('#inp_' + table + '_rows').val();
		var list = $('#inp_' + table + '_list').val();
		var fidx = $('#inp_' + table + '_fidx').val();
		var fval = $('#inp_' + table + '_fval').val();
		var srch_string = $('#inp_' + table + '_srch_string').val();
		var srch_idx 	= $('#inp_' + table + '_srch_idx').val(); 
		var tpages = $('#inp_' + table + '_tpages').val();

		var date_start = $('#inp_' + table + '_date_srch_start').val();
		var date_end = $('#inp_' + table + '_date_srch_end').val();
		var date_srch = $('#inp_' + table + '_date_srch_idx').val();
		
		if ( page < 1 ){
			page = 1; 
			$('#inp_' + table + '_page').val(1);
		} else if ( page > tpages ) {
			page = tpages; 
			$('#inp_' + table + '_page').val(tpages);
		}
		
		page = ( srch_string != '' || date_start != '' || date_end != '' ? 1 : page );
		
		$.ajax({
			url: "ajax.php",
			type: "POST",
			async: false,
			data: {
		  		resource: 		'lists',
		  		action:	 		list,
		  		table_id:		table,
				page:			page,
				rows:			rows,
				sord:	 		sord,
				sidx:			sidx,
				searchField: 	srch_idx,
				searchString: 	srch_string,
				filterIdx:	 	fidx,
				filterVal: 		fval,
				date_srch:		date_srch,
				date_start:		date_start,
				date_end:		date_end
			},
		  	dataType: "json",
		 	success: function(data) {
				if (data.success == true )  {
					
					$('#' + table +'_tbody').html( data.html );
					$('#' + table +'_lbl_foot').html( data.lbl_foot );
					$('#' + table +'_lbl_tpages').html( data.tpages );
					
					$('#inp_' + table +'_rows').val( data.rows );
					$('#inp_' + table +'_page').val( data.page );
					
					$('#inp_' + table +'_tpages').val( data.tpages );
					
					set_header( table );
					
					
				}
				else {  
					show_error( data.error );
					return false;
				}
			}
		}); 
		
		
	} else {
		show_error('DataTableLib (js): Invalid table to reload.');
	}
	
}

function set_header( table ){
	
	var sidx = $('#inp_' + table + '_sidx').val();
	var str_sidx = table + '_hd_' + sidx;
	$('.' + table + '_head.sortable ' ).each( function( index ){ 
		var col  = this.id; 
		if ( col == str_sidx ){
			var sord =  $('#inp_' + table + '_sord').val() ;
			$('#' + this.id ).attr( 'class',  table + '_head sortable sorting_' + ( ( sord == 'DESC') ? 'asc' : 'desc'));
		} else
			$('#' + this.id ).attr( 'class',  table + '_head sortable sorting' );
			
	} );
	
}

function show_loader( table ){
	var test = $('#' + table +' thead tr');
	 
	var html = "<tr> <td colspan = " + $('#inp_' + table +'_cols').val() + "' class='text-center' />";
		html += "<img src='img/loader.gif' /> </td></tr>";
	var loader = "";
	$('#' + table +' tbody').html( html );
}

/**
 * sort_table
 * Sets column and order values and calls reload_table()
 * @param String table
 * @param String col
 * @param String ord 'ASC' or 'DESC'
 */
function sort_table( table, col, ord ){ 
	if ( table != '' && col != '' ){  
		var sord = 'ASC';
		if ( $('#inp_' + table + '_sidx').val() == col ){
			if ($('#inp_' + table + '_sord').val() == 'ASC' ){
				sord = 'DESC';
			} 
		} else {
			$('#inp_' + table + '_sidx').val( col ); 
		}  
		$('#inp_' + table + '_sord').val( sord ); 
		reload_table( table );
		
	} else {
		show_error('DataTableLib (js): Invalid table to sort');
	}
}

function export_table_xls(table)
{
	if (table != '')
	{
		
		//show_loader( table );
		
		
		var sord = $('#inp_' + table + '_sord').val();
		var sidx = $('#inp_' + table + '_sidx').val();
		var page = $('#inp_' + table + '_page').val();
		var rows = $('#inp_' + table + '_rows').val();
		var list = $('#inp_' + table + '_list').val();
		var fidx = $('#inp_' + table + '_fidx').val();
		var fval = $('#inp_' + table + '_fval').val();
		var srch_string = $('#inp_' + table + '_srch_string').val();
		var srch_idx 	= $('#inp_' + table + '_srch_idx').val(); 
		var tpages = $('#inp_' + table + '_tpages').val();
		
		var date_start = $('#inp_' + table + '_date_srch_start').val();
		var date_end = $('#inp_' + table + '_date_srch_end').val();
		var date_srch = $('#inp_' + table + '_date_srch_idx').val();
		
		var url = 'export.php?action='+list+"&table_id="+table+"&sord="+sord+"&sidx="+sidx+"&searchField="+srch_idx;
		url += "&searchString="+srch_string+"&filterIdx="+fidx+"&filterVal="+fval+"&date_srch="+date_srch+"&date_start="+date_start+"&date_end="+date_end;
		
		this.location = url;
	}
	else
	{
		show_error('DataTableLib (js): Invalid table to reload.');
	}
}

function reload_table_page(table, page)
{
	show_loader( table );
		
	var sord = $('#inp_' + table + '_sord').val();
	var sidx = $('#inp_' + table + '_sidx').val();
	var rows = $('#inp_' + table + '_rows').val();
	var list = $('#inp_' + table + '_list').val();
	var fidx = $('#inp_' + table + '_fidx').val();
	var fval = $('#inp_' + table + '_fval').val();
	var srch_string = $('#inp_' + table + '_srch_string').val();
	var srch_idx 	= $('#inp_' + table + '_srch_idx').val(); 
	
	var date_start = $('#inp_' + table + '_date_srch_start').val();
	var date_end = $('#inp_' + table + '_date_srch_end').val();
	var date_srch = $('#inp_' + table + '_date_srch_idx').val();
	
	
	$.ajax({
		url: "ajax.php",
		type: "POST",
		async: false,
		data: {
			resource: 		'lists',
			action:	 		list,
			table_id:		table,
			page:			page,
			rows:			rows,
			sord:	 		sord,
			sidx:			sidx,
			searchField: 		srch_idx,
			searchString: 		srch_string,
			filterIdx:	 	fidx,
			filterVal: 		fval,
			date_srch:		date_srch,
			date_start:		date_start,
			date_end:		date_end
		},
		dataType: "json",
		success: function(data) {
			if (data.success == true )  {
				$('#' + table +'_tbody').html( data.html );
				$('#' + table +'_lbl_foot').html( data.lbl_foot );
				$('#' + table +'_lbl_tpages').html( data.tpages );
				
				$('#inp_' + table +'_rows').val( data.rows );
				$('#inp_' + table +'_page').val( data.page );
				$('#inp_' + table +'_tpages').val( data.tpages );

				
				set_header( table );
			}
			else {  
				show_error( data.error );
				return false;
			}
		}
	}); 
}

function next_table_page(table)
{
	var tpages = $('#inp_' + table + '_tpages').val();
	var page = parseInt( $('#inp_' + table + '_page').val() ) + 1;
	
	if ( page > 1 && page <= tpages) 
	{
		reload_table_page(table, page);
	}
}

function prev_table_page(table)
{
	var tpages = $('#inp_' + table + '_tpages').val();
	var page = parseInt( $('#inp_' + table + '_page').val() ) - 1;
	
	if ( page > 0 && page <= tpages) 
	{
		reload_table_page(table, page);
	}
}


function last_table_page(table)
{
	var page = $('#inp_' + table + '_tpages').val();
	reload_table_page(table, page);
}

function clean_table( table )
{
	if (table != ''){
		
		show_loader( table );
		
		var sord = $('#inp_' + table + '_sord').val();
		var sidx = $('#inp_' + table + '_sidx').val();
		$('#inp_' + table + '_page').val(1);
		var page = $('#inp_' + table + '_page').val();
		var rows = $('#inp_' + table + '_rows').val();
		var list = $('#inp_' + table + '_list').val();
		var fidx = $('#inp_' + table + '_fidx').val();
		var fval = $('#inp_' + table + '_fval').val();
		
		$('#inp_' + table + '_srch_string').val('');
		//$('#inp_' + table + '_srch_idx').val(1); 

		$('#inp_' + table + '_date_srch_start').val('');
		$('#inp_' + table + '_date_srch_end').val('');
		//$('#inp_' + table + '_date_srch_idx').val(1);
		
		$.ajax({
			url: "ajax.php",
			type: "POST",
			async: false,
			data: {
		  		resource: 		'lists',
		  		action:	 		list,
		  		table_id:		table,
				page:			page,
				rows:			rows,
				sord:	 		sord,
				sidx:			sidx
			},
		  	dataType: "json",
		 	success: function(data) {
				if (data.success == true )  {
					$('#' + table +'_tbody').html( data.html );
					$('#' + table +'_lbl_foot').html( data.lbl_foot );
					$('#' + table +'_lbl_tpages').html( data.tpages );
					
					$('#inp_' + table +'_rows').val( data.rows );
					$('#inp_' + table +'_page').val( data.page );
					
					set_header( table );
				}
				else {  
					show_error( data.error );
					return false;
				}
			}
		}); 
		
		
	} else {
		show_error('DataTableLib (js): Invalid table to clean.');
	}
}