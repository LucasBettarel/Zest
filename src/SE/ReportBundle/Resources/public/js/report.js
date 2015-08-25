$(document).ready(function() {

// Load the fonts
Highcharts.createElement('link', {
   href: '//fonts.googleapis.com/css?family=Dosis:400,600',
   rel: 'stylesheet',
   type: 'text/css'
}, null, document.getElementsByTagName('head')[0]);

Highcharts.theme = {
   colors: ["#7cb5ec", "#f7a35c", "#90ee7e", "#7798BF", "#aaeeee", "#ff0066", "#eeaaee",
      "#55BF3B", "#DF5353", "#7798BF", "#aaeeee"],
   chart: {
      backgroundColor: null,
      style: {
         fontFamily: "Dosis, sans-serif"
      }
   },
   title: {
      style: {
         fontSize: '16px',
         fontWeight: 'bold',
         textTransform: 'uppercase'
      }
   },
   tooltip: {
      borderWidth: 0,
      backgroundColor: 'rgba(219,219,216,0.8)',
      shadow: false
   },
   legend: {
      itemStyle: {
         fontWeight: 'bold',
         fontSize: '13px'
      }
   },
   xAxis: {
      gridLineWidth: 1,
      labels: {
         style: {
            fontSize: '12px'
         }
      }
   },
   yAxis: {
      minorTickInterval: 'auto',
      title: {
         style: {
            textTransform: 'uppercase'
         }
      },
      labels: {
         style: {
            fontSize: '12px'
         }
      }
   },
   plotOptions: {
      candlestick: {
         lineColor: '#404048'
      }
   },


   // General
   background2: '#F0F0EA'
   
};

// Apply the theme
Highcharts.setOptions(Highcharts.theme);

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
            data: [jsonHub[jsonHub.length-1]],
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

   var optionsDaily = {
        title: {
            text: 'Daily-to-date',
            x: -20 //center
        },
        xAxis: {
            categories: jsonCategories,
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
            data: jsonHub
        }, {
            name: 'Outbound 4',
            data: jsonOut4
        }, {
            name: 'Inbound 4',
            data: jsonIn4
        }, {
            name: 'Outbound 3',
            data: jsonOut3
        }, {
            name: 'Inbound 3',
            data: jsonIn3
        }]
    };

  $('#container-daily').highcharts(optionsDaily);  
  replaceTotalData('hub'); 
  var yesterdayDate = new Date();
  yesterdayDate.setDate(yesterdayDate.getDate() - 1);
  $('#yesterday').append(yesterdayDate.toDateString());
   
	$('#filters a').click(function(){
  	$(this).siblings().removeClass('label-primary').addClass('label-default');
  	$(this).removeClass('label-default').addClass('label-primary');

    //update charts data
    var containerDaily = $('#container-daily').highcharts();
    var containerProd = $('#container-prod').highcharts();

    if ($(this).parent().hasClass('teams')){
      if($(this).attr('id') == "allT" || $(this).attr('id') == "Outbound3" || $(this).attr('id') == "Inbound3"){
        if(!$('#filters .shifts').hasClass('hide')){
          $('#filters .shifts').addClass('hide');
        }
      }
      else if ($('#filters .shifts').hasClass('hide')){
        $('#filters .shifts').removeClass('hide');
      }
      $('#filters .shifts a').removeClass('label-primary').addClass('label-default');
      $('#filters .shifts').find('#allS ').removeClass('label-default').addClass('label-primary');

      
      if($(this).attr('id') == "allT"){
        while (containerDaily.series.length > 0) {
              containerDaily.series[0].remove();
        }

        containerDaily.addSeries({
            name: 'HubAsia',
            data: jsonHub
        });
        containerDaily.addSeries({
            name : 'Outbound 4',
            data: jsonOut4
        });
        containerDaily.addSeries({
            name : 'Inbound 4',
            data: jsonIn4
        });
        containerDaily.addSeries({
            name : 'Outbound 3',
            data: jsonOut3
        });
        containerDaily.addSeries({
            name : 'Inbound 3',
            data: jsonIn3
        });

        containerProd.series[0].setData([jsonHub[jsonHub.length-1]]);
        replaceTotalData('hub');

      }else if($(this).attr('id') == "Outbound4"){
        while (containerDaily.series.length > 0) {
              containerDaily.series[0].remove();
        }

        containerDaily.addSeries({
            name : 'Outbound 4',
            data: jsonOut4
        });
        containerDaily.addSeries({
            name : 'Shift 1',
            data: jsonOut4s1
        });
        containerDaily.addSeries({
            name : 'Shift 2',
            data: jsonOut4s2
        });
        containerDaily.addSeries({
            name : 'Shift 3',
            data: jsonOut4s3
        });

        containerProd.series[0].setData([jsonOut4[jsonOut4.length-1]]);
        replaceTotalData('out4');

      }else if($(this).attr('id') == "Inbound4"){
        while (containerDaily.series.length > 0) {
              containerDaily.series[0].remove();
        }

        containerDaily.addSeries({
            name : 'Inbound 4',
            data: jsonIn4
        });
        containerDaily.addSeries({
            name : 'Shift 1',
            data: jsonIn4s1
        });
        containerDaily.addSeries({
            name : 'Shift 2',
            data: jsonIn4s2
        });
        containerDaily.addSeries({
            name : 'Shift 3',
            data: jsonIn4s3
        });

        containerProd.series[0].setData([jsonIn4[jsonIn4.length-1]]);
        replaceTotalData('in4');

      }else if($(this).attr('id') == "Outbound3"){
        while (containerDaily.series.length > 0) {
              containerDaily.series[0].remove();
        }

        containerDaily.addSeries({
            name : 'Outbound 3',
            data: jsonOut3
        });
        containerProd.series[0].setData([jsonOut3[jsonOut3.length-1]]);
        replaceTotalData('out3');

      }else if($(this).attr('id') == "Inbound3"){
        while (containerDaily.series.length > 0) {
              containerDaily.series[0].remove();
        }

        containerDaily.addSeries({
            name : 'Inbound 3',
            data: jsonIn3
        });
        containerProd.series[0].setData([jsonIn3[jsonIn3.length-1]]);
        replaceTotalData('in3');

      }
    }
    else{
      //shift filters
      if($('#filters a#Outbound4').hasClass('label-primary')){
        if($(this).attr('id') == "Shift1"){
          containerProd.series[0].setData([jsonOut4s1[jsonOut4s1.length-1]]);
          replaceTotalData('out4s1');
        }else if($(this).attr('id') == "Shift2"){
          containerProd.series[0].setData([jsonOut4s2[jsonOut4s2.length-1]]);
          replaceTotalData('out4s2');
        }else if($(this).attr('id') == "Shift3"){
          containerProd.series[0].setData([jsonOut4s3[jsonOut4s3.length-1]]);
          replaceTotalData('out4s3');
        }else{
          containerProd.series[0].setData([jsonOut4[jsonOut4.length-1]]);
          replaceTotalData('out4');
        }
      }
      else{
        if($(this).attr('id') == "Shift1"){
          containerProd.series[0].setData([jsonIn4s1[jsonIn4s1.length-1]]);
          replaceTotalData('in4s1');
        }else if($(this).attr('id') == "Shift2"){
          containerProd.series[0].setData([jsonIn4s2[jsonIn4s2.length-1]]);
          replaceTotalData('in4s2');
        }else if($(this).attr('id') == "Shift3"){
          containerProd.series[0].setData([jsonIn4s3[jsonIn4s3.length-1]]);
          replaceTotalData('in4s3');
        }else{
          containerProd.series[0].setData([jsonIn4[jsonIn4.length-1]]);
          replaceTotalData('in4');
        }
      }
    }
  }); 

});

function replaceTotalData(team){
    $('#daily-panel #m-to').html(jsonTotalData[team][1]['to']);
    $('#daily-panel #m-ma').html(jsonTotalData[team][1]['mh']);
    $('#daily-panel #m-wh').html(jsonTotalData[team][1]['wh']);
    $('#daily-panel #m-hc').html(jsonTotalData[team][1]['hc']);
    $('#daily-panel #m-ot').html(jsonTotalData[team][1]['ot']);
    $('#daily-panel #m-tr').html(jsonTotalData[team][1]['tr']);
    $('#daily-panel #m-ab').html(jsonTotalData[team][1]['ab']);

    if(jsonTotalData[team][1]['wh'] > 0){
      var prod = Math.round( (jsonTotalData[team][1]['to'] / jsonTotalData[team][1]['wh'] ) * 100) / 100;
      $('#daily-panel #m-prod').html(prod+" to lines/h");
    } else {
      $('#daily-panel #m-prod').html('0 to lines/h');
    }

    $('#yesterday-panel #y-to').html(jsonTotalData[team][0]['to']);
    $('#yesterday-panel #y-ma').html(jsonTotalData[team][0]['mh']);
    $('#yesterday-panel #y-wh').html(jsonTotalData[team][0]['wh']);
    $('#yesterday-panel #y-hc').html(jsonTotalData[team][0]['hc']);
    $('#yesterday-panel #y-ot').html(jsonTotalData[team][0]['ot']);
    $('#yesterday-panel #y-tr').html(jsonTotalData[team][0]['tr']);
    $('#yesterday-panel #y-ab').html(jsonTotalData[team][0]['ab']);
  }