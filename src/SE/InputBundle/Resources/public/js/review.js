$(document).ready(function() {

	  $('#table').DataTable( {
	  	paging: false,
	  	"dom": 'lrtip',
	  	"info": false
	  });

	  $('#filters a').click(function(){
	  	filterColumn( $(this).parents('div').attr('id'), $(this).attr('id') );
	  	$(this).siblings().removeClass('label-primary').addClass('label-default');
	  	$(this).removeClass('label-default').addClass('label-primary');
	  })

});

function filterColumn ( i , val) {
    $('#table').DataTable().column( i ).search(val).draw();
}