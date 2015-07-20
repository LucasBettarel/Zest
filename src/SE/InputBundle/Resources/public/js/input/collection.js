$(document).ready(function() {

    initCollection();
    $('form').preventDoubleSubmission();

    $('.input-team, .input-shift input[type=radio]').change(function(e) {
      var valueSelected = $('.input-shift input[type=radio]:checked').val();
      dynamicList(valueSelected);
    });

    $('.input-ot-time input').change(function(e) {
      var startTime = $(this).closest('.input-ot-time').find('input:first').val();
      var endTime = $(this).closest('.input-ot-time').find('input:last').val();
      var $overTime = $(this).closest('.row').find('.input-overtime input');
      displayOvertime(startTime, endTime, $overTime);
    });


   $(document).on('click', '#add[data-target]', function(e) {
      var $proto = $('#' + $(this).attr('data-target'));
      defaultValues = [0, 1,"", 0, 0, 0];
      addElement($proto, defaultValues);
      e && e.preventDefault(); 
      return false;
    });

    $(document).on('click', '#rmv[data-target]', function(e) {
      removeElement($(this));
      e && e.preventDefault();
      return false;
    });

    $(document).on('click', '#presence :checkbox', function(e) {
      $(this).closest('#presence').find('.toggling').toggleClass('hide');
      $(this).closest('td').toggleClass('expand-cell');
      $(this).closest('#presence').find('.input-reason').val(0);
      var $actProto = $(this).closest('td').siblings('#activities').find('div:first');
      if ($(this).closest('#presence').find('.toggling').hasClass('hide')){
        //add 1st activity
        addSubElement($actProto);
      }
      else{
        //rmv activities
        $actProto.children().remove();
      }
    });

    function addElement($prototypeHolder, values){
      if($prototypeHolder.is("#entries-prototype")){
        var $element = definePrototype($prototypeHolder, true);
        $('tbody#entries').append($element);
        var $item = $('tbody#entries').children().last();

        //attach data-target on rmv btn and remove btn on item appended
        var content = $prototypeHolder.attr('id')+'_'+$prototypeHolder.attr('data-counter');
        attachData($item, content);

        //get activity prototype
        var $activitiesPrototypeHolder = $item.find('#activities-prototype');
        $activitiesPrototypeHolder.attr('id',content+'_activities-prototype');

        //add first activity
        var $sub = addSubElement($activitiesPrototypeHolder);
        $item.find('#add').attr('data-target', $activitiesPrototypeHolder.attr('id'));

        //increment
        $prototypeHolder.attr('data-counter', Number($prototypeHolder.attr('data-counter')) + 1);

        $item.find('.input-employee select').val(values[0]);
        $item.find('.input-sesa input').val(values[2]);
        $item.find('.input-reason').val(0);
        $item.find("*[data-toggle='tooltip']").tooltip();
      }
      else{
        var $sub = addSubElement($prototypeHolder);
        $sub.find("*[data-toggle='tooltip']").tooltip();
      }
       //set default values
       $sub.find('.input-activity select').val(values[5]);
    }

    function removeElement($element){
      target = $element.attr('data-target');
      $('*[data-content="'+target+'"]').remove();
    }

    function definePrototype($collectionHolder, level){
      if (!$collectionHolder.attr('data-counter')) {
        $collectionHolder.attr('data-counter', $collectionHolder.children().length);
      }

      var $prototype = $collectionHolder.attr('data-prototype');
      var type = (level) ? "entry_name" : "activity_name";
      var re = new RegExp(type,"g")
      var $form = $prototype.replace(re, $collectionHolder.attr('data-counter')).replace(/time_name/g, parseInt($collectionHolder.attr('data-counter'))+1);

      return $form;

    }

    function addSubElement($prototypeHolder){
      var $element = definePrototype($prototypeHolder, false);
      $prototypeHolder.append($element);
      var $sub = $prototypeHolder.children().last();
      var content = $prototypeHolder.attr('id')+'_'+$prototypeHolder.attr('data-counter');
      attachData($sub, content);
      var parentContent = $prototypeHolder.closest('tr').attr('data-content');
      var $transfer = $sub.find('.transfer').attr({
        'data-sub-target': $prototypeHolder.attr('id'),
        'data-target': parentContent,
        'data-disabled': 0});
      $sub.find('.input-regular-hours input').val(8);
      $sub.find('.input-overtime input').val(0);
      //$sub.find('.input-zone select').val(0);
      $prototypeHolder.attr('data-counter', Number($prototypeHolder.attr('data-counter')) + 1);

      return $sub;
    }

    function attachData($item, content){
      if (!$item.attr('data-content')) {
          $item.attr('data-content', content);
        }
      $item.find('#rmv:last').attr('data-target', content);
    }

    function dynamicList(valueSelect){
    //delete all current field
    $('#entries').children().remove();

    //reset data counter
    $('#entries-prototype').attr('data-counter', 0);

    //add good nb fields
    for(var i = 0; i < employeesData.length; ++i){
          if(employeesData[i][3] == $('.input-team').val() && employeesData[i][4] == valueSelect){
            addElement($('#entries-prototype'), employeesData[i]);
          }
      }
    }

    function initCollection(){
      $('.input-ot-time input').val('00:00');
      $('.input-shift').val(1);
      $(".input-shift input[value=1]").attr('checked', 'checked');
      for(var i = 0; i < employeesData.length; ++i){
          if(employeesData[i][3] == $('.input-team').val() && employeesData[i][4] == $('.input-shift').val()){
            addElement($('#entries-prototype'), employeesData[i]);
          }
      }
      $('.clockpicker').clockpicker();  
      $('form').children('div').last().hide();
      $("*[data-toggle='tooltip']").tooltip();
    }

    function displayOvertime(start, end, $overt){
      end = end.split(/:/);
      start = start.split(/:/);
      var diff = Math.round((end[0] * 3600 + end[1] * 60 + 43200 - (start[0] * 3600 + start[1] * 60 + 43200))/36)/100;
      if (diff<0){
        diff+=24;
      }
      $overt.val(diff);
    }

});

jQuery.fn.preventDoubleSubmission = function() {
  $(this).on('submit',function(e){
    var $form = $(this);

    if ($form.data('submitted') === true) {
      // Previously submitted - don't submit again
      e.preventDefault();
    } else {
      // Mark it so that the next submit can be ignored
      $form.data('submitted', true);
    }
  });

  // Keep chainability
  return this;
};