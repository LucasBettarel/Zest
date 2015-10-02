$(document).ready(function() {

var d = new Date();
$('#monthpicker').monthpicker({'minYear' : 2015, 'maxYear' : 2016});
$('.monthpick').val(d.getMonth());
$('.yearpick').val(d.getFullYear());
var monthVal = parseInt($('.monthpick').val()) + 1;
var yearVal = $('.yearpick').val();
$('form select').addClass('form-control');

	createPie(importErrors, inputErrors, toErrors, hourErrors);

	$('#history').DataTable( {
		"paging": true,
	  "dom": 'lrtip',
	  "info": false,
    "order": [[ 0, "desc" ]]
	});

	$('#errors').DataTable( {
		"scrollY": "203px",
		"paging": false,
	  "dom": 'lrtip',
	  "info": false
	});

  $('.panel-warning .dataTables_scrollHeadInner').css('padding-left','0px');
  $('.panel-warning .table').css('margin-bottom','0px');

	$('#filters a').click(function(){
	  	filterColumn( $(this).parents('div').attr('id'), $(this).attr('id') );
	  	$(this).siblings().removeClass('label-primary').addClass('label-default');
      $(this).removeClass('label-default').addClass('label-primary');

      if ($(this).parent().attr('id') == 1){
        if($(this).hasClass('allT') || $(this).attr('id') == "Outbound3" || $(this).attr('id') == "Inbound3"){
          if(!$('#filters .shifts').hasClass('hide')){
            $('#filters .shifts').addClass('hide');
          }
        }else if ($(this).attr('id') == "Adaptation" || $(this).attr('id') == "Taskforce" || $(this).attr('id') == "Releasing"){
         $('#filters .shifts').removeClass('hide');
         if(!$('#filters .shifts #3').hasClass('hide')){
            $('#filters .shifts #3').addClass('hide');
          } 
        }else{
          $('#filters .shifts').removeClass('hide');
          $('#filters .shifts #3').removeClass('hide');
        }
        $('#filters .shifts a').removeClass('label-primary').addClass('label-default');
        $('#filters .shifts .allS').removeClass('label-default').addClass('label-primary');

        //if filter by team, reset filter shift to all
        filterColumn( 2, "" );
      }

	});

    $("*[data-toggle='tooltip']").tooltip();

    $(document).on('click', '#ignore', function(e){
      e && e.preventDefault();
      if (confirm("Warning ! If you choose to ignore this missing input, the productivity for this date/team/shift will be 0. Do you want to continue? ") == true) {
        var id = $(this).attr('data-id') 
        ignoreClick(id);
      }
    });

    $(document).on('click', '#refresh', function(e){
      e && e.preventDefault();
      if (confirm("Refresh the missing inputs?") == true) {
        refreshClick();
      }
    });

    $(document).on('click', '#delete', function(e){
      e && e.preventDefault();
      if (confirm("Warning ! If you delete this input, the productivity for this date/team/shift will be 0. Are you sure to continue? ") == true) {
        var id = $(this).attr('data-id') 
        deleteClick(id);
      }
    });

    $(document).on('change', '.month select', function(e){
      monthVal = parseInt($('.monthpick').val()) + 1;
      yearVal = $('.yearpick').val();
      $.get(
        ajaxDash,               
        {month: monthVal, year: yearVal}, 
        function(r){
          console.log(r);
          createPie(r.m, r.i, r.t, r.h);

          historyTable = $('#history').DataTable();
          historyTable.clear();
          historyTable.rows.add(r.hTemplate).draw();
          historyTable.draw();

          errorTable = $('#errors').DataTable();
          errorTable.clear();
          errorTable.rows.add(r.eTemplate).draw();
          errorTable.draw();
        },
      "json");
    });
    
});

function filterColumn ( i , val) {
    $('#history').DataTable().column( i ).search(val).draw();
    $('#errors').DataTable().column( i ).search(val).draw();
}

function ignoreClick(id){
    $.post(
      ajaxIgnore,               
      {idInput: id}, 
      function(response){
        if(response.code == 100 && response.success){
        console.log('input trouve, ignored', $('#errors [data-id="'+id+'"]').closest('tr'));
          $('#errors [data-id="'+id+'"]').closest('tr').remove();
        }
        else{
            alert('Sorry, a strange error occurred... Please try again or contact Lucas !');
        }
      },
      "json");    
}

function deleteClick(id){
    $.post(
      ajaxDelete,               
      {idInput: id}, 
      function(response){
        if(response.code == 100 && response.success){
          console.log('input trouve, deleted', $('#history [data-id="'+id+'"]').closest('tr'));
          $('#history [data-id="'+id+'"]').closest('tr').remove();
        }
        else{
            alert('Sorry, a strange error occurred... Please try again or contact Lucas !');
        }
      },
      "json");    
}

function refreshClick(){
    $.get(
      ajaxMi,               
      {}, 
      function(response){
        if(response.code == 100 && response.success){
          $('#refresh').removeClass('text-mute').addClass('text-success').prop('disabled', true);
          $('#refresh i').removeClass('glyphicon-refresh').addClass('glyphicon-ok');
        }
        else{
            alert('Sorry, a strange error occurred... Please try again or contact Lucas !');
        }
      },
      "json");    
}

function createPie(imports, inputs, tos, hours){
  var sum = imports + inputs + tos + hours;
  if (sum>0){
    $('.panel:first .panel-heading h4').html("<i class='glyphicon glyphicon-alert text-danger'> </i> "+sum+" errors to review!");
  }else{
    $('.panel:first .panel-heading h4').html("<i class='glyphicon glyphicon-ok text-success'> </i> "+sum+" errors to review!");
  }
  $('#container').highcharts({
        chart: {
            type: 'pie'
        },
        title: {
            text: ''
        },
        legend: {
            align: 'right',
            verticalAlign: 'top',
            layout: 'vertical',
            x: 0,
            y: 100
        },
        yAxis: {
            title: {
                text: 'error percentage'
            }
        },
        credits: {
            enabled: false
        },
        plotOptions: {
            pie: {
                shadow: false
            }
        },
        tooltip: {
            formatter: function() {
                return '<b>'+ this.point.name +'</b>: '+ this.y +' ';
            }
        },
        series: [{
            name: 'error',
            data: [["Missing SAP imports",imports],["Missing inputs",inputs],["TO line not affected",tos], ["Missing manhours", hours]],
            size: '100%',
            innerSize: '60%',
            showInLegend:true,
            dataLabels: {
                enabled: false
            }
        }]
    });
}
