<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <script LANGUAGE="javascript" type="text/javascript" SRC="../scripts/jsDate.js"></script>
    <script LANGUAGE="javascript" SRC="../scripts/json2.js"> </script>
    <script LANGUAGE="javascript" SRC="../scripts/calendrier.js"> </script>
    <script LANGUAGE="javascript" SRC="../scripts/parsedate.js"> </script>
    <script LANGUAGE="javascript" SRC="../scripts/asemon_report.js"> </script>
    <link rel=STYLESHEET type="text/css" href="../stylesheets/common.css" >
    <link rel=STYLESHEET type="text/css" href="../stylesheets/maindiv.css" >
    <link rel=STYLESHEET type="text/css" href="../stylesheets/stylecalend.css" >
    <link rel=STYLESHEET type="text/css" href="../stylesheets/menu.css" >

    <?php
    // Retreive session context
    include ("../ARContext_restore.php");

    // Retreive search panel parameters
    if ( isset($_POST['StartTimestamp' ]) ) $StartTimestamp= $_POST['StartTimestamp'];
    if ( isset($_POST['EndTimestamp'   ]) ) $EndTimestamp=   $_POST['EndTimestamp'];
    if ( isset($_POST['SrvType' ])        ) $SrvType=        $_POST['SrvType'];
    if ( isset($_POST['ServerName'     ]) ) $ServerName=     $_POST['ServerName'];
    if ( isset($_POST['DFormat'  ])       ) $DFormat=        $_POST['DFormat'];

    include ("../connectArchiveServer.php");	

    if ( isset($_GET['filter_clause'   ]) ) $filter_clause=   $_GET['filter_clause'];    else $filter_clause="";

    $title = $ServerName."-Process repartition";
    ?>

    <title> <?php echo $title ?> </title>

</head>

<body>
  <script type=text/javascript> setMainDivSize(false); </script>
  <form name="inputparam" method="POST" action="">
  <?php
  $displaylevel=1;
  include ("../asemon_search_panel.php");
  ?>




<div class="maindiv">

HELLO</br>  
<div class="divMenu">

<?php                     
     include ("menu.php");
 ?>                  
</div>  <!-- end div divMenu -->  
HELLO2</br>  
  
  
  <INPUT type="HIDDEN" name="ARContextJSON" value='<?php echo $ARContextJSON;?>' >
   
  <center>
  
<P>
   <img src='<?php echo "./graphPieProcess.php?group=login&indicator=logical&filter_clause=".urlencode($filter_clause)."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
   <img src='<?php echo "./graphPieProcess.php?group=program&indicator=logical&filter_clause=".urlencode($filter_clause)."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
</p>
<p>
   <img src='<?php echo "./graphPieProcess.php?group=login&indicator=cpu&filter_clause=".urlencode($filter_clause)."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
   <img src='<?php echo "./graphPieProcess.php?group=program&indicator=cpu&filter_clause=".urlencode($filter_clause)."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
</p>
<P>
   <img src='<?php echo "./graphPieProcess.php?group=login&indicator=physreads&filter_clause=".urlencode($filter_clause)."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
   <img src='<?php echo "./graphPieProcess.php?group=program&indicator=physreads&filter_clause=".urlencode($filter_clause)."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
</p>

  </center>
  </div>

</form>
</body>
