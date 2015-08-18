$(document).ready(function() {

initData();

$.get(
    ajaxAttendance,               
    {
        month: $('.monthpick').val(),
        year: $('.yearpick').val()
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