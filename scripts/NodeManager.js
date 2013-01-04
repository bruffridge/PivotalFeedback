  var NodeManager = function() {

    this.removeErrors = function() {
      //remove all inError classes from formItems, set html of .err to blank
      $('.formItem').removeClass('inError');
      $('.err').html('&nbsp;');
    }

    this.addError = function(context, fieldName, errorMsg, setFocus) {
      var $formField = $(context + ' [name="' + fieldName + '"]').filter(':first');
      var fi = $formField.parents('.formItem').filter(':first');
      fi.addClass("inError");
      var msgElement = fi.find('.err').filter(':first');
      msgElement.html(errorMsg);
      $formField.attr('aria-invalid', 'true');
      $formField.attr('aria-describedby', msgElement.attr('id'));
      if(setFocus) {
        $formField.focus();
      }
    }
 
    return this;
  }