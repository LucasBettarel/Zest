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

$.get(
  ajaxMonthly,               
  {
    month: monthVal,
    year: yearVal
  }, 
  function(response){
    createGauge(response.monthlyJson, 0, 0);
    createProd(response.monthlyJson, 0, 0);
    replaceTotalData(response.monthlyJson, 0,0);
    monthlyJson = response.monthlyJson;
    init = true;
  },
  "json");

$('#filters a').click(function(){
  if (init == true){
    $this = $(this);
    filterData($this, monthlyJson);
  }
});

$(document).on('change', '.month select', function(e){
  monthVal = parseInt($('.monthpick').val()) + 1;
  yearVal = $('.yearpick').val();
  $.get(
    ajaxMonthly,               
    {
      month: monthVal,
      year: yearVal
    }, 
    function(response){
      createGauge(response.monthlyJson, 0, 0);
      createProd(response.monthlyJson, 0, 0);
      replaceTotalData(response.monthlyJson, 0,0);
      init = true;
      monthlyJson = response.monthlyJson; 
    },
    "json");
});

});

function initData(){
    var d = new Date();
    $('#monthpicker').monthpicker({'minYear' : 2015, 'maxYear' : 2016});
    $('.monthpick').val(d.getMonth());
    $('.yearpick').val(d.getFullYear());
}

function createGauge(json, team, shift){
  var gaugeOptions = {

    chart: {
        type: 'solidgauge'
    },

    title: {
            text: 'Productivity'
        },

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
                   '<span style="font-size:12px;color:black">to line/h</span></div>'
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
            text: 'Activities Usage & Efficiency'
        },
        credits: {
            enabled: false
        },
        xAxis: {
            categories: json[team][shift]['activities']['cat']
        },
        yAxis: [{
            min: 0,
            title: {
                text: 'Hours'
            }
        }, {
            min: 0,
            title: {
                text: 'Ke (%)'
            },
            opposite: true
        }],
        legend: {
            shadow: false
        },
        tooltip: {
            shared: true
        },
        plotOptions: {
            column: {
                grouping: false,
                borderWidth: 0,
                shadow: false
            }
        },
        series: [{
            name: 'Activities Manhours',
            color: 'rgba(165,170,217,1)',
            data: json[team][shift]['activities']['data'],
            tooltip: {
                valueSuffix: ' h'
            },
            pointPadding: 0.3,
            pointPlacement: -0.2
        }, {
            name: 'Ke',
            color: 'rgba(248,161,63,1)',
            data: json[team][shift]['activities']['ke'],
            tooltip: {
                valueSuffix: ' %'
            },
            pointPadding: 0.3,
            pointPlacement: 0.2,
            yAxis: 1
        }]
    });
}

function createProd(json, team, shift){
  var optionsDaily = {
      title: {
          text: 'Daily-to-date Productivity',
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
          valueSuffix: 'to line/h'
      },
      legend: {
          layout: 'vertical',
          align: 'right',
          verticalAlign: 'middle',
          borderWidth: 0
      },
      series: [{
          name: 'HubAsia',
          data: json[0][0]['prod']
      }, {
          name: 'Outbound 4',
          data: json[1][0]['prod']
      }, {
          name: 'Inbound 4',
          data: json[2][0]['prod']
      }, {
          name: 'Outbound 3',
          data: json[4][0]['prod']
      }, {
          name: 'Inbound 3',
          data: json[5][0]['prod']
      }, {
          name : 'Adaptation',
          data: json[8][0]['prod']
      }]
  };

  $('#container-monthly').highcharts(optionsDaily);  

  var optionsTo = {
    title: {
        text: 'Daily-to-date TO-Lines',
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
        valueSuffix: 'to lines'
    },
    legend: {
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'middle',
        borderWidth: 0
    },
    series: [{
        name: 'HubAsia',
        data: json[0][0]['to']
    }, {
        name: 'Outbound 4',
        data: json[1][0]['to']
    }, {
        name: 'Inbound 4',
        data: json[2][0]['to']
    }, {
        name: 'Outbound 3',
        data: json[4][0]['to']
    }, {
        name: 'Inbound 3',
        data: json[5][0]['to']
    }, {
        name : 'Adaptation',
        data: json[8][0]['to']
    }]
  };

  $('#container-to').highcharts(optionsTo); 

  var optionsHours = {
    title: {
        text: 'Daily-to-date Working Manhours',
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
        valueSuffix: 'h'
    },
    legend: {
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'middle',
        borderWidth: 0
    },
    series: [{
        name: 'HubAsia',
        data: json[0][0]['wh']
    }, {
        name: 'Outbound 4',
        data: json[1][0]['wh']
    }, {
        name: 'Inbound 4',
        data: json[2][0]['wh']
    }, {
        name: 'Outbound 3',
        data: json[4][0]['wh']
    }, {
        name: 'Inbound 3',
        data: json[5][0]['wh']
    }, {
        name : 'Adaptation',
        data: json[8][0]['wh']
    }]
  };

  $('#container-hours').highcharts(optionsHours); 
}

function replaceTotalData(j, t, s){
  $('#report-panel #to').html(j[t][s]['report']['to']);
  $('#report-panel #ma').html(j[t][s]['report']['mh']);
  $('#report-panel #wh').html(j[t][s]['report']['wh']);
  $('#report-panel #le').html(j[t][s]['report']['le']);
  $('#report-panel #ot').html(j[t][s]['report']['ot']);
  $('#report-panel #mto').html(j[t][s]['report']['mto']);
  $('#report-panel #tr').html(j[t][s]['report']['tr']);
  $('#report-panel #ab').html(j[t][s]['report']['ab']);
}

function filterData($this, json){
  $this.siblings().removeClass('label-primary').addClass('label-default');
  $this.removeClass('label-default').addClass('label-primary');

  //update charts data
  var containerMonthly = $('#container-monthly').highcharts();
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

    loadTeamCharts(json, $this.attr('id'), 0, containerMonthly, containerProd, containerAct);
  }else{
   //shift
   var teamId = $('#filters #1').find('.label-primary').attr('id');
   loadShiftCharts(json, teamId, $this.attr('id'), containerProd, containerAct); 
  }
}

function loadTeamCharts(j, t, s, m, p, a){

  while (m.series.length > 0) {
    m.series[0].remove();
  }

  if (t == 0){
    for (var i = 0; i <= 8; i++) {
      if(typeof(j[i]) != "undefined" && j[i] !== null && i != 6) {
        m.addSeries({
          name : $('#filters .teams a#'+i).html(),
          data: j[i][0]['prod']
        });
      }
    }
  }else{
    for (var i = 0; i < j[t].length; i++) {
      if(typeof(j[t][i]) != "undefined" && j[t][i] !== null) {
        var label = ( i == 0) ? $('#filters .teams a#'+t).html() : $('#filters .shifts a#'+i).html();
        m.addSeries({
          name : label,
          data: j[t][i]['prod']
        });
      }
    }
  }

  p.series[0].setData([j[t][s]['report']['prod']]);
  a.xAxis[0].setCategories(j[t][s]['activities']['cat']);
  a.series[0].setData(j[t][s]['activities']['data']);
  a.series[1].setData(j[t][s]['activities']['ke']);
  replaceTotalData(j,t,s);
}

function loadShiftCharts(j, t, s, p, a){
  p.series[0].setData([j[t][s]['report']['prod']]);
  a.xAxis[0].setCategories(j[t][s]['activities']['cat']);
  a.series[0].setData(j[t][s]['activities']['data']);
  a.series[1].setData(j[t][s]['activities']['ke']);
  replaceTotalData(j,t,s);
}