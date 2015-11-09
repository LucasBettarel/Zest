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
    function(r){
        attJson = r.template;
        createTable(attJson, r.headers, r.daysNb);
        createCharts(r.jsonData);
    },
    "json"); 

$('#filters a').click(function(){
  $this = $(this);
  filterData($this, attJson);
});

$('.close').click(function(){$('.table-panel').toggleClass('col-md-12 col-md-9').siblings('.entry-panel').addClass('hide');});

$(document).on('change', '.month select', function(e){
  monthVal = parseInt($('.monthpick').val()) + 1;
  yearVal = $('.yearpick').val();
  $.get(
    ajaxAttendance,               
    {
      month: monthVal,
      year: yearVal
    }, 
    function(r){
      $('#filters .teams').html(r.filters);//update filters
      attJson = r.template; 
      $('#filters a').click(function(){
        $this = $(this);
        filterData($this, attJson);
      });
        var attendanceTable = $('#attendance').DataTable();
        attendanceTable.clear();
        attendanceTable.destroy();
        $('#attendance thead .day-h').remove();
        createTable(attJson, r.headers, r.daysNb);
        createCharts(r.jsonData);
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

function createCharts(json){

  $('#summary-panel #mar').html(json['report']['attrate']);
  $('#summary-panel #hr').html(json['report']['totalhr']);
  $('#summary-panel #ot').html(json['report']['totalothr']);
  $('#summary-panel #otn').html(json['report']['wdot']);
  $('#summary-panel #phot').html(json['report']['weot']);
      
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

    if($this.attr('id') != 0){
      $('#attendance').DataTable().column(1).search($this.text()).draw();  
    }else{
      $('#attendance').DataTable().column(1).search("").draw();  
    }
    //if filter by team, reset filter shift to all
    $('#attendance').DataTable().column(2).search("").draw(); 
  }else{
   //shift
   if($this.attr('id') != 0){
      $('#attendance').DataTable().column(2).search($this.attr('id')).draw();  
    }else{
      $('#attendance').DataTable().column(2).search("").draw();  
    }
  }
}

function createTable(j, h, n){

    for (var i=1; i <= n; i++) {
        if(typeof(h[i]) != "undefined" && h[i] !== null) {
            $("<th id='"+i+"' class='day-h'>"+h[i][0]['id']+"</th>").insertBefore('#total');
            if( h[i][0]['hd'] ){$('#attendance thead #'+i).addClass('holiday');}
            else if( !h[i][0]['wd'] ){$('#attendance thead #'+i).addClass('weekend');}
        }
    };

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
    attendanceTable.rows.add(j).draw();

    $("*[data-toggle='tooltip']").tooltip({html :'true',container: 'body'});
    $('.e-day-ok').closest('td').addClass('green box');
    $('.e-day-low').closest('td').addClass('yellow box');
    $('.e-day-high').closest('td').addClass('orange box');
    $('.e-day-absent').closest('td').addClass('grey box');
    $('.e-day-leave').closest('td').addClass('blue box');
    $('#attendance .box').not('.grey').append("<div class='overlay'><i class='glyphicon glyphicon-info-sign'></i></div>");

    $('#attendance .box').not('.grey').click(function(){
          $this = $(this);
          if($('.table-panel').hasClass('col-md-12')){
              $('.table-panel').toggleClass('col-md-12 col-md-9').siblings('.entry-panel').removeClass('hide');
          }
          displayDetails($this);
    });
}

function displayDetails($this){
    $.get(
        ajaxAttDetails,               
    {
      y: $('.yearpick').val(),
      m: parseInt($('.monthpick').val()) + 1,
      d: $this.find('div:first').attr('data-d'),
      e: $this.find('div:first').attr('data-e')
    }, 
    function(r){
      $('#e-name').html(r.det['name']);
      $('#e-date').html(r.det['date']);
      $('#e-tot').html(r.det['tothr']);
      $('#e-mh').html(r.det['totreg']);
      $('#e-ot').html(r.det['totot']);
      $('#e-res').html(r.det['res']);

      var $detTable = $('#input-det-table');
      $('.det-content').find('.clone').remove();
      $('.entry-panel .alert').addClass('hide');

      if(r.det['tab']){
          for (var i = 0; i < r.det['tab'].length; i++) {
            $detTable.find('#inject-employee').children().remove();
            $detTable.find('#input-det').html(r.det['tab'][i]['header']);
            $detTable.find('th a').attr('href', r.det['tab'][i]['link'])
            if( !r.det['tab'][i]['present']){$detTable.find('#inject-employee').html("<tr><td>Absent : "+r.det['tab'][i]['absence']+"</td></tr>");}
            else if(r.det['tab'][i]['row']){
                for (var j = 0; j < r.det['tab'][i]['row'].length; j++) {
                    $detTable.find('#inject-employee').append(r.det['tab'][i]['row'][j]);
                };
            }
            $('.det-content').append($detTable);
            var $detTable = $detTable.clone().addClass('clone');
          };
      }

      if ($this.hasClass('yellow')){
        $('.entry-panel .alert-danger').removeClass('hide');
        $('.entry-panel #error-msg').html("The hours recorded are too low!")
      }else if ($this.hasClass('orange')){
        $('.entry-panel .alert-danger').removeClass('hide');
        $('.entry-panel #error-msg').html("The hours recorded are too high!")
      }else{
        $('.entry-panel .alert-success').removeClass('hide');
      }
      
    },
    "json");
}
