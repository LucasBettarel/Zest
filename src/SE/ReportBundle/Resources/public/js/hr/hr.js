$(document).ready(function() {

Highcharts.createElement('link',{href:'//fonts.googleapis.com/css?family=Dosis:400,600',rel:'stylesheet',type:'text/css'},null,document.getElementsByTagName('head')[0]);Highcharts.theme={colors:["#7cb5ec","#f7a35c","#90ee7e","#7798BF","#aaeeee","#ff0066","#eeaaee","#55BF3B","#DF5353","#7798BF","#aaeeee"],chart:{backgroundColor:null,style:{fontFamily:"Dosis, sans-serif"}},title:{style:{fontSize:'16px',fontWeight:'bold',textTransform:'uppercase'}},tooltip:{borderWidth:0,backgroundColor:'rgba(219,219,216,0.8)',shadow:false},legend:{itemStyle:{fontWeight:'bold',fontSize:'13px'}},xAxis:{gridLineWidth:1,labels:{style:{fontSize:'12px'}}},yAxis:{minorTickInterval:'auto',title:{style:{textTransform:'uppercase'}},labels:{style:{fontSize:'12px'}}},plotOptions:{candlestick:{lineColor:'#404048'}},background2:'#F0F0EA'};Highcharts.setOptions(Highcharts.theme);

var init = false;
var monthlyJson;
var monthVal;
var yearVal;
initData();

monthVal = parseInt($('.monthpick').val()) + 1;
yearVal = $('.yearpick').val();
$('form select').addClass('form-control');

 $(document)
  .ajaxStart(function () {
    $('#loadingModal').modal({backdrop: 'static', keyboard: false});
  })
  .ajaxStop(function () {
    $('#loadingModal').modal('hide');
  });


$.get(
    ajaxAttendance,               
    {
        month: monthVal,
        year: yearVal
    }, 
    function(response){
        console.log(response.jsonAttendance);
      createCharts(response.jsonAttendance);
    },
    "json"); 
});

function initData(){
    var d = new Date();
    $('#monthpicker').monthpicker({'minYear' : 2015, 'maxYear' : 2016});
    $('.monthpick').val(d.getMonth());
    $('.yearpick').val(d.getFullYear());
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