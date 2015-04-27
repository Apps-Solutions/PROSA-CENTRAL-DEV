function edit_client( id ){
	if (id > 0){
		$.ajax({
			url: "ajax.php", type: "POST", async: false,
			data: {
		  		resource: 'client', 
		  		action: 'get_client_info', 
		  		id_client: id
			},
		  	dataType: "json",
		 	success: function(data) {
				if (data.success == true ){
					var cl = data.client;
					if ( cl.id_client > 0 ){ 
						$('#inp_id_client').val( cl.id_client ); 
						$('#inp_client').val( cl.client );  
						$('#inp_code' 	).val( cl.code); 
						
						$('#mdl_frm_client').modal('show');
					} else {
						show_error( "No se encontró el registro.");
						return false;
					} 
				}
				else {  
					show_error( data.error );
					return false;
				}
			}
		}); 
	} else {
		clean_form();
		$('#mdl_frm_client').modal('show'); 
	} 	
}

function delete_client( id ){
	if ( !confirm('¿Está seguro que desea borrar el cliente?') )
		return;
	if (id > 0){
		$.ajax({
			url: "ajax.php", type: "POST", async: false,
			data: {
		  		resource: 'client', 
		  		action: 'delete_client', 
		  		id_client: id
			},
		  	dataType: "json",
		 	success: function(data) {
				if (data.success == true )  {
					show_message( data.message );
					window.location.reload();
				}
				else {  
					show_error( data.error );
					return false;
				}
			}
		}); 
	} else {
		clean_form();
		$('#mdl_frm_client').modal('show'); 
	} 	
}


function clean_form(){
	$('#inp_id_client').val( 0 ); 
	$('#inp_client').val( "");  
	$('#inp_code' 	).val( ""); 
}

function cancel_client_edition(){
	clean_form();
	$('#mdl_frm_client').modal('hide'); 
}

function get_client_services( id ){
	if (id > 0){
		$.ajax({
			url: "ajax.php", type: "POST", async: false,
			data: {
		  		resource: 'client', 
		  		action: 'get_client_services_form', 
		  		id_client: id
			},
		  	dataType: "json",
		 	success: function(data) {
				if (data.success == true )  {
					$('#inp_id_client_service').val( id );  
					$('#div_client_service_form').html( data.html );
					$('#mdl_client_services').modal('show'); 
				}
				else {  
					show_error( data.error );
					return false;
				}
			}
		}); 
	} else {
		show_error( "No se recibieron los datos necesarios." ); 
		$('#mdl_client_services_frm').modal('hide'); 
	} 	
}

function show_user_services( index, user ){ 
	if ( index >= 0 && user != '' ){ 
		$('.div_user_service').hide();
		$('#div_user_services_' + index + ' table').html( "<tr> <td> Cargando... </td> </tr>" );
		$('#div_user_services_' + index + '').show('slide');
		$.ajax({
			url: "ajax.php", type: "POST", async: false,
			data: {
		  		resource: 'client', 
		  		action: 'get_client_users_services_table', 
		  		id_client: $('#inp_id_client_service').val(), 
		  		user: user
			},
		  	dataType: "json",
		 	success: function(data) { 
				if (data.success == true )
				{ 
					$('#div_user_services_' + index + ' table').html( data.html );
					$('#div_user_services_' + index + '').show('slide');  
				}
				else {  
					show_error( data.error );
					return false;
				}
			}
		}); 
	}
}

function set_client_service( id_client, id_service ){
	if (id_client > 0 && id_service > 0 ){
		
		var inp = "inp_cl_" + id_client + "_service_" + id_service + "";
		
		var value = $("#" + inp ).val(); 
		var checked = $("#" + inp ).is(':checked') ;
		
		$.ajax({
			url: "ajax.php", type: "POST", async: false,
			data: {
		  		resource: 'client', 
		  		action: 'set_client_service', 
		  		id_client: id_client, 
		  		id_service: id_service,
		  		status: checked
			},
		  	dataType: "json",
		 	success: function(data) {
				if (data.success == true )  {
					show_message( data.message );  
					return true; 
				}
				else {  
					show_error( data.error );
					return false;
				}
			}
		}); 
	} else {
		show_error( "No se recibieron los datos necesarios.");
		return false; 
	} 
}

function save_service(idClient, idService)
{
	
	if (idClient > 0 && idService > 0)
	{
		$.ajax({
			url: "ajax.php",
			type: "POST", 
			data:
			{
				resource: 		'client',
				action:	 		'insert_service',
				id_client:		idClient,
				id_service:		idService
			},
			dataType: "json",
			success: function(data)
			{
				if (data.success == true )
				{
					show_message( 'Service Activated' );
					return true;
				}
				else
				{  
					show_error( data.error );
					return false;
				}
			}
		});
	}
	
	
}

function set_client_user_service(id_cliente, id_user, id_service)
{
	if (id_cliente > 0 && id_user != "" && id_service > 0 )
	{
		
		var inp = "inp_cl_" + id_cliente + "_us_"  + id_user + "_service_" + id_service + "";				
		var value = $("#" + inp ).val(); 
		var checked = $("#" + inp ).is( ":checked" );				
		
		
		$.ajax({
			url: "ajax.php", type: "POST", async: false,
			data: {
		  		resource: 'user', 
		  		action: 'set_client_user_service',
				id_client: id_cliente,
		  		us_user: id_user, 
		  		id_service: id_service,
		  		status: checked
			},
		  	dataType: "json",
		 	success: function(data)
			{
				if (data.success == true )
				{
					show_message( "Informaci�n actualizada." );
					return true; 
				}
				else
				{  
					show_error( data.error );
					return false;
				}
			}
		});
	} else {
		show_error( "No se recibieron los datos necesarios.");
		return false; 
	}
	
}

function search_client()
{
	var srch = $("#inp_cli_search").val();
	
	if (srch != "")
	{
		srch = srch.trim().toLowerCase();
		var found = false;
		var client = "";
		var code = "";
		
		$("#clients_table tbody tr").each(function ()
		{
			
			client = ($(this).find("td")[0]).innerHTML;
			client = client.trim().toLowerCase();
			
			code = ($(this).find("td")[1]).innerHTML;
			code = code.trim().toLowerCase();
			
			if ( client.indexOf(srch) > -1 || code.indexOf(srch) > -1 )
			{
				$(this).show('fast');
				found = true;
			}
			else
			{
				$(this).hide('fast');
			}	
		});
		
		if ( !found )
			show_error("No se encontro el cliente.");

	}
	else
	{
		$("#clients_table tbody tr").each(function ()
		{
			$(this).show('fast');	
		});
	}
	
}
