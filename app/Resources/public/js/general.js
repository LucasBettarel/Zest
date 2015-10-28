$(document).ready(function() {

$(document)
	.ajaxStart(function () {
		$('#loadingModal').modal({backdrop: 'static', keyboard: false});
  	})
	.ajaxStop(function () {
    	$('#loadingModal').modal('hide');
});

$(window).scroll(function() {
	if ($(window).scrollTop() > 75) {
		$('#filters').addClass("fix-filter");
	} else {
		$('#filters').removeClass("fix-filter");
	}
});

});