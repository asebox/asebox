<html>                                                                    
<head>                                                            
<title>Login</title>
</head>                                                           
<body>                                                            
                                                                  
<?php                                                             
$SID = session_id();
if (empty($SID)) session_start();

// Check if username and password are correct
$username = $_POST["username"] ;                                                                 
$password = $_POST["password"] ;                                                                 
$_SESSION["asebox_contact"] = "";

if ( $username == "aaa" && $password != "xxx") { 	
   $_SESSION["asebox_contact"]   = $username;
   $_SESSION["asebox_firstname"] = "Alan";
   $_SESSION["asebox_lastname"]  = "Alexander";
} 
if ( $username == "bbb" && $password != "xxx") { 	
   $_SESSION["asebox_contact"]   = $username;
   $_SESSION["asebox_firstname"] = "Betty";
   $_SESSION["asebox_lastname"]  = "Boop";
} 

if (  $_SESSION["asebox_contact"] !== "" ) { 	
   
   header("Location: ../asebox_report/asebox_main.php");
	
}                                                                 
else {                                                            
   // If not correct, we set the session to NO                       
   session_start();                                                
   $_SESSION["Login"] = "NO";                                      
   echo "<h1>You are NOT logged correctly in </h1>";               
   echo "<p><a href='document.php'>Link to protected file</a><p/>";                                                                  
}                                                                  
?>   

   <form method="post" action="login.php">
   
      <p>xUsername: <input type="text" name="username" /></p>
      <p>xPassword: <input type="text" name="password" /></p>
      
      <p><input type="submit" value="Let me in" /></p>
   
   </form>

                                                             
</body>                                                           
</html>                                                           