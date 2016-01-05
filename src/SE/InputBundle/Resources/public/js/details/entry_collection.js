$(document).ready(function() {

  initCollection();
  $('form').preventDoubleSubmission();

  $(document).on('click', '#presence :checkbox', function(e) {
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

  $(document).on('click', '#add[data-target]', function(e) {
    addSubElement($('#activities-prototype'));
    e && e.preventDefault(); 
    return false;
  });

  $(document).on('click', '#rmv[data-target]', function(e) {
      target = $(this).attr('data-target');
      $('*[data-content="'+target+'"]').remove();
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
