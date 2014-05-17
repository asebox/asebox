<?php
   $SID = session_id();
   if (empty($SID)) session_start();

   if (($_COOKIE["asebox_contact"] == "") and ($_SESSION["asebox_contact"] == "") ) {
       header("Location: ../signin/signin.html");
   }
   
   if (($_SESSION["asebox_contact"] == "xxx") ) {
       setcookie('asebox_contact',  "", time()+3600);
       setcookie('asebox_fullname', "", time()+3600);
       header("Location: ../signin/signin.html");
   }
   
   if ( isset($_SESSION["asebox_contact"]) ) {
      $LoginContact  = $_SESSION['asebox_contact' ] ;
      $LoginFullName = $_SESSION['asebox_fullname'] ;
      setcookie('asebox_contact',   $LoginContact,  time()+3600);
      setcookie('asebox_firstname', $LoginFullName, time()+3600);
   } else {
      $LoginContact  = $_COOKIE['asebox_contact'  ] ;
      $LoginFullName = $_COOKIE['asebox_fullname'] ;
   }
?>
