$(document).ready(function() {

	var table = $('#table').DataTable( {
		paging: false,
	  	"dom": 'lrtip',
	  	"info": false,
        buttons:[{
            text: "<i class='fa fa-edit' title='Edit this input'></i>",
            action: function ( e, dt, node, config ) {
               console.log('edit!');
               dt.columns([7,8,9]).visible(false);
               dt.column(10).visible(true);
            },
            className: 'btn-info'
        },{
            extend: 'excelHtml5',
            text: "<i class='fa fa-file-excel-o' title='Export in Excel'></i>",
            className: 'btn-success'
        },{
            extend: 'pdfHtml5',
            text: "<i class='fa fa-file-pdf-o' title='Export in PDF'></i>",
            className: 'btn-danger'
        }],
        "columnDefs": [{
            "targets": [ 10 ],
            "visible": false,
        }]
    });

    table.buttons().container().prependTo($('#input-details .panel-heading')).addClass('pull-right');
    $('div.dt-buttons a').each(function(){
        $(this).attr('data-toggle','tooltip').attr('title',$(this).find('i').attr('title'));
    });

    $("*[data-toggle='tooltip']").tooltip({container: 'body'});

    $('#formModal').on('show.bs.modal', function (event) {
      var content = getForm($(event.relatedTarget).data('type'));
      var modal = $(this);

      //modal.find('.modal-body').val(recipient)
    })

	$.get(
      ajaxActivities,               
      {id: id}, 
      function(response){
        createChart(response.jsonActivities);
      },
      "json"); 
});

function createChart(json){
	$('#container-activities').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'Activities Usage'
        },
        credits: {
            enabled: false
        },
        xAxis: {
            categories: json['cat'],
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: 'hours'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.1f} h</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [{
            name: 'Activities',
            data: json['data']
        }]
    });
}

function getForm(type){
    var ajaxForm = ( type == "@new" ) ? ajaxFormNew : ajaxFormEdit;
    $.post(
        ajaxForm,               
        {id: id},
        function(response){
            console.log(response);
        },
        "json"
    ); 
}
