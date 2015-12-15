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
    });

    $('#formModal').on('hide.bs.modal', function (event) {
        table.columns([7,8,9]).visible(true);
        table.column(10).visible(false);
        //reset form
        $('.modal-body .toggling').addClass('hide');
        $('#activities-prototype').children().remove();
        addSubElement($('#activities-prototype'));
        $('.modal-body form').removeClass('hide');
        $('.modal-footer button[type="submit"]').prop('disabled', false);
        $('form #errors').html("").parent(".col-md-12").addClass('hide');
        $('form').find('.has-error').removeClass('has-error');
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
        entryId = null;
        $('.modal-title').text('New Entry');
        $('.modal-body #information-alert').removeClass('alert-info alert-success alert-warning').addClass('alert-info');
        $('.modal-body #information-alert').html('<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Missing Employee ?</strong><br>You can add his details here and validate. It will be added to the input after review. Productivity will be recalculated.');
        $('#se_inputbundle_editorentry_employee').val(null);
        $('#se_inputbundle_editorentry_sesa').val(null);
    }else{
        $('.modal-title').text('Edit Entry');
        $('.modal-body #information-alert').removeClass('alert-info alert-success alert-warning').addClass('alert-warning');
        $('.modal-body #information-alert').html('<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Editing an Employee entry?</strong><br>You can edit the details here and validate. The input Productivity will be updated after review.');
            $.get(
              ajaxEditPopulate,               
              {id: entryId}, 
              function(r){
                $('#se_inputbundle_editorentry_employee').val(r.entry[0]['employee']);
                $('#se_inputbundle_editorentry_sesa').val(r.entry[0]['sesa']);
                $('#se_inputbundle_editorentry_present').val(r.entry[0]['present']);
                $('#se_inputbundle_editorentry_absence_reason').val(r.entry[0]['absence']);
                $('#se_inputbundle_editorentry_comments').val(r.entry[0]['comments']);

                if(r.entry[0]['comments'] != null){$('.modal-body .txtarea-sm').removeClass('hide');}

                for (var i = 0; i < r.entry.length; i++) {
                    if(i>0){addSubElement($('#activities-prototype'));}
                    console.log(i, r.entry[i]['activity'],  $('#activities-prototype').children('div').last().find('.input-activity'));
                    $('#activities-prototype').children('div').last().find('.input-activity').val(r.entry[i]['activity']);
                    $('#activities-prototype').children('div').last().find('.input-regular-hours').val(r.entry[i]['regularHours']);
                    $('#activities-prototype').children('div').last().find('.input-overtime').val(r.entry[i]['otHours']);
                };
              },
              "json")
            ; 
    }

    $('#se_inputbundle_editorentry_inputEntry').val(entryId);

    $('body').on('submit', '.ajaxForm', function (e) {
 
        e.preventDefault();
        $('.modal-footer button[type="submit"]').prop('disabled', true);
 
        $.ajax({
            type: $(this).attr('method'),
            url: $(this).attr('action'),
            data: $(this).serialize(),
        })
        .done(function (data) {
            if (typeof data.message !== 'undefined') {
                $('.modal-body #information-alert').removeClass('alert-info alert-success alert-warning').addClass('alert-success');
                $('.modal-body #information-alert').html('<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Entry saved successfully!</strong><br>The input will be updated as soon as the changes will be reviewed.');
                $('.modal-body form').addClass('hide');
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            if (typeof jqXHR.responseJSON !== 'undefined') {
                if (jqXHR.responseJSON.hasOwnProperty('form')) {
                    //$('#ajaxForm').html(jqXHR.responseJSON.form);
                }
                $('form #errors').html(jqXHR.responseJSON.message).parent(".col-md-12").removeClass('hide');
                $('.modal-footer button[type="submit"]').prop('disabled', false);
 
            } else {
                alert(errorThrown);
            }
 
        });
    });
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