function setCookie(cname,cvalue,exdays)
{
var d = new Date();
d.setTime(d.getTime()+(exdays*24*60*60*1000));
var expires = "expires="+d.toGMTString();
document.cookie = cname + "=" + cvalue + "; " + expires;
}

function getCookie(cname)
{
var name = cname + "=";
var ca = document.cookie.split(';');
for(var i=0; i<ca.length; i++)
  {
  var c = ca[i].trim();
  if (c.indexOf(name)==0) return c.substring(name.length,c.length);
}
return "";
}

function checkCookie()
{
var lastname=getCookie("asebox_lastname");
if (lastname!="")
  {
  alert("Welcome again " + lastname);
  }
else
  {
  lastname = prompt("Please enter your name:","");
  if (user!="" && user!=null)
    {
    setCookie("lastname",user,365);
    }
  }
}

function checkinit()
{
var lastname=getCookie("asebox_lastname");
  //alert("aseboxready.js: Welcome from checkinit lastname=" + lastname);
if (lastname=="") 
   {
//   window.location.href = '../signin/signin.html';
   }
// else
//   {
//   alert("Welcome again " + lastname);
//   }
}

checkinit();

