$(document).ready(function(){
            <!-- $("#phpcontactform").submit(function(e){      -->
 
 $('#submit2').click(function(e){
  e.preventDefault();   
  var name = $("#name");
  var password = $("#password");
  var web = $("#website");
  var msg = $("#message");
  var flag = false;
  if(name.val()==''){
   name.closest('.control-group').addClass('error');
   name.focus();
   flag = false;
   return false;
  } else {
   name.closest('.control-group').removeClass('error').addClass('success');
  }
  if(password.val()==''){
   password.closest('.control-group').addClass('error');
   password.focus();
   flag = false;
   return false;
  } else {
   password.closest('.control-group').removeClass('error').addClass('success');
  }
   flag=true;
   var dataString = "name="+name.val()+"&password="+password.val()+"&web="+web.val()+"&msg="+msg.val();
   $('.loading').fadeIn('slow').html('Loading...');
   $.ajax({
    type: 'POST',
    data: dataString,
    url: 'signin.php',
    cache: false,
    success: function(d){
    	$('.control-group').removeClass('success');
    	if(d=='success') {    		
       		$('.loading').fadeIn('slow').html('<font color="green">Success..</font>');
   			var elem = document.getElementById('form-signin');
   			//setCookie
   			var dt = new Date();
   			//var exdays = 365;
                        dt.setTime(dt.getTime()+(365*24*60*60*1000));
                        var expires = "expires="+dt.toGMTString();
                      //document.cookie = "lastname=" + name.val() + "; " + expires;
                        document.cookie = "lastname=; expires=Thu, 01 Jan 1970 00:00:00 GMT"; 
                        document.cookie = "lastname=bbb; " + expires;

   			//setCookie("lastname",name,365);
   			//window.lastname=name.val();
			window.location.href = '../asebox_report/asebox_main.php?lastname=' + name.val();    			
		}
		else
    		$('.loading').fadeIn('slow').html('<font color="red">Incorrect name or password.</font>' );	
    	}
   });
  return false;
 }); 
 
})