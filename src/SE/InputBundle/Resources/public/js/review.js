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
        plotOptions: {
            pie: {
                shadow: false
            }
        },
        tooltip: {
            formatter: function() {
                return '<b>'+ this.point.name +'</b>: '+ this.y +' %';
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

});

function filterColumn ( i , val) {
    $('#table').DataTable().column( i ).search(val).draw();
}