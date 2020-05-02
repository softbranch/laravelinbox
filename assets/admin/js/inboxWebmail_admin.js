jQuery(document).ready(function ($) {
    "use strict";
    $('#mobile_left').on('click', ".toggle-email-nav", function(e) {
     $("#email-nav").toggle();
    });

    $('#ac_frm').on('click', "#delete_server", function(e) {
        if($(this).is(":checked")) {
            $("#show_fold").hide(300);
        } else {
            $("#show_fold").show(200);
        }
    });

  $('#inboxWebmail_table_lbl').on('click', "#add_lbl", function(e) {
      $('tbody').append('<tr class="add_row"><td><input type="text" class="form-control" name="lbl_name[][]" value="" required="required" /></td><td><input type="text" class="form-control" name="lbl_code[][]" value="" required="required" /></td><td class="text-center"><button type="button" class="badge badge-danger delc" id="delete_lbl" title="Delete label">X</button></td><tr>');
      e.preventDefault();
  });

  // Delete row
  $('#inboxWebmail_table_lbl').on('click', "#delete_lbl", function(e) {
      if (!confirm("Are you sure you want to delete this label?"))
          return false;
      $(this).closest('tr').remove();
      e.preventDefault();
  });

$('#email-nav').on('click', "#refresh_data", function(e) {
        	$.ajax({
                  type: "GET",
                  url: $("#inlbl_refresh_url").val(),
                  data: 1,
                  cache: false,
                  success: function(data){
                     location.reload();
                  }
        });
	 	return false;
	});

	$('#inboxWebmail_table_file').on('click', "#add_file", function(e) {
	$('tbody').append('<tr class="add_row"><td><button type="button" class="badge badge-danger delc" id="delete_file" title="">X</button></td><td><input style="border:0px;" name="file[]" type="file" multiple /></td><td></td><tr>');
	e.preventDefault();
   });

   // Delete row
   $('#inboxWebmail_table_file').on('click', "#delete_file", function(e) {
	if (!confirm("Are you sure you want to delete this file?"))
	return false;
	$(this).closest('tr').remove();
	e.preventDefault();
   });



    	$('#frm_inbox').on('click', "#mc0", function(e) {
                $(".mycls").prop('checked', $(this).prop('checked'));

		});


});