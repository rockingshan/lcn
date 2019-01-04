$('document').ready(function()
{
     /* validation */
  $("#login-form").validate({
      rules:
   {
   pass: {
   required: true,
   },
   user: {
            required: true,
            },
    },
       messages:
    {
            pass:{
                      required: "please enter your password"
                     },
            user: "please enter your username",
       },
    submitHandler: submitForm
       });
    /* validation */

    /* login submit */
    function submitForm()
    {
   var data = $("#login-form").serialize();

   $.ajax({

   type : 'POST',
   url  : 'process/submit.php',
   data : data,
   beforeSend: function()
   {
    $("#error").fadeOut();
    $("#btn-login").html('<span class="glyphicon glyphicon-transfer"></span> &nbsp; sending ...');
   },
   success :  function(response)
      {
     if(response=="ok"){

      $("#btn-login").html('<img src="images/ripple.gif" /> &nbsp; Signing In ...');
      setTimeout(' window.location.href = "db_select.php"; ',4000);
     }
     else{

      $("#error").fadeIn(1000, function(){
    $("#error").html('<div class="alert alert-danger"> <span class="glyphicon glyphicon-info-sign"></span> &nbsp; '+response+' !</div>');
           $("#btn-login").html('<span class="glyphicon glyphicon-log-in"></span> &nbsp; Sign In');
         });
     }
     }
   });
    return false;
  }
    /* login submit */
});
