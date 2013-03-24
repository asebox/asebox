<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <script LANGUAGE="javascript" type="text/javascript" src="../scripts/jsDate.js"></script>
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


  $param_list=array(
  	'Instance_ID',
  	'Info'
  );
  foreach ($param_list as $param)
  @$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];
    

  if ( !isset($orderStmt) ) $orderStmt="StmtID";
  if ( !isset($rowcnt) ) $rowcnt=200;

?>


<title>Asemon Report - RA detail</title>

</head>

<body>
  <form name="inputparam" method="POST" action="">
  <?php  
  $displaylevel=1;
  include ("../asemon_search_panel.php");
  ?>
  <INPUT type="HIDDEN" name="ARContextJSON" value='<?php echo $ARContextJSON;?>' >
  <INPUT type="HIDDEN" name="Instance_ID" value='<?php echo $Instance_ID;?>' >
  <INPUT type="HIDDEN" name="Info" value='<?php echo $Info;?>' >

   <CENTER>
   <H1> RA Detail - <?php echo $Info; ?></H1>


   <p>
   <img src='<?php echo "./graphSQMSegs.php?Instance_ID=".$Instance_ID."&Instance_Val=1&ARContextJSON=".urlencode($ARContextJSON); ?> '>
   </p>
             
   <p>
   <img src='<?php echo "./graphRAWrites.php?Instance_ID=".$Instance_ID."&Instance_Val=1&ARContextJSON=".urlencode($ARContextJSON); ?> '>
   </p>

   <p>
   <img src='<?php echo "./graphSQMWriteBPS.php?Instance_ID=".$Instance_ID."&Instance_Val=1&ARContextJSON=".urlencode($ARContextJSON); ?> '>
   </p>

   <p>
   <img src='<?php echo "./graphSQMCmds.php?Instance_ID=".$Instance_ID."&Instance_Val=1&ARContextJSON=".urlencode($ARContextJSON); ?> '>
   </p>

   <p>
   <img src='<?php echo "./graphSQMAvgCmds.php?Instance_ID=".$Instance_ID."&Instance_Val=1&ARContextJSON=".urlencode($ARContextJSON); ?> '>
   </p>

   <p>
   <img src='<?php echo "./graphSQMReadBPS.php?Instance_ID=".$Instance_ID."&Instance_Val=21&ARContextJSON=".urlencode($ARContextJSON); ?> '>
   </p>

   <p>
   <img src='<?php echo "./graphSQMReadCmds.php?Instance_ID=".$Instance_ID."&Instance_Val=21&ARContextJSON=".urlencode($ARContextJSON); ?> '>
   </p>




    </center>
</body>