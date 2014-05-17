<?php 
//setcookie('asebox_firstname', 'Geoff', time()+3600);
//setcookie('asebox_lastname',  'STAMP', time()+3600);

$SID = session_id();
if (empty($SID)) session_start();

//$_SESSION['asebox_lastname'] = "";

if ( isset($_COOKIE['asebox_lastname']) ) {
	$LoginFirstName = 'COO:'.$_COOKIE['asebox_firstname'] ;
	$LoginLastName  = 'COO:'.$_COOKIE['asebox_lastname'] ;
        $asebox_lastname = $_COOKIE["asebox_lastname"];
      //print "<div>cookie.asebox_lastname=" . $_COOKIE['asebox_lastname'] . "</div>";     
}

if ( isset($_SESSION['asebox_lastname']) ) {
   $LoginFirstName = $_SESSION['asebox_firstname'] ;
   $LoginLastName  = $_SESSION['asebox_lastname'] ;
}


?>



