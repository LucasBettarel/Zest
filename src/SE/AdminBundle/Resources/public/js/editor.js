$(document).ready(function() {

	var table = $('#editor').DataTable( {
		paging: true,
	  	"dom": 'rtip',
	  	"info": false,
        buttons:[{
            text: "<i class='glyphicon glyphicon-th'></i> All",
            action: function ( e, dt, node, config ) {
            },
            className: 'btn-default'
        },{
            text: "<i class='glyphicon glyphicon-time'></i> Waiting",
            action: function ( e, dt, node, config ) {
            },
            className: 'btn-warning'
        },{
            text: "<i class='glyphicon glyphicon-ok'></i> Accepted",
            action: function ( e, dt, node, config ) {
            },
            className: 'btn-success'
        },{
            text: "<i class='glyphicon glyphicon-remove'></i> Rejected",
            action: function ( e, dt, node, config ) {
            },
            className: 'btn-danger'
        },{
            text: "<i class='glyphicon glyphicon-question-sign'></i> Ignored",
            action: function ( e, dt, node, config ) {
            },
            className: 'btn-info'
        }]
    });

    table.buttons().container().prependTo($('#editor-panel .panel-heading')).addClass('pull-right');
});