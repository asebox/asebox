<?php
   $name = $_REQUEST['name'] ;
   $password = $_REQUEST['password'] ;
   session_start();
   $_SESSION["asebox_contact"]  = "";
   $_SESSION["asebox_fullname"] = "";

   if (( $name == 'guest')    && ($password != 'xxx')) { $f='Guest';  } 

   //It's OK
   if ( $f !== "" ) { 	
      $_SESSION["asebox_contact"]  = $name;
      $_SESSION["asebox_fullname"] = $f;
   	
      //header("Location: ../asebox_report/asebox_main.php");
      print "success";
   }
?>
