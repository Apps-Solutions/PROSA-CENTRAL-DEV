var max_msg_length = 250;

function validate_msg_length()
{
    var msg = $("#inp_message").val();

    if (msg.length >= max_msg_length)
    {
        msg = msg.substring(0, max_msg_length);
        $("#inp_message").val(msg);
    }

    $("#lbl_num_caract").html(max_msg_length - msg.length);

}

/*Devuelve todos los clientes y usuarios de un servicio click individual en servicios*/
function service_row(id, tipo)
{
    $('#ajax-load').attr('src', 'img/ajax-loader.gif');

    $.ajax({
        url: "ajax.php",
        type: "POST",
        async: false,
        data: {
            resource: 'service',
            action: 'get_clients_users',
            id: id,
            type: tipo
        },
        dataType: "json",
        success: function (data)
        {
            if (data.success == true)
            {
                var usrs = data.users;
                var clients = data.clients;

                var id_usr = '';
                var id_client = '';

                if (clients.length > 0)
                {
                    for (var i = 0; i < clients.length; i++)
                    {
                        id_client = "#inp_client_" + clients[i];
                        $(id_client).prop('checked', $("#inp_notif_user_service_" + id).prop('checked'));
                    }
                }

                if (usrs.length > 0)
                {
                    for (var i = 0; i < usrs.length; i++)
                    {
                        id_usr = "#inp_usr_" + usrs[i].replace(".", "_");
                        $(id_usr).prop('checked', $("#inp_notif_user_service_" + id).prop('checked'));
                    }
                }

                $('#ajax-load').removeAttr('src');

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


/*Devuelve servicios de un cliente*/
function service_client(id, tipo)
{
    $('#ajax-load').attr('src', 'img/ajax-loader.gif');

    $.ajax({
        url: "ajax.php",
        type: "POST",
        async: false,
        data: {
            resource: 'service',
            action: 'get_services_clients',
            id: id,
            type: tipo
        },
        dataType: "json",
        success: function (data)
        {
            if (data.success == true)
            {
                var services = data.services;

           		 var id_service = '';

                if (services.length > 0)
                {
                    for (var i = 0; i < services.length; i++)
                    {
                        id_service = "#inp_notif_user_service_" + services[i];
                        $(id_service).prop('checked', $("#inp_client_" + id).prop('checked'));
                    }
                }
				
                $('#ajax-load').removeAttr('src');

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


function all_clients()
{
    $('#ajax-load').attr('src', 'img/ajax-loader.gif');


    $("input[name='clients[]']").each(function () {

        if (this.checked)
        {
            this.checked = false;
        }
    });


    var services = '';
    $("input[name='notif_user_services[]']").each(function () {
        if (this.checked)
        {
            services += this.value + ',';
        }
    });

    var val = $("#inp_serv_all").prop('checked');


    $.ajax({
        url: 'ajax.php',
        type: 'POST',
        async: false,
        data: {
            resource: 'all_clients',
            action: 'get_all_clients',
            services: services,
            type: 'service_client'
        },
        dataType: 'JSON',
        success: function (data) {

            if (data.success == true)
            {
                var id_client = '';
                var id_usr = '';

                var clients = data.clients;
                var usrs = data.users;

                if (clients.length > 0)
                {
                    for (var i = 0; i < clients.length; i++)
                    {
                        for (var x = 0; x < clients[i].length; x++)
                        {
                            id_client = "#inp_client_" + clients[i][x];
                            $(id_client).prop('checked', val);
                        }
                    }
                }


                if (usrs.length > 0)
                {
                    for (var i = 0; i < usrs.length; i++)
                    {
                        for (var x = 0; x < usrs[i].length; x++)
                        {
                            id_usr = "#inp_usr_" + usrs[i][x].replace(".", "_");
                            $(id_usr).prop('checked', true);
                        }
                    }
                }


                $('#ajax-load').removeAttr('src');

            }
        }

    });

}



function all_services()
{
    $('#ajax-load').attr('src', 'img/ajax-loader.gif');


    $("input[name='notif_user_services[]']").each(function () {

        if (this.checked)
        {
            this.checked = false;
        }
    });

    var clients = '';

    $("input[name='clients[]']").each(function () {
        if (this.checked)
        {
            clients += this.value + ',';
        }
    });


    $.ajax({
        url: 'ajax.php',
        type: 'POST',
        async: false,
        data: {
            resource: 'all_clients',
            action: 'get_all_clients',
            services: clients,
            type: 'client_service'
        },
        dataType: 'JSON',
        success: function (data) {

            if (data.success == true)
            {
                var id_client = '';

                var clients = data.clients;

                if (clients.length > 0)
                {
                    for (var i = 0; i < clients.length; i++)
                    {
                        if (clients[i] != false)
                        {
                            for (var x = 0; x < clients[i].length; x++)
                            {
                                id_client = "#inp_notif_user_service_" + clients[i][x];
                                $(id_client).prop('checked', true);

                            }
                        }

                    }
                }

                /*
                 if (usrs.length > 0)
                 {
                 for (var i = 0; i < usrs.length; i++)
                 {
                 if (usrs[i] != false)
                 {
                 id_usr = "#inp_usr_" + usrs[i].replace(".", "_");
                 $(id_usr).prop('checked', true);
                 }
                 
                 }
                 }*/

                $('#ajax-load').removeAttr('src');

            }
        }

    });

}

function send_notification()
{
    //$('#ajax-load').attr('src', 'img/ajax-loader.gif');
    $('#div_loader').show();

    var services = '';
    var clients = '';
    var users = '';

    var mesage = $("textarea[name='mesage']").val().replace(/\n\r?/g, '\\n').replace(/&/g, '%26');

    $("input[name='notif_user_services[]']").each(function () {
        if (this.checked)
        {
            services += this.value + ',';
        }
    });

    $("input[name='clients[]']").each(function () {
        if (this.checked)
        {
            clients += this.value + ',';
        }
    });

    $("input[name='users[]']").each(function () {
        if (this.checked)
        {
            users += this.value + ',';
        }
    });


    $.ajax({
        url: 'ajax.php',
        type: 'POST',
        async: false,
        data: {
            resource: 'notification',
            action: 'send_notification',
            mesage: mesage,
            services: services,
            clients: clients,
            users: users
        },
        dataType: 'JSON',
        success: function (data)
        {
        
            $('#ajax-load').removeAttr('src');
            $('#div_loader').hide();
            if (data.success == true)
            {
                $('#ajax-load').removeAttr('src');
                
                
                $("input[name='notif_user_services[]']").each(function () {
                    this.checked = false;
                });
            
                $("input[name='clients[]']").each(function () {
                    this.checked = false;
                });
            
                $("input[name='users[]']").each(function () {
                    this.checked = false;
                });
                
                $("textarea[name='mesage']").val('');
                
                show_message('Notificacion enviada exitosamente.');
                
                return true;
            }
            else
            {
                $('#ajax-load').removeAttr('src');
                alert(data.error);
                return false;
            }
        }
    });
}


function search_notificatios(field, table_id)
{
    var srch = $("input[name='" + field + "']").val();

    if (srch != "")
    {
        srch = srch.trim().toLowerCase();
        var found = false;
        var client = "";
        var code = "";

        $("#" + table_id + " tbody tr").each(function ()
        {

            client = ($(this).find("td")[0]).innerHTML;
            client = client.trim().toLowerCase();

            code = ($(this).find("td")[1]).innerHTML;
            code = code.trim().toLowerCase();

            if (client.indexOf(srch) > -1 || code.indexOf(srch) > -1)
            {
                $(this).show('fast');
                found = true;
                $('#err_div').css('display', 'none');
            }
            else
            {
                $(this).hide('fast');
            }
        });

        if (!found)
            show_error("No se encontraron registros con " + srch);

    }
    else
    {
        $("#" + table_id + " tbody tr").each(function ()
        {
            $(this).show('fast');
        });
    }

}



$(document).ready(function ()
{
    $('#inp_serv_all').click(function ()
    {
        var val = this.checked;

        $('input[name="notif_user_services[]"]').prop('checked', val);

        all_clients();
    });

    $('#inp_cli_all').click(function ()
    {
        var val = this.checked;
        $('input[name="clients[]"]').prop('checked', val);

        all_services();
    });

    $('#inp_usr_all').click(function ()
    {
        var val = this.checked;
        $('input[name="users[]"]').prop('checked', val);

    });

});