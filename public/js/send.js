// Send email with recaptcha
$(function(){
  $("#sendForm").click(function(){
    var name = $("input#name").val();
    var email = $("input#email").val();
    var message = $("textarea#message").val();
    var response = grecaptcha.getResponse();

    $.ajax({
        url: "/send",
        type: "POST",
        data: {
            name: name,
            email: email,
            message: message,
            recaptcha: response
        },
        cache: false,
        success: function() {
            // Success message
            $('#success').html("<div class='alert alert-success'>");
            $('#success > .alert-success').html("<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;")
                .append("</button>");
            $('#success > .alert-success')
                .append("<strong>Your message has been sent. </strong>");
            $('#success > .alert-success')
                .append('</div>');

            //clear all fields
            $('#contactForm').trigger("reset");
        },
        error: function(err) {
            var msg;
            switch(err.status) {
                case 400:
                  msg = err.statusText;
                  break;
                case 500:
                  msg = "It seems that my mail server is not responding. Please try again later!";
                  break;
            }
            $('#success').html("<div class='alert alert-danger'>");
            $('#success > .alert-danger').html("<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;")
                .append("</button>");
            $('#success > .alert-danger').append("<strong> Error:</strong> " + err.statusText);
            $('#success > .alert-danger').append('</div>');
        },
    });
  });
});
