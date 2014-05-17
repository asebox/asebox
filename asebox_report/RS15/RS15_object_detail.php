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

  if ( isset($_GET['ID']) ) $ID= $_GET['ID'];  else $ID="";
  if ( isset($_GET['instance_id']) ) $instance_id= $_GET['instance_id'];  else $instance_id="";
  if ( isset($_GET['ObjectName']) ) $ObjectName= $_GET['ObjectName'];  else $ObjectName="";

?>



<title>Asemon Report - RS Object detail</title>

</head>

<body>
  <form name="inputparam" method="POST" action="">
  <?php  
  $displaylevel=1;
  include ("../asemon_search_panel.php");
  ?>
  <INPUT type="HIDDEN" name="ARContextJSON" value='<?php echo $ARContextJSON;?>' >

   <center>

   <H1> Object activity of :</H1>
   <H1> <?php echo $ObjectName; ?> </H1>


    
    <!-- Display graph of inserts /s for the DATA part of this object  --> 
    <p>
       <img src='<?php echo "./graph_RS_Statcounter.php?ID=".urlencode($ID)."&instance_id=".urlencode($instance_id)."&counter_id=65000&counter_name=inserts&ARContextJSON=".urlencode($ARContextJSON); ?> '>
    </p>
    
    <!-- Display graph of updates /s for the DATA part of this object   -->
    <p>
       <img src='<?php echo "./graph_RS_Statcounter.php?ID=".urlencode($ID)."&instance_id=".urlencode($instance_id)."&counter_id=65001&counter_name=updates&ARContextJSON=".urlencode($ARContextJSON); ?> '>
    </p>
    
    <!-- Display graph of deletes /s for the DATA part of this object  -->
    <p>
       <img src='<?php echo "./graph_RS_Statcounter.php?ID=".urlencode($ID)."&instance_id=".urlencode($instance_id)."&counter_id=65002&counter_name=deletes&ARContextJSON=".urlencode($ARContextJSON); ?> '>
    </p>

    <!-- Display graph of writetext /s for the DATA part of this object  -->
    <p>
       <img src='<?php echo "./graph_RS_Statcounter.php?ID=".urlencode($ID)."&instance_id=".urlencode($instance_id)."&counter_id=65003&counter_name=writetext&ARContextJSON=".urlencode($ARContextJSON); ?> '>
    </p>

    <!-- Display graph of exec /s for the DATA part of this object   -->
    <p>
       <img src='<?php echo "./graph_RS_Statcounter.php?ID=".urlencode($ID)."&instance_id=".urlencode($instance_id)."&counter_id=65004&counter_name=exec&ARContextJSON=".urlencode($ARContextJSON); ?> '>
    </p>


    </center>
</form>
</body>
