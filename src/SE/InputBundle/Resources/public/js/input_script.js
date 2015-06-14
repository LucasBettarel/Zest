$(document).ready(function() {
	$(document).on('click', '#presence :checkbox', function(e) {
		$(this).next('textarea').toggleClass('hide');
	});

	$('[data-toggle="popover"]').popover();
});