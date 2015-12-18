$(document).ready(function() {

	var table = $('#editor').DataTable( {
		paging: true,
	  	"dom": 'rtip',
	  	"info": false,
	  	"aoColumnDefs": [
	      { "sType": "string", "aTargets": [ 4 ] }
	    ],
        buttons:[{
            text: "<i class='glyphicon glyphicon-th'></i>",
            action: function ( e, dt, node, config ) {
            	dt.column(4).search("").draw(); 
            },
            className: 'btn-default'
        },{
            text: "<i class='glyphicon glyphicon-time'></i>",
            action: function ( e, dt, node, config ) {
            	dt.column(4).search("time").draw(); 
            },
            className: 'btn-warning'
        },{
            text: "<i class='glyphicon glyphicon-ok'></i>",
            action: function ( e, dt, node, config ) {
            	dt.column(4).search("ok").draw(); 
            },
            className: 'btn-success'
        },{
            text: "<i class='glyphicon glyphicon-remove'></i>",
            action: function ( e, dt, node, config ) {
            	dt.column(4).search("remove").draw(); 
            },
            className: 'btn-danger'
        },{
            text: "<i class='glyphicon glyphicon-question-sign'></i>",
            action: function ( e, dt, node, config ) {
            	dt.column(4).search("question").draw(); 
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

    $('#choice button').click(function(){
      $this = $(this);
      editorChoice($this.attr('data-path'), $('#r-id').html(), $this.attr('id'));
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

	if($this.children().last().find('i').attr('id') == 2){$('#choice button').attr('disabled', true);}
	else{$('#choice button').attr('disabled', false);}
}

function editorChoice(path, id, type){
	$.ajax({
            type: 'POST',
            url: path,
            data: {
            	id: id
            },
        })
        .done(function (data) {
            $('#alert-result').html(data.message).removeClass('hide');
            window.setTimeout(updateDisplay(id, type), 2000);
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR.responseJSON);
            $('#alert-result').text(jqXHR.responseJSON).removeClass('hide');
        });
}

function updateDisplay(id, type){
	$('#editor-panel').toggleClass('col-md-12 col-md-5').siblings('#request-panel').addClass('hide').find('#alert-result').addClass('hide');
	var status = $("#editor-panel tr[id='"+id+"']").children().last();
	if (type == 2){status.html("<i id='2' class='text-success glyphicon glyphicon-ok-sign'></i>");}
	else if (type == 3){status.html("<i id='3' class='text-danger glyphicon glyphicon-remove-sign'></i>");}
	else if (type == 4){status.html("<i id='2' class='glyphicon glyphicon-question-sign'></i>");}
}
