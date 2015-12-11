$(document).ready(function() {

	var table = $('#table').DataTable( {
		paging: false,
	  	"dom": 'lrtip',
	  	"info": false,
        buttons:[{
            text: "<i class='fa fa-edit' title='Edit this input'></i>",
            action: function ( e, dt, node, config ) {
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
      getForm($(event.relatedTarget).data('type'), $(event.relatedTarget).data('id'));
      var modal = $(this);

      //modal.find('.modal-body').val(recipient)
    });

    $('#formModal').on('hide.bs.modal', function (event) {
        table.columns([7,8,9]).visible(true);
        table.column(10).visible(false);
    });

    $(document).on('click', '#delete-entry', function(e){
      e && e.preventDefault();
      if (confirm("Warning ! If you delete this entry, you have to justify why the employee was not at work. Are you sure to continue? ") == true) {
        var id = $(this).attr('data-id') 
        deleteClick(id);
      }else{
        table.columns([7,8,9]).visible(true);
        table.column(10).visible(false);
      }
    });

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

function getForm(type, entryId){
    if( type == "@new" ){
//        var ajaxForm = ajaxFormNew;
        entryId = null;
        $('.modal-title').text('New Entry');
        $('.modal-body .alert').removeClass('alert-info alert-warning').addClass('alert-info');
        $('.modal-body .alert').html('<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Missing Employee ?</strong><br>You can add his details here and validate. It will be added to the input after review. Productivity will be recalculated.');
        initAjaxForm(entryId);
    }else{
//        var ajaxForm = ajaxFormEdit;
        $('.modal-title').text('Edit Entry');
        $('.modal-body .alert').removeClass('alert-info alert-warning').addClass('alert-warning');
        $('.modal-body .alert').html('<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Editing an Employee entry?</strong><br>You can edit the details here and validate. The input Productivity will be updated after review.');
        initAjaxForm(entryId);
    }
}

function deleteClick(id){
    $.post(
      ajaxDeleteEntry,               
      {idEntry: id}, 
      function(response){
        if(response.code == 100 && response.success){
          console.log('entry trouve, deleted');
          $('#table [data-id="'+id+'"]').remove();
        }
        else{
            alert('Sorry, a strange error occurred... Please try again or contact Lucas !');
        }
      },
      "json");    
}

function initAjaxForm(entryId)
{
    $('body').on('submit', '.ajaxForm', function (e) {
 
        e.preventDefault();
 
        $.ajax({
            type: $(this).attr('method'),
            url: $(this).attr('action'),
            data: $(this).serialize(),
            input: id,
            entry: entryId
        })
        .done(function (data) {
            if (typeof data.message !== 'undefined') {
                alert(data.message);
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            if (typeof jqXHR.responseJSON !== 'undefined') {
                if (jqXHR.responseJSON.hasOwnProperty('form')) {
                    $('#form_body').html(jqXHR.responseJSON.form);
                }
 
                $('.form_error').html(jqXHR.responseJSON.message);
 
            } else {
                alert(errorThrown);
            }
 
        });
    });
}
