$(document).ready(function() {
	$(document).on('click', '#presence :checkbox', function(e) {
		$(this).next('textarea').toggleClass('hide');
	});

	$(document).on('click', '#comment', function(e) {
		$(this).siblings('div').toggleClass('hide');
	});

	$(document).on('click', '[data-toggle="popover"]', function(e) {
		$(this).popover();
	});

	$(document).on('click', '.transfer', function(e){
		$activitiesHolder = $("#"+$(this).attr('data-sub-target'));
		$rowHolder = $('*[data-content="'+$(this).attr('data-target')+'"]');
		//****************************************************************************
		//manque le cas ou on disable qu'une activite, puis qu'on supprime les autres

		if ($activitiesHolder.attr('data-counter') == 1){
			//disable toute la ligne, sauf transfer
			if($(this).attr('data-disabled') == 'false'){
				//disable que l'activite, sauf transfer et ajoute la propriete a transfer
				$(rowHolder).find("input, select, textarea").not('.transfer').prop('disabled', true);
				$(this).attr('data-disabled', 'true');
				console.log('1 ligne disable');
			}
			else{
				$rowHolder.find("input, select, textarea").not('.transfer').prop('disabled', false);
				$(this).attr('data-disabled', 'false');
				console.log('1 ligne enable');
			}
		}
		else{
			if($(this).attr('data-disabled') == 'false'){
				//disable que l'activite, sauf transfer et ajoute la propriete a transfer
				$(this).closest('row').find("input, select").not('.transfer').prop('disabled', true);
				$(this).attr('data-disabled', 'true');
				console.log('x ligne disable');
			}
			else{
				$(this).closest('row').find("input, select").not('.transfer').prop('disabled', false);
				$(this).attr('data-disabled', 'false');
				console.log('x ligne enable');
			}
		}
	});

	$('form').children('div').last().hide();
});