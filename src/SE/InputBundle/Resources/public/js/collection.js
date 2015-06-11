/*$(document).ready(function() {

    $('.btn-add[data-target]').click(function(e) {
        var collectionHolder = $('#' + $(this).attr('data-target'));
 
        if (!collectionHolder.attr('data-counter')) {
            collectionHolder.attr('data-counter', collectionHolder.children().length);
        }
 
        var prototype = collectionHolder.attr('data-prototype');
        var form = prototype.replace(/__name__/g, collectionHolder.attr('data-counter'));
 
        collectionHolder.attr('data-counter', Number(collectionHolder.attr('data-counter')) + 1);
        collectionHolder.append(form);
 
        event && event.preventDefault();
    });
 
    $(document).on('click', '.btn-remove[data-related]', function(event) {
        var name = $(this).attr('data-related');
        $('*[data-content="'+name+'"]').remove();
 
        event && event.preventDefault();
    });
});
*/

$(document).ready(function() {

    $('#add').click(function(e) {
        var $collectionHolder = $('#' + $(this).attr('data-target'));

        console.log('collectionHolder', $collectionHolder);

        if (!$collectionHolder.attr('data-counter')) {
            $collectionHolder.attr('data-counter', $collectionHolder.children().length);
        }

        console.log('data-counter', $collectionHolder.attr('data-counter'));

        var $prototype = $collectionHolder.attr('data-prototype');
        var $form = $prototype.replace(/__name__/g, $collectionHolder.attr('data-counter'));
        
        $collectionHolder.attr('data-counter', Number($collectionHolder.attr('data-counter')) + 1);
        $('tbody').append($form);

        e && e.preventDefault(); 

    //    addElement($entryContainer, entryLabel, entryIndex);
    //  addSubCollection($entryContainer.children('div'), entryIndex -1);
        return false;
    });

/*

    var $entryContainer = $('div#entries-prototype');
    var entryLabel = '#';
    var entryIndex = $entryContainer.find(':input').length;
  
   // var $addEntryLink = $('<a href="#" id="add_entry" class="btn btn-default">Add an entry</a>');
   // $entryContainer.append($addEntryLink);

    $('#add_entry').click(function(e) {
      entryIndex = addElement($entryContainer, entryLabel, entryIndex);
      e.preventDefault(); 
      addSubCollection($entryContainer.children('div'), entryIndex -1);
      return false;
    });

    if (entryIndex == 0) {
        for (var i = 0; i < {{ EmployeeCount }}; i++) {
          entryIndex = addElement($entryContainer, entryLabel, entryIndex);
        } 
    } else {
      $entryContainer.children('div').each(function() {
        addDeleteLink($(this));
      });
    }

    //array de ahcontainer pour chaque entry
    var $ah = {
      container : [],
      idt : []
    };
    var ahLabel = 'Activity n*';
    var $addActivityLink = [];
 
    //on initialise les index et container de chaque ah
    $entryContainer.children('div').each(function(j) {
        addSubCollection($(this), j);
    }); 

    function addElement($container, lab, i) {
      var $prototype = $($container.attr('data-prototype').replace(/__name__label__/, lab + (i+1))
          .replace(/__name__/, i));
      addDeleteLink($prototype);
      $container.append($prototype);
      i++;
      return i;
    }

    function addDeleteLink($prototype) {
      $deleteLink = $('<p><a href=# class="btn btn-danger"><i class="glyphicon glyphicon-trash"></i></a></p>');
      $prototype.append($deleteLink);
      $deleteLink.click(function(e) {
        $prototype.remove();
        e.preventDefault();
        return false;
      });
    }

    function addSubCollection($parent, j) {  // j = parent new index $parent = $entryContainer.children('div') //.last()
      
      $ah.container.push($parent.find($("div[id*='se_inputbundle_userinput_input_entries_'][id*='_activity_hours']")).last());
      $ah.idt.push($ah.container[j].find(':input').length);

      $addActivityLink.push($('<a href=# id="add_entry" class="btn btn-success"><i class="glyphicon glyphicon-plus"></i></a>'));
      $ah.container[j].append($addActivityLink[j]);

      $addActivityLink[j].click(function(e) {
        $ah.idt[j] = addElement($ah.container[j], ahLabel, $ah.idt[j]);
        e.preventDefault(); 
        return false;
      });

      if ($ah.idt[j] == 0) {
        $ah.idt[j] = addElement($ah.container[j], ahLabel, $ah.idt[j]);
      } else {
        $ah.container[j].children('div').each(function() {
          addDeleteLink($(this));
        });
      }
    }
*/

  });