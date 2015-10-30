$(document).ready(function() {

Highcharts.createElement('link',{href:'//fonts.googleapis.com/css?family=Dosis:400,600',rel:'stylesheet',type:'text/css'},null,document.getElementsByTagName('head')[0]);Highcharts.theme={colors:["#7cb5ec","#f7a35c","#90ee7e","#7798BF","#aaeeee","#ff0066","#eeaaee","#55BF3B","#DF5353","#7798BF","#aaeeee"],chart:{backgroundColor:null,style:{fontFamily:"Dosis, sans-serif"}},title:{style:{fontSize:'16px',fontWeight:'bold',textTransform:'uppercase'}},tooltip:{borderWidth:0,backgroundColor:'rgba(219,219,216,0.8)',shadow:false},legend:{itemStyle:{fontWeight:'bold',fontSize:'13px'}},xAxis:{gridLineWidth:1,labels:{style:{fontSize:'12px'}}},yAxis:{minorTickInterval:'auto',title:{style:{textTransform:'uppercase'}},labels:{style:{fontSize:'12px'}}},plotOptions:{candlestick:{lineColor:'#404048'}},background2:'#F0F0EA'};Highcharts.setOptions(Highcharts.theme);

var attJson;
var monthVal;
var yearVal;
var attendanceTable;
initData();

monthVal = parseInt($('.monthpick').val()) + 1;
yearVal = $('.yearpick').val();
$('form select').addClass('form-control');

$.get(
    ajaxAttendance,               
    {
        month: monthVal,
        year: yearVal
    }, 
    function(response){
        attJson = response.template;
        updateCharts(attJson);
    },
    "json"); 

$('#filters a').click(function(){
  $this = $(this);
  filterData($this, attJson);
});

});

function initData(){
    var d = new Date();
    $('#monthpicker').monthpicker({'minYear' : 2015, 'maxYear' : 2016});
    $('.monthpick').val(d.getMonth());
    $('.yearpick').val(d.getFullYear());
}

function updateCharts(json){
    attendanceTable = $('#attendance').DataTable( {
        scrollY:        "480px",
        scrollX:        true,
        scrollCollapse: true,
        paging:         false,
        fixedColumns:   true,
        retrieve: true,
        "dom": 'lrtip',
        "info": false,
        "columnDefs": [
            {
                "targets": [ 1 ],
                "visible": false,
            },
            {
                "targets": [ 2 ],
                "visible": false
            }
        ]
    });
    attendanceTable.rows.add(json).draw();
    $("*[data-toggle='tooltip']").tooltip({html :'true',container: 'body'});
    $('.e-day-ok').closest('td').addClass('green');
    $('.e-day-low').closest('td').addClass('yellow');
    $('.e-day-high').closest('td').addClass('orange');
    $('.e-day-absent').closest('td').addClass('grey');
    $('.e-day-leave').closest('td').addClass('blue');
}

function createCharts(json){
      
  $('#container-attendance').highcharts({
    title: {
        text: 'Monthly-to-date attendance rate',
        x: -20 //center
    },
    xAxis: {
        categories: json['days'],
        title: {
                text: null
            }
    },
    yAxis: {
        title: {
            text: ''
        },
        plotLines: [{
            value: 0,
            width: 1,
            color: '#808080'
        }]
    },
    credits: {
        enabled: false
    },
    tooltip: {
        valueSuffix: '%'
    },
    legend: {
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'middle',
        borderWidth: 0
    },
    series: [{
        name: 'HubAsia',
        data: [1,2,3,3,5,3,6,3,6,3,3,3,0]
    /*}, {
        name: 'Outbound 4',
        data: jsonAttendance[o4][rate]
    }, {
        name: 'Inbound 4',
        data: jsonAttendance[i4][rate]
    }, {
        name: 'Outbound 3',
        data: jsonAttendance[o3][rate]
    }, {
        name: 'Inbound 3',
        data: jsonAttendance[i3][rate]
    */}]
  });

}

function filterData($this, json){
  $this.siblings().removeClass('label-primary').addClass('label-default');
  $this.removeClass('label-default').addClass('label-primary');
    
  //update charts data
  //var containerProd = $('#container-prod').highcharts();
  //var containerAct = $('#container-activities').highcharts();

  if ($this.parent().attr('id') == 1){//team
    if($this.attr('id') == 0 || $this.attr('id') == 4 || $this.attr('id') == 5){
      if(!$('#filters .shifts').hasClass('hide')){
        $('#filters .shifts').addClass('hide');
      }
    }else if ( $this.attr('id') == 3 || $this.attr('id') == 6 || $this.attr('id') == 8 || $this.attr('id') == 9){
     $('#filters .shifts').removeClass('hide');
     if(!$('#filters .shifts #3').hasClass('hide')){
        $('#filters .shifts #3').addClass('hide');
      } 
    }else{
      $('#filters .shifts, #filters .shifts #3').removeClass('hide');
    }
    $('#filters .shifts a').removeClass('label-primary').addClass('label-default');
    $('#filters .shifts #0').removeClass('label-default').addClass('label-primary');

    //loadCharts(json, $this.attr('id'), 0, containerProd, containerAct);

    if($this.attr('id') != 0){
      $('#attendance').DataTable().column(1).search($this.text()).draw();  
    }else{
      $('#attendance').DataTable().column(1).search("").draw();  
    }
    //if filter by team, reset filter shift to all
    $('#attendance').DataTable().column(2).search("").draw(); 
  }else{
   //shift
   var teamId = $('#filters #1').find('.label-primary').attr('id');
   //loadCharts(json, teamId, $this.attr('id'), containerProd, containerAct);
   if($this.attr('id') != 0){
      $('#attendance').DataTable().column(2).search($this.attr('id')).draw();  
    }else{
      $('#attendance').DataTable().column(2).search("").draw();  
    }
  }
}
