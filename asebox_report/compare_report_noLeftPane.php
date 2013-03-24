<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <script LANGUAGE="javascript" type="text/javascript" src="scripts/jsDate.js"></script>
    <script LANGUAGE="javascript" SRC="scripts/json2.js"> </script>
    <script LANGUAGE="javascript" SRC="scripts/calendrier.js"> </script>
    <script LANGUAGE="javascript" SRC="scripts/parsedate.js"> </script>
    <script LANGUAGE="javascript" SRC="scripts/asemon_report.js"> </script>
    <link rel=STYLESHEET type="text/css" href="stylesheets/common.css" >
    <link rel=STYLESHEET type="text/css" href="stylesheets/maindiv.css" >
    <link rel=STYLESHEET type="text/css" href="stylesheets/stylecalend.css" >

    <?php
    // Retreive session context
    include ("ARContext_restore.php");

    // Retreive search panel parameters
    if ( isset($_POST['StartTimestamp' ]) ) $StartTimestamp=  $_POST['StartTimestamp'];
    if ( isset($_POST['EndTimestamp'   ]) ) $EndTimestamp=    $_POST['EndTimestamp'];
    if ( isset($_POST['SrvType' ])        ) $SrvType=         $_POST['SrvType'];
    if ( isset($_POST['ServerName'     ]) ) $ServerName=      $_POST['ServerName'];
    if ( isset($_POST['DFormat'  ])       ) $DFormat=         $_POST['DFormat'];
    
    if ( isset($_POST['ServerName1'    ]) ) $ServerName1=     $_POST['ServerName1'];
    if ( isset($_POST['StartTimestamp1']) ) $StartTimestamp1= $_POST['StartTimestamp1'];
    if ( isset($_POST['EndTimestamp1'  ]) ) $EndTimestamp1=   $_POST['EndTimestamp1'];

    if ( isset($_POST['ServerName2'    ]) ) $ServerName2=     $_POST['ServerName2'];
    if ( isset($_POST['StartTimestamp2']) ) $StartTimestamp2= $_POST['StartTimestamp2'];
    if ( isset($_POST['EndTimestamp2'  ]) ) $EndTimestamp2=   $_POST['EndTimestamp2'];
    
    
    
    

    include ("connectArchiveServer.php");	



    $title = $ServerName."-".$selector;
    ?>


    <title> <?php echo $title ?> </title>

</head>

<body>
  <form name="inputparam" method="POST" action="">
  <?php  
  $displaylevel=0;
  include ("compare_search_panel.php");
  ?>
  <INPUT type="HIDDEN" name="ARContextJSON" value='<?php echo $ARContextJSON;?>' >

  <!-- initialize max-width of "boxinmain" div according to the windows width -->
  <script type=text/javascript> setMainDivSize(false); </script>
  <div class="maindiv">

  <?php
      include ("compare_report_by_type.php");
  ?>

  </div>  <!-- end div maindiv -->


  </form>
</body>
