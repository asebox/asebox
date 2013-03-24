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


    if ( isset($_GET['dbid'])  )      $dbid= $_GET['dbid'];   else $dbid="";
    if ( isset($_GET['dbname'])  )      $dbname= $_GET['dbname'];   else $dbname="";
    if ( isset($_GET['isMixedLog'])  )      $isMixedLog= $_GET['isMixedLog'];   else $isMixedLog="";
    
    $title = $ServerName."-AseDbSpce. detail";
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
  <INPUT type="HIDDEN" name="ARContextJSON" value='<?php echo $ARContextJSON;?>' >

  <center>

  <H1> Space usage of database : <?php echo $dbname; ?> </H1>

    
       <img src='<?php echo "./graphDBUsage.php?ARContextJSON=".urlencode($ARContextJSON)."&dbid=".urlencode($dbid)."&dbname=".urlencode($dbname)."&isMixedLog=".urlencode($isMixedLog)."&typeGraph=DATA"; ?> '>
       <?php
       if ($isMixedLog==0) {
       ?>
           <p>
           <img src='<?php echo "./graphDBUsage.php?ARContextJSON=".urlencode($ARContextJSON)."&dbid=".urlencode($dbid)."&dbname=".urlencode($dbname)."&isMixedLog=".urlencode($isMixedLog)."&typeGraph=LOG"; ?> '>
           </p>
       <?php
       }
       ?>

  </center>
  </form>
</body>
