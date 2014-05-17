<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <script LANGUAGE="javascript" type="text/javascript" SRC="../scripts/jsDate.js"></script>
    <script LANGUAGE="javascript" SRC="../scripts/json2.js"> </script>
    <script LANGUAGE="javascript" SRC="../scripts/calendrier.js"> </script>
    <script LANGUAGE="javascript" SRC="../scripts/parsedate.js"> </script>
    <script LANGUAGE="javascript" SRC="../scripts/asemon_report.js"> </script>
    <link rel=STYLESHEET type="text/css" href="../stylesheets/asebox.css" >

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


    if ( isset($_GET['Field_id'])  )      $Field_id= $_GET['Field_id'];   else $Field_id="";
    if ( isset($_GET['fldname'])  )      $fldname= $_GET['fldname'];   else $fldname="";
    if ( isset($_GET['Name'])  )      $Name= $_GET['Name'];   else $Name="";


    $title = $ServerName."-Spinlock detail";
    ?>

    <title> <?php echo $title ?> </title>

</head>

<body>
  <script type=text/javascript> setMainDivSize(false); </script>
  <form name="inputparam" method="POST" action="">
  <?php
  $displaylevel=1;
  include ("../compare_search_panel.php");
  ?>
  <INPUT type="HIDDEN" name="ARContextJSON" value='<?php echo $ARContextJSON;?>' >

    <center>
        
    <H1> Detail for spinlock : Name = <?php echo $Name; ?>, FldName = <?php echo $fldname; ?>, ID = <?php echo $Field_id; ?> </H1>

 
    
   <H2> Spinlock Contention (%) </H2>
    <p>
       <img src='<?php echo "./graph_Spinlock_contention.php?Type=Contention&Field_id=".urlencode($Field_id)."&fldname=".urlencode($fldname)."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
    </p>

   <H2> Waits </H2>
    
    <p>
       <img src='<?php echo "./graph_Spinlock_contention.php?Type=Waits&Field_id=".urlencode($Field_id)."&fldname=".urlencode($fldname)."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
    </p>

   <H2> Spins </H2>
    
    <p>
       <img src='<?php echo "./graph_Spinlock_contention.php?Type=Spins&Field_id=".urlencode($Field_id)."&fldname=".urlencode($fldname)."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
    </p>

</center>
</form>
</body>
