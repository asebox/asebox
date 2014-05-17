<!DOCTYPE html> 
<html lang="en"> 
<head>
    <title>Signin Form</title>
    <meta charset="utf-8">
    <link href="bootstrap.min.css" rel="stylesheet">
    <script LANGUAGE="javascript" SRC="../asebox_report/scripts/jquery-1.3.1.min.js"> </script>
</head>
<body>
<?php
    $SID = session_id();
    if (empty($SID)) session_start();

    $contact   = $_GET['contact'  ];
    $firstname = $_GET['firstname'];
    $lastname  = $_GET['lastname' ];
    
    print "<b>GET</b><br>";
    print "<div>GET.contact="   . $contact   . "</div>";   
    print "<div>GET.firstname=" . $firstname . "</div>";   
    print "<div>GET.lastname="  . $lastname  . "</div>";   
    print "<br>";

    print "<b>COOKIES</b><br>";
    print "<div>COOKIE.asebox_contact="   . $_COOKIE["asebox_contact"]   . "</div>";   
    print "<div>COOKIE.asebox_firstname=" . $_COOKIE["asebox_firstname"] . "</div>";   
    print "<div>COOKIE.asebox_lastname="  . $_COOKIE["asebox_lastname"]  . "</div>";   
    print "<br>";
    
    setcookie("asebox_contact",   $contact,   time()+3600);
    setcookie("asebox_firstname", $firstname, time()+3600);
    setcookie("asebox_lastname",  $lastname,  time()+3600);
    
    $_SESSION['asebox_contact'  ] = $contact  ;
    $_SESSION['asebox_firstname'] = $firstname;
    $_SESSION['asebox_lastname' ] = $lastname ;

    print "<b>COOKIES</b><br>";
    print "<div>COOKIE.asebox_contact="   . $_COOKIE["asebox_contact"]   . "</div>";   
    print "<div>COOKIE.asebox_firstname=" . $_COOKIE["asebox_firstname"] . "</div>";   
    print "<div>COOKIE.asebox_lastname="  . $_COOKIE["asebox_lastname"]  . "</div>";   
    print "<br>";

    print "<b>ALL COOKIES</b><br>";    
    // Print all cookies
    print_r($_COOKIE);
    // Print all cookies

    print "<br><br><b>SESSION</b><br>";    
    print_r($_SESSION);
?>

<script>

function setCookie(cname,cvalue,exdays)
{
var d = new Date();
d.setTime(d.getTime()+(exdays*24*60*60*1000));
var expires = "expires="+d.toGMTString();
document.cookie = cname + "=" + cvalue + "; " + expires;
}

setCookie("asebox_lastname","set-cookie",time()+3600*24);

</script>
</body>
</html>
