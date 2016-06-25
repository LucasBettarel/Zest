$(document).ready(function() {

var id;
var modal = $('.modal');

  $(document).on('click', '#confirmDelete', function(e){
    e && e.preventDefault();
    modal.find('button').addClass('disabled');
    $(this).text('Wait...')
    deleteImport(id);
  });

  $(document).on('click', '#deleteImport', function(e){
    e && e.preventDefault();
    id = $(this).attr('data-id');
  });

  $('#deleteModal').on('shown.bs.modal', function (e) {
    e && e.preventDefault();
    modal.find('.modal-title').html("<i class='glyphicon glyphicon-alert'> </i> Warning!");
    modal.find('.modal-body').html('This function must be used only when an undesired behavior created a duplicate record !<br>It will reset the productivity associated and delete the TO Lines imported.');
    modal.find('#confirmDelete').text('Delete import').show();
    modal.find('.modal-footer button:first').text('Cancel');
  })

});

function deleteImport(id){
    $.post(
      ajaxDeleteImport,               
      {idImport: id}, 
      function(response){
        if(response.code == 100 && response.success){
          $('.modal .modal-title').text('Import deleted !');
          $('.modal .modal-body').text(response.comment);
          $('#imports [data-id="'+id+'"]').closest('tr').remove();
        }
        else{
          $('.modal .modal-title').text('Sorry, a strange error occured...');
          $('.modal .modal-body').text(response.comment);
        }
        $('.modal #confirmDelete').hide();
        $('.modal-footer button:first').text('Close');
        $('.modal button').removeClass('disabled');
      },
      "json");    
}
