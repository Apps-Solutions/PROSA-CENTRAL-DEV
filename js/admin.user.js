function show_user_services(index, user)
{

	if ( index >= 0 && user != '' )
	{
	
		$('.div_user_service').hide();
		$('#div_user_services_' + index + ' table').html( "<tr> <td> Cargando... </td> </tr>" );
		$('#div_user_services_' + index + '').show('slide');
		$.ajax({
			url: "ajax.php", type: "POST", async: false,
			data: {
		  		resource: 'user', 
		  		action: 'get_services_prosa_table', 
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

function set_user_prosa_service( id_user, id_service)
{
	if (id_user != "" && id_service > 0 )
	{
		var inp = "inp_serv_user_prosa_"  + id_user + "_service_" + id_service;				
		var value = $("#" + inp ).val(); 
		var checked = $("#" + inp ).is( ":checked" );				
		$.ajax({
			url: "ajax.php", type: "POST", async: false,
			data: {
		  		resource: 'user', 
		  		action: 'set_user_service_prosa',
		  		us_user: id_user, 
		  		id_service: id_service,
		  		status: checked
			},
		  	dataType: "json",
		 	success: function(data)
			{
				if (data.success == true )
				{
					show_message( data.message );
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