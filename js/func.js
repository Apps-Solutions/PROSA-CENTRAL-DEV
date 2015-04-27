//
//    Main script of Tracker

/**
 * function show_error()
 * Displays an error on screen
 * 
 * @param   error String
 */
function show_error( error ) {

	if ( error != '' ){ 
		$('#err_span').html( error );
		$('#err_div').show();
	}

}

/**
 * function show_message()
 * Displays a message on screen
 * 
 * @param   message String
 */
function show_message( message ) {

	if ( message != '' ){ 
		$('#msg_span').html( message );
		$('#msg_div').show();
	}

}

/**
 * function update_settings()
 * 
 * @param	option String option to update
 */
function update_settings( option, value ){
	return;
	if ( option != '' ){
		if ( value !== true) value = false; 
		$.ajax({
			url: "ajax.php",
			type: "POST", 
			data: {
		  		resource: 		'settings',
		  		action:	 		'update_settings',
		  		option:			option,
	    		value:			value 
			},
		  	dataType: "json",
		 	success: function(data) {
				if (data.success == true )  {
					//show_message( 'Settings updated' ); 
				}
				else {  
					show_error( data.error );
					return false;
				}
			}
		}); 
	}
}


/// Move Boxes
function WinMove(){
	$( "div.box")
		.not('.no-drop')
		.draggable({
			revert: true,
			zIndex: 2000,
			cursor: "crosshair",
			handle: '.box-name',
			opacity: 0.8
		})
		.droppable({
			tolerance: 'pointer',
			drop: function( event, ui ) {
				var draggable = ui.draggable;
				var droppable = $(this);
				var dragPos = draggable.position();
				var dropPos = droppable.position();
				draggable.swap(droppable);
				setTimeout(function() {
						var dropmap = droppable.find('[id^=map-]');
						var dragmap = draggable.find('[id^=map-]');
						if (dragmap.length > 0 || dropmap.length > 0){
							dragmap.resize();
							dropmap.resize();
						}
						else {
							draggable.resize();
							droppable.resize();
						}
					}, 50);
				setTimeout(function() {
						draggable.find('[id^=map-]').resize();
						droppable.find('[id^=map-]').resize();
					}, 250);
			}
		});
}
//
// Swap 2 elements on page. Used by WinMove function
//
jQuery.fn.swap = function(b){
	b = jQuery(b)[0];
	var a = this[0];
	var t = a.parentNode.insertBefore(document.createTextNode(''), a);
	b.parentNode.insertBefore(a, b);
	t.parentNode.insertBefore(b, t);
	t.parentNode.removeChild(t);
	return this;
}; 



/*  FORMS */ 
  var validate_language = {
      errorTitle : 'No se ha podido enviar el formulario! ',
      requiredFields : 'Requerido. ',
      badTime : 'Tiempo inválido. ',
      badEmail : 'E-mail inválido. ',
      badTelephone : 'Teléfono inválido. ',
      badSecurityAnswer : 'Respuesta de seguridad erronea.',
      badDate : 'Fecha inválida.',
      lengthBadStart : 'La respuesta debe contener entre ',
      lengthBadEnd : ' caracteres',
      lengthTooLongStart : 'Valor mayor a ',
      lengthTooShortStart : 'Valor menor a ',
      notConfirmed : 'El valor no pudo ser confirmado ',
      badDomain : 'Domain incorrecto',
      badUrl : 'URL inválido',
      badCustomVal : 'Valor inválido',
      badInt : 'Número inválido',
      badSecurityNumber : 'SSN inválido.',
      badUKVatAnswer : 'UK VAT invorrecto',
      badStrength : 'Password no es suficientmente fuerte.',
      badNumberOfSelectedOptionsStart : 'Debe elegir al menos ',
      badNumberOfSelectedOptionsEnd : ' opciones',
      badAlphaNumeric : 'Sólo se permiten caracteres alfanuméricos ',
      badAlphaNumericExtra: ' y ',
      wrongFileSize : 'Archivo demasiado pesado ',
      wrongFileType : 'Tipo de archivo inválido ',
      groupCheckedRangeStart : 'Elija entre ',
      groupCheckedTooFewStart : 'Elija al menos ',
      groupCheckedTooManyStart : 'Elija máximo ',
      groupCheckedEnd : ' elemento(s)'
    };

$(document).ready(function() {
	
	$('.show-sidebar').on('click', function () {
		$('div#main').toggleClass('sidebar-show');
		update_settings('show_sidebar', $('div#main').hasClass('sidebar-show'));
		//setTimeout(MessagesMenuWidth, 250);
	});
	$.formUtils.addValidator({
		name : 'select-option',
		validatorFunction : function(value, $el, config, language, $form) { 
			return ( value > 0 );
		},
		errorMessage : 'Elija una opción. ',
		errorMessageKey: 'badSelectedOption'
	});
	
});
