$(document).ready(function() {

  Highcharts.createElement('link',{href:'//fonts.googleapis.com/css?family=Dosis:400,600',rel:'stylesheet',type:'text/css'},null,document.getElementsByTagName('head')[0]);Highcharts.theme={colors:["#7cb5ec","#f7a35c","#90ee7e","#7798BF","#aaeeee","#ff0066","#eeaaee","#55BF3B","#DF5353","#7798BF","#aaeeee"],chart:{backgroundColor:null,style:{fontFamily:"Dosis, sans-serif"}},title:{style:{fontSize:'16px',fontWeight:'bold',textTransform:'uppercase'}},tooltip:{borderWidth:0,backgroundColor:'rgba(219,219,216,0.8)',shadow:false},legend:{itemStyle:{fontWeight:'bold',fontSize:'13px'}},xAxis:{gridLineWidth:1,labels:{style:{fontSize:'12px'}}},yAxis:{minorTickInterval:'auto',title:{style:{textTransform:'uppercase'}},labels:{style:{fontSize:'12px'}}},plotOptions:{candlestick:{lineColor:'#404048'}},background2:'#F0F0EA'};Highcharts.setOptions(Highcharts.theme);

  var init = false;
  var dailyJson;
  var dateVal = $('#dailyDate').val();
  var inputTable;

  $.post(
    ajaxDaily,               
    {date: dateVal}, 
    function(response){
      createGauge(response.dailyJson, 0, 0);
      replaceTotalData(response.dailyJson, 0,0);
      dailyJson = response.dailyJson;
      $('.panel-default .dataTables_scrollHeadInner').css('padding-left','0px');
      $('.panel-default .table').css('margin-bottom','0px');
      $("*[data-toggle='tooltip']").tooltip();
      inputTable = $('#inputs').DataTable( {
        retrieve: true,
        "scrollY": "173px",
        paging: false,
        "dom": 'lrtip',
        "info": false
      });
      inputTable.rows.add(response.template).draw();
      init = true;
    },
    "json");

  $('#filters a').click(function(){
    if (init == true){
      $this = $(this);
      filterData($this, dailyJson);
    }
  });

  $(document).on('change', 'form input#dailyDate', function(e){
    dateVal = $('#dailyDate').val();
    init = false;
    $.post(
    ajaxDaily,               
    {date: dateVal}, 
    function(response){
      createGauge(response.dailyJson, 0, 0);
      replaceTotalData(response.dailyJson, 0,0);
      dailyJson = response.dailyJson;
      inputTable = $('#inputs').DataTable();
      inputTable.clear();
      inputTable.rows.add(response.template).draw();
      inputTable.draw();
      init = true;
    },
    "json");
  });
});

function createGauge(json, team, shift){
  var gaugeOptions = {

    chart: {
        type: 'solidgauge'
    },

    title: null,

    pane: {
        center: ['50%', '50%'],
        size: '100%',
        startAngle: 0,
        endAngle: 360,
        background: {
            backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || '#EEE',
            innerRadius: '60%',
            outerRadius: '100%',
            shape: 'arc'
        }
    },

    tooltip: {
        enabled: false
    },

    // the value axis
    yAxis: {
        stops: [
            [0.1, '#DF5353'], // red
            [0.5, '#DDDF0D'], // yellow
            [0.9, '#55BF3B'] // green
        ],
        lineWidth: 0,
        minorTickInterval: null,
        tickPixelInterval: 400,
        tickWidth: 0,
        title: {
            y: 140
        },
        labels: {
            y: 16
        }
    },

    plotOptions: {
        solidgauge: {
            dataLabels: {
                y: 5,
                borderWidth: 0,
                useHTML: true
            }
        }
    }
  };
  // The speed gauge
$('#container-prod').highcharts(Highcharts.merge(gaugeOptions, {
    yAxis: {
        min: 0,
        max: 10,
        title: {
            text: ''
        }
    },

    credits: {
        enabled: false
    },

    series: [{
        name: 'Productivity',
        data: [json[team][shift]['report']['prod']],
        dataLabels: {
            format: '<div style="text-align:center; margin-top:-70px;"><span style="font-size:90px;color:' +
                ((Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black') + '">{y}</span><br/>' +
                   '<span style="font-size:12px;color:silver">to line/h</span></div>'
        },
        tooltip: {
            valueSuffix: 'to line/h'
        }
    }]
}));

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
            categories: json[team][shift]['activities']['cat'],
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
            name: 'Activities Manhours',
            data: json[team][shift]['activities']['data']
        }]
    });
}

function replaceTotalData(j, t, s){
  $('#report-panel #to').html(j[t][s]['report']['to']);
  $('#report-panel #ma').html(j[t][s]['report']['mh']);
  $('#report-panel #wh').html(j[t][s]['report']['wh']);
  $('#report-panel #hc').html(j[t][s]['report']['hc']);
  $('#report-panel #ot').html(j[t][s]['report']['ot']);
  $('#report-panel #mto').html(j[t][s]['report']['mto']);
  $('#report-panel #tr').html(j[t][s]['report']['tr']);
  $('#report-panel #ab').html(j[t][s]['report']['ab']);
}

function filterData($this, json){
  $this.siblings().removeClass('label-primary').addClass('label-default');
  $this.removeClass('label-default').addClass('label-primary');
    
  //update charts data
  var containerProd = $('#container-prod').highcharts();
  var containerAct = $('#container-activities').highcharts();

  if ($this.parent().attr('id') == 1){//team
    if($this.attr('id') == 0 || $this.attr('id') == 4 || $this.attr('id') == 5){
      if(!$('#filters .shifts').hasClass('hide')){
        $('#filters .shifts').addClass('hide');
      }
    }else if ($this.attr('id') == 8 || $this.attr('id') == 9 || $this.attr('id') == 6){
     $('#filters .shifts').removeClass('hide');
     if(!$('#filters .shifts #3').hasClass('hide')){
        $('#filters .shifts #3').addClass('hide');
      } 
    }else{
      $('#filters .shifts').removeClass('hide');
      $('#filters .shifts #3').removeClass('hide');
    }
    $('#filters .shifts a').removeClass('label-primary').addClass('label-default');
    $('#filters .shifts #0').removeClass('label-default').addClass('label-primary');

    loadCharts(json, $this.attr('id'), 0, containerProd, containerAct);

    if($this.attr('id') != 0){
      $('#inputs').DataTable().column(0).search($this.text()).draw();  
    }else{
      $('#inputs').DataTable().column(0).search("").draw();  
    }
    //if filter by team, reset filter shift to all
    $('#inputs').DataTable().column(1).search("").draw(); 
  }else{
   //shift
   var teamId = $('#filters #1').find('.label-primary').attr('id');
   loadCharts(json, teamId, $this.attr('id'), containerProd, containerAct);
   $('#inputs').DataTable().column(1).search($this.attr('id')).draw();
  }
}

function loadCharts(j, t, s, p, a){
  p.series[0].setData([j[t][s]['report']['prod']]);
  a.xAxis[0].setCategories(j[t][s]['activities']['cat']);
  a.series[0].setData(j[t][s]['activities']['data']);
  replaceTotalData(j,t,s);
}
