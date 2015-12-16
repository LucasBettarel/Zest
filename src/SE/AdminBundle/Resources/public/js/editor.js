$(document).ready(function() {

	var table = $('#editor').DataTable( {
		paging: true,
	  	"dom": 'rtip',
	  	"info": false,
	  	"aoColumnDefs": [
	      { "sType": "string", "aTargets": [ 3 ] }
	    ],
        buttons:[{
            text: "<i class='glyphicon glyphicon-th'></i>",
            action: function ( e, dt, node, config ) {
            	dt.column(3).search("").draw(); 
            },
            className: 'btn-default'
        },{
            text: "<i class='glyphicon glyphicon-time'></i>",
            action: function ( e, dt, node, config ) {
            	dt.column(3).search("time").draw(); 
            },
            className: 'btn-warning'
        },{
            text: "<i class='glyphicon glyphicon-ok'></i>",
            action: function ( e, dt, node, config ) {
            	dt.column(3).search("ok").draw(); 
            },
            className: 'btn-success'
        },{
            text: "<i class='glyphicon glyphicon-remove'></i>",
            action: function ( e, dt, node, config ) {
            	dt.column(3).search("remove").draw(); 
            },
            className: 'btn-danger'
        },{
            text: "<i class='glyphicon glyphicon-question-sign'></i>",
            action: function ( e, dt, node, config ) {
            	dt.column(3).search("question").draw(); 
            },
            className: 'btn-default'
        }]
    });

    table.buttons().container().prependTo($('#editor-panel .panel-heading')).addClass('pull-right');

    $('#editor tbody tr').click(function(){
      $this = $(this);
      if($('#editor-panel').hasClass('col-md-12')){
          $('#editor-panel').toggleClass('col-md-12 col-md-5').siblings('#request-panel').removeClass('hide');
      }
      entryRequest($this);
    });

    $('.close').click(function(){$('#editor-panel').toggleClass('col-md-12 col-md-5').siblings('#request-panel').addClass('hide');});

});

function entryRequest($this){

	$('#r-id').html($this.attr('id'));
	$('#r-user').html($this.find('.r-user').text());
	$('#r-createdAt').html($this.find('.r-createdAt').text());
	$('#r-input').html($this.find('.r-input').html());
	$('#r-status').html($this.find('.r-status').html());

	$.get(
        ajaxCurrentEntry,               
	    {
	      current: $this.attr('data-current'),
	      request: $this.attr('id')
	    }, 
	    function(r){
	      $('#inject-info').html(r.table);	      
	    },
	    "json"
	);
}
