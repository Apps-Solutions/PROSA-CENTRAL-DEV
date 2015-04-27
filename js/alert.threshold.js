
function edit_window( id ){
	if (id > 0){
		$.ajax({
			url: "ajax.php", type: "POST", async: false,
			data: {
		  		resource: 'threshold', 
		  		action: 'get_maintenance_window', 
		  		id_main: id
			},
		  	dataType: "json",
		 	success: function(data) {
				if (data.success == true )
				{
					var win = data.info;
					$('#inp_id_window').val( win.id_window ); 
					$('#inp_win_id_service').val( win.id_service );  
					$('#inp_win_start' 	).val( win.start_str );  
					$('#inp_win_end' 	).val( win.end_str );
					  
					$('#mdl_frm_window').modal('show');
				}
				else {  
					show_error( data.error );
					return false;
				}
			}
		}); 
	} else {
		clean_form();
		$('#mdl_frm_window').modal('show'); 
	} 
}

function clean_form(){
	$('#inp_id_window' 	).val( 0 );
	$('#inp_id_service' ).val( 0 ); 
	$('#inp_cat_value' 	).val( "" );  
	$('#inp_cat_parent'	).val( 0 );   
}

function save_window()
{
	var fechaI = new Date( $("#inp_win_start").val() );
	var fechaF = new Date( $("#inp_win_end").val() );
	var now = new Date();
	var hoy = new Date( now.getFullYear(), now.getMonth(), now.getDate(), now.getHours(), now.getMinutes(), 0, 0 );
	
	if (fechaI >= hoy && fechaF >= hoy)
	{
		if (fechaI >= fechaF)
		{
			show_error('La Fecha Final debe ser posterior a la Fecha de Inicio.');
			return false;
		}
		else
		{
			$("form#frm_threshold").submit();
		}
	}
	else
	{
		show_error('Ambas Fechas deben ser programadas a partir del Dia y Hora actuales.');
		return false;
	}
	
	return false;
}

function srch_service_threshold()
{
	var id_service = $("#inp_srch_id_service").val();
	var f_str = $("#inp_srch_start").val();
	var f_end = $("#inp_srch_end").val();
	
	var params = '';
	var values = '';
	var oprdrs = '';
	var format = '';
	
	if (id_service > 0 || f_str != '' || f_end != '')
	{
		if (id_service > 0)
		{
			params = 'MA_SE_ID_SERVICE|';
			values = id_service + '|';
			oprdrs = '=|';
			format = 'NUM|';
		}
		
		if (f_str != '')
		{
			params += 'MA_START|';
			values += f_str + '|';
			oprdrs += '>=|';
			format += 'DATE|';
		}
		
		if (f_end != '')
		{
			params += 'MA_END|';
			values += f_end + '|';
			oprdrs += '<=|';
			format += 'DATE|';
			
		}
		
		//alert('filtros: ' + params + '\nvalores: ' + values + '\nopers: ' + oprdrs + '\nformato: ' + format);
		
		
		$.ajax({
			url: "ajax.php", type: "POST", async: false,
			data:
			{
		  		resource: 'threshold', 
		  		action: 'srch_maintenance', 
		  		filtros: params,
				valores: values,
				oprs: oprdrs,
				format: format
			},
		  	dataType: "json",
		 	success: function(data)
			{
				if (data.success == true )
				{
					$("#maintenance tbody").html(data.html);
					$("#reset_srch").show();
					return true;
				}
				else
				{  
					show_error(data.error);
					return false;
				}
			}
		}); 
	}
	else
	{
		//alert('No hay criterios de busqueda.');
		show_error('No hay criterios de busqueda.');
	}
}


function reset_srch_service_threshold()
{
	$("#inp_srch_id_service").val(0);
	
	var params = '';
	var values = '';
	var oprdrs = '';
	var format = '';
	
	params = 'MA_SE_ID_SERVICE|';
	values = '-1|';
	oprdrs = '!=|';
	format = 'NUM|';
	
	
	//alert('filtros: ' + params + '\nvalores: ' + values + '\nopers: ' + oprdrs + '\nformato: ' + format);
	
	
	$.ajax({
		url: "ajax.php", type: "POST", async: false,
		data:
		{
			resource: 'threshold', 
			action: 'srch_maintenance', 
			filtros: params,
			valores: values,
			oprs: oprdrs,
			format: format
		},
		dataType: "json",
		success: function(data)
		{
			if (data.success == true )
			{
				$("#maintenance tbody").html(data.html);
				$("#reset_srch").hide();
				return true;
			}
			else
			{  
				show_error(data.error);
				return false;
			}
		}
	}); 
}
