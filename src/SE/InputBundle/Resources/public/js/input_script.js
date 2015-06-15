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
});