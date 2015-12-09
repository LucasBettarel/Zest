$(document).ready(function() {

  initCollection();
  $('form').preventDoubleSubmission();

  $(document).on('click', '#presence :checkbox', function(e) {
    $(this).closest('#presence').find('.toggling').toggleClass('hide');
   // $(this).closest('td').toggleClass('expand-cell');
    var $actProto = $(this).closest('td').siblings('#activities').find('div:first');
    if ($(this).closest('#presence').find('.toggling').hasClass('hide')){
      //add 1st activity
      addSubElement($actProto);
      $(this).closest('#presence').find('.input-reason').val(0);
    }
    else{
      //rmv activities
      $actProto.children().remove();
      $(this).closest('#presence').find('.input-reason').val(1);
    }
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
  attachData($sub, content);

//  var parentContent = $prototypeHolder.closest('tr').attr('data-content');
//  var $transfer = $sub.find('.transfer').attr({
//    'data-sub-target': $prototypeHolder.attr('id'),
//    'data-target': parentContent,
//    'data-disabled': 0});
  $sub.find('.input-regular-hours input').val(8);
  $sub.find('.input-overtime input').val(0);

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

function attachData($item, content){
  if (!$item.attr('data-content')) {
      $item.attr('data-content', content);
    }
  $item.find('#rmv:last').attr('data-target', content);
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