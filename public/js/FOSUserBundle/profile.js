    $('button[id^="btn-update-"]').on("click",function(e){
      e.preventDefault();
      console.log($(this));
      var ProfileUserId= $(this).data('profile');
      var field = $(this).data('field');
      var inputValue = $('#form-'+field+ ' :input').val();
     console.log( Routing.generate('edit_profile_user', { id: ProfileUserId, field: field,inputValue : inputValue }) );
    $.ajax({
      url: Routing.generate('edit_profile_user', { id: ProfileUserId }) ,
      type: "post",
      data : {"field": field,"inputValue" : inputValue},
      success: function(data, textStatus, jqXHR) {
        let nouvelValeurInput= data.nouvelValeurInput;
        $('span#show-'+field).text(nouvelValeurInput);
      },
      dataType : 'json',
      error: function(jqXHR, textStatus, errorThrown) {
        console.log(JSON.stringify(jqXHR));
        console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
      },

      complete : function(resultat, statut){
          $('.modal#modal-'+field).modal('toggle');
      }
    });
    });
  
