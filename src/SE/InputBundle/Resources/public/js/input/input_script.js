$(document).ready(function() {
	//incorporate all that shit in creation of collection -> event listener take ressources!
/*
	$(document).on('click', '.transfer', function(e){
		$activitiesHolder = $("#"+$(this).attr('data-sub-target'));
		$rowHolder = $('*[data-content="'+$(this).attr('data-target')+'"]');
		//****************************************************************************
		//manque le cas ou on disable qu'une activite, puis qu'on supprime les autres

		if ($activitiesHolder.children().length == 1){
			//disable toute la ligne, sauf transfer
			if($(this).attr('data-disabled') == 0){
				//disable que l'activite, sauf transfer et ajoute la propriete a transfer
				$rowHolder.find("input, select, textarea").not('.transfer').prop('disabled', true);
				$(this).attr('data-disabled', 1);
			}
			else{
				$rowHolder.find("input, select, textarea").not('.transfer').prop('disabled', false);
				$(this).attr('data-disabled', 0);
			}
		}
		else{
			if($(this).attr('data-disabled') == 0){
				//disable que l'activite, sauf transfer et ajoute la propriete a transfer
				$(this).closest('.row').find("input, select").not('.transfer').prop('disabled', true);
				$(this).attr('data-disabled', 1);
			}
			else{
				$(this).closest('.row').find("input, select").not('.transfer').prop('disabled', false);
				$(this).attr('data-disabled', 0);
			}
		}
	});
*/

});

