<?php
   $name = $_REQUEST['name'] ;
   $password = $_REQUEST['password'] ;
   session_start();
   $_SESSION["asebox_contact"]   = "";
   $_SESSION["asebox_firstname"] = "";
   $_SESSION["asebox_lastname"]  = "";

   if (( $name == "aaa")    && ($password != "xxx")) { $f="Alan";             $n="Alexander"   ; } 
   if (( $name == "bbb")    && ($password != "xxx")) { $f="Betty";            $n="Boop"        ; } 	
   if (( $name == "ccc")    && ($password != "xxx")) { $f="Chris";            $n="Carol"       ; } 	
   if (( $name == "ddd")    && ($password != "xxx")) { $f="Damon";            $n="Dale"        ; } 	
   if (( $name == "653949") && ($password != "xxx")) { $f="Geoff";            $n="STAMP"       ; } 	

   //It's OK
   if ( $n !== "" ) { 	
      $_SESSION["asebox_contact"]   = $name;
      $_SESSION["asebox_firstname"] = $f;
      $_SESSION["asebox_lastname"]  = $n;
   	
      //header("Location: ../asebox_report/asebox_main.php");
      print "success";
   }
   
?>