/**
* Doc ready functionality
*/
$(document).ready(function(){
  var lang = {
    messages: {
      user: {
        deleteConfirm: "Are you sure you want to delete the selected user?"
      },
      instance: {
        deleteConfirm: "Are you sure you want to delete the selected instance?"
      }
    }
  };

  $('a[confirm-user-delete]').on('click', function(e){
    e.preventDefault();
    if(confirm(lang.messages.user.deleteConfirm)){
      window.location = $(this).attr('href');
    }
  });

  $('a[confirm-instance-delete]').on('click', function(e){
    e.preventDefault();
    if(confirm(lang.messages.instance.deleteConfirm)){
      window.location = $(this).attr('href');
    }
  });

  $('a[copy-to-clipboard-target]').on('click', function(e){
    e.preventDefault();

    $(this).attr('title', "Copied");

    $(this).tooltip({
      placement: 'right',
      trigger: 'manual',
    });

    if($(this).attr('copy-to-clipboard-target') !== ""){
      var target = $(this).attr('copy-to-clipboard-target');
      var inputDisabled = $(target).prop('disabled');

      // Enable textfield for copy if disabled
      inputDisabled ? $(target).prop('disabled', false): '';

      $(target).select();
      document.execCommand("copy");

      // Disable textfield if it was disabled
      inputDisabled ? $(target).prop('disabled', true) : '';

      // Show copied tooltip
      $(this).tooltip('show');

      var _this = this;
      setTimeout(function(){
        $(_this).tooltip('hide');
      }, 2500);
    }
  });

  function copyToClipboard(element) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(element).html()).select(); //Note the use of html() rather than text()
    document.execCommand("copy");
    $temp.remove();
  }
});
