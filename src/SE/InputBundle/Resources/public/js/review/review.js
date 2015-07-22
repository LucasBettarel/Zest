$(document).ready(function() {
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
            data: [["Missing SAP imports",importErrors],["Missing inputs",inputErrors],["TO line not affected",toErrors], ["Missing manhours", hourErrors]],
            size: '100%',
            innerSize: '60%',
            showInLegend:true,
            dataLabels: {
                enabled: false
            }
        }]
    });

	$('#table').DataTable( {
		paging: false,
	  	"dom": 'lrtip',
	  	"info": false
	});

	$('#errors').DataTable( {
		"scrollY":        "150px",
		paging: false,
	  	"dom": 'lrtip',
	  	"info": false
	});

	$('#filters a').click(function(){
	  	filterColumn( $(this).parents('div').attr('id'), $(this).attr('id') );
	  	$(this).siblings().removeClass('label-primary').addClass('label-default');
	  	$(this).removeClass('label-default').addClass('label-primary');
	});

    $("*[data-toggle='tooltip']").tooltip();

    $(document).on('click', '#ignore', function(e){
      e && e.preventDefault();
      if (confirm("Warning ! If you choose to ignore this missing input, the productivity for this date/team/shift will be 0. Do you want to continue? ") == true) {
        var id = $(this).attr('data-id') 
        ignoreClick(id);
      }
    });
    

    $(document).on('click', '#delete', function(e){
      e && e.preventDefault();
      if (confirm("Warning ! If you delete this input, the productivity for this date/team/shift will be 0. Are you sure to continue? ") == true) {
        var id = $(this).attr('data-id') 
        deleteClick(id);
      }
    });
    
});

function filterColumn ( i , val) {
    $('#table').DataTable().column( i ).search(val).draw();
}

function ignoreClick(id){
    $.post(
      ajaxIgnore,               
      {idInput: id}, 
      function(response){
        if(response.code == 100 && response.success){
         $('#errors').find('#'+id).remove();
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
          console.log('input trouve, deleted', $('#table').find('#'+id));
         $('#table').find('#'+id).remove();
        }
        else{
            alert('Sorry, a strange error occurred... Please try again or contact Lucas !');
        }
      },
      "json");    
}
