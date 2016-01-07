$(document).ready(function() {

  initCollection();
  $('form').preventDoubleSubmission();

/*  $(document).on('click', '#presence :checkbox', function(e) {
    $(this).closest('#presence').find('.toggling').toggleClass('hide');

    if ($(this).closest('#presence').find('.toggling').hasClass('hide')){
      //add 1st activity
      addSubElement($('#activities-prototype'));
      $(this).closest('#presence').find('.input-reason').val(0);
    }
    else{
      //rmv activities
      $('#activities-prototype').children().remove();
      $(this).closest('#presence').find('.input-reason').val(1);
    }
  });
*/
  $(document).on('click', '#add[data-target]', function(e) {
    addSubElement($('#activities-prototype'));
    e && e.preventDefault(); 
    return false;
  });

  $(document).on('click', '#rmv[data-target]', function(e) {
    target = $element.attr('data-target');
    $proto = $('*[data-content="'+target+'"]').parent();
    $('*[data-content="'+target+'"]').remove();
    preventNullActivity($proto);
    e && e.preventDefault();
    return false;
  });

  $(document).on('click', '#presence-container, presence-container *', function(e) {
    if(!$('#presence-container').attr('disabled')){presenceToggler($(this).closest("#presence-container"));}
    e && e.preventDefault();
    return false;
  });

  $(document).on('click', '#comment', function(e) {
    $(this).parent().siblings('.txtarea-sm').toggleClass('hide');
  });

  $(document).on('change', '.input-employee', function(e){
   //input sesa automatic...
   var $this =$(this)
   var id = $this.val(); 
   $.get(
    ajaxPopulate,               
    {idEmployee: id}, 
    function(response){
      if(response.code == 100 && response.success){
       $('.input-sesa').val(response.sesa);
      }
    },
    "json");    
  });

});


function initCollection(){
  if($.trim( $('#errors').html() ).length ) {
    $('#errors').parent().toggleClass('hide');
    alert('There are errors in this input, please check.');
    }
  else{
    var $sub = addSubElement($('#activities-prototype'));
    $('form').children('div').last().hide();
  }
  $("*[data-toggle='tooltip']").tooltip();
}

function addSubElement($prototypeHolder){

  var $element = definePrototype($prototypeHolder);
  $prototypeHolder.append($element);

  var $sub = $prototypeHolder.children().last();
  var content = $prototypeHolder.attr('id')+'_'+$prototypeHolder.attr('data-counter');

  if (!$sub.attr('data-content')) {
      $sub.attr('data-content', content);
    }
  $sub.find('#rmv:last').attr('data-target', content);

  $sub.find('.input-regular-hours').val(8);
  $sub.find('.input-overtime').val(0);

  $prototypeHolder.attr('data-counter', Number($prototypeHolder.attr('data-counter')) + 1);

  preventNullActivity($prototypeHolder);

  return $sub;
}

function definePrototype($collectionHolder){
  if (!$collectionHolder.attr('data-counter')) {
    $collectionHolder.attr('data-counter', $collectionHolder.children().length);
  }

  var $prototype = $collectionHolder.attr('data-prototype');
  var re = new RegExp("activity_name","g")
  var $form = $prototype.replace(re, $collectionHolder.attr('data-counter')).replace(/time_name/g, parseInt($collectionHolder.attr('data-counter'))+1);

  return $form;
}

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

function presenceToggler($this){
  var $main = $this.closest('#presence');
  var cb1 =  $main.find('.input-present');
  var cb2 =  $main.find('.input-halfday');
  var $actProto = $('#activities-prototype');

  if($this.attr('data-state') == 'Present'){
    $this.find('.presence-gauge').animate({top: "+=15"}, 500);
    cb1.val(cb1.prop('checked', false));
    $this.attr('data-state','Absent').attr('data-original-title','Absent');

    //$this.closest('td').toggleClass('expand-cell'); no need here
    $main.find('.toggling').toggleClass('hide').find('.input-reason').val(1);
    $actProto.children().remove();

  }else if($this.attr('data-state') == 'Absent'){
    $this.find('.presence-gauge').animate({top: "-=8"}, 500);
    cb1.val(cb1.prop('checked', true));
    cb2.val(cb2.prop('checked', true));
    $this.attr('data-state','Halfday').attr('data-original-title','Halfday');

    addSubElement($actProto);
    $actProto.children().last().find('.input-regular-hours').val(4);
     
  }else{
    $this.find('.presence-gauge').animate({top: "-=7"}, 500);
    cb2.val(cb2.prop('checked', false));
    $this.attr('data-state', 'Present').attr('data-original-title','Present');

    //$this.closest('td').toggleClass('expand-cell'); no need here
    $main.find('.toggling').toggleClass('hide');
    $main.find('.input-reason').val(0);
    $actProto.children().last().find('.input-regular-hours').val(8);
  
  }
}

function preventNullActivity($proto){
  //test to disable deleting the last activity from user (if not absent)
  if ($proto.children().length == 1){
    $proto.find('#rmv').attr('disabled','disabled');
  }else{
    $proto.find('#rmv').removeAttr('disabled');
  }
}
