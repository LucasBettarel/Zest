$(document).ready(function() {

   $(document).on('click', '#add[data-target]', function(e) {
        var $collectionHolder = $('#' + $(this).attr('data-target'));

        if (!$collectionHolder.attr('data-counter')) {
            $collectionHolder.attr('data-counter', $collectionHolder.children().length);
        }

        var $prototype = $collectionHolder.attr('data-prototype');
        var type = ($collectionHolder.is("#entries-prototype")) ? "input_entries_" : "activity_hours_";
        var re = new RegExp(type + '__name__',"g")
        var $form = $prototype.replace(re, $collectionHolder.attr('data-counter'));
        console.log('replace',re);
        
        $collectionHolder.attr('data-counter', Number($collectionHolder.attr('data-counter')) + 1);
        content = $collectionHolder.attr('id')+'_'+$collectionHolder.attr('data-counter');

        if($collectionHolder.is("#entries-prototype")){
          $('tbody#entries').append($form);
          $item = $('tbody#entries').children().last();
        }else{
          $('tbody#activities').append($form);
          $item = $('tbody#activities').children().last();  
        }

        if (!$item.attr('data-content')) {
          $item.attr('data-content', content);
          $item.find('#rmv').attr('data-target', content);
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

  });