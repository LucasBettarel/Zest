$(document).ready(function() {

var id;

    $(document).on('click', '#confirmDelete', function(e){
      e && e.preventDefault();
      deleteImport(id);
    });

    $(document).on('click', '#deleteImport', function(e){
      e && e.preventDefault();
      id = $(this).attr('data-id');
    });

});

function deleteImport(id){
    $.post(
      ajaxDeleteImport,               
      {idImport: id}, 
      function(response){
        console.log(response);
/*        if(response.code == 100 && response.success){
          console.log('import found, deleted', $('#imports [data-id="'+id+'"]').closest('tr'));
          $('#imports [data-id="'+id+'"]').closest('tr').remove();
        }
        else{
            alert('Sorry, a strange error occurred... Please try again or contact Lucas !');
        }*/
      },
      "json");    
}