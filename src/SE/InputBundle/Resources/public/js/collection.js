$(document).ready(function() {

   $(document).on('click', '#add[data-target]', function(e) {
      var $prototypeHolder = $('#' + $(this).attr('data-target'));

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
        addSubElement($activitiesPrototypeHolder);
        $item.find('#add').attr('data-target', $activitiesPrototypeHolder.attr('id'));

        //increment
        $prototypeHolder.attr('data-counter', Number($prototypeHolder.attr('data-counter')) + 1);
      }
      else{
        addSubElement($prototypeHolder);
      }
      
      e && e.preventDefault(); 
      return false;
    });

    $(document).on('click', '#rmv[data-target]', function(e) {
      target = $(this).attr('data-target');
      $('*[data-content="'+target+'"]').remove();
      e && e.preventDefault();
      return false;
    });

    function definePrototype($collectionHolder, level){

      if (!$collectionHolder.attr('data-counter')) {
        $collectionHolder.attr('data-counter', $collectionHolder.children().length);
      }

      var $prototype = $collectionHolder.attr('data-prototype');
      var type = (level) ? "entry_name" : "activity_name";
      var re = new RegExp(type,"g")
      var $form = $prototype.replace(re, $collectionHolder.attr('data-counter'));
       
      return $form;

     /* $collectionHolder.attr('data-counter', Number($collectionHolder.attr('data-counter')) + 1);
      
      if($collectionHolder.is("#entries-prototype")){
        $('tbody#entries').append($form);
        $item = $('tbody#entries').children().last();
        content = $collectionHolder.attr('id')+'_'+$collectionHolder.attr('data-counter');
      }else{
        $('tbody#activities').append($form);
        $item = $('tbody#activities').children().last(); 
        content = $collectionHolder.attr('id')+'_'+$collectionHolder.attr('data-counter'); 
      }

      if (!$item.attr('data-content')) {
        $item.attr('data-content', content);
        $item.find('#rmv').attr('data-target', content);
      }
      */

    }

    function addSubElement($prototypeHolder){
      var $element = definePrototype($prototypeHolder, false);
      $prototypeHolder.append($element);
      var $sub = $prototypeHolder.children().last();
      var content = $prototypeHolder.attr('id')+'_'+$prototypeHolder.attr('data-counter');
      attachData($sub, content);
      $prototypeHolder.attr('data-counter', Number($prototypeHolder.attr('data-counter')) + 1);
    }

    function attachData($item, content){
      if (!$item.attr('data-content')) {
          $item.attr('data-content', content);
        }
      $item.find('#rmv:last').attr('data-target', content);
    }

});