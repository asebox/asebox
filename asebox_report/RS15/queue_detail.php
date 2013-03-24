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

    if ( isset($_GET['Info'])  )      $Info= $_GET['Info'];   else $Info="";

    $title = $ServerName."-Queue detail";



    // Get ID and instance_id of queue SQM
    $result=sybase_query("select ID, instance_id from ".$ServerName."_Instances where instance like 'SQM, ".$Info."%'");
    $row=sybase_fetch_array($result);
	if ($row != null) {
      $ID_SQM = $row["ID"];	
      $instance_id = $row["instance_id"];	

      // Get ID and instance_id of queue SQMR
      $result=sybase_query("select ID from ".$ServerName."_Instances where instance_id=".$instance_id." and instance like 'SQMR%'");
      $row=sybase_fetch_array($result);
      $ID_SQMR = $row["ID"];	
    } else {
	  $ID_SQM = null;
	  $instance_id = null;
	  $ID_SQR = null;
	}


    ?>

    <title> <?php echo $title ?> </title>

</head>

<body>
  <script type=text/javascript> setMainDivSize(false); </script>
  <form name="inputparam" method="POST" action="">
  <?php
  $displaylevel=1;
  include ("../asemon_search_panel.php");
  //echo "ID_SQM=".$ID_SQM." instance_id=".$instance_id." ID_SQMR=".$ID_SQMR."\n";
  ?>
  <INPUT type="HIDDEN" name="ARContextJSON" value='<?php echo $ARContextJSON;?>' >
   

  <center>
    <H1> Activity for queue : <?php echo  $Info; ?> </H1>
  
    
    <p>
       <img src='<?php echo "./graph_RSQueue.php?Info=".urlencode($Info)."&type=ACTIVE&ARContextJSON=".urlencode($ARContextJSON); ?> '>
    </p>
    <p>
       <img src='<?php echo "./graph_RSQueue.php?Info=".urlencode($Info)."&type=SAVED&ARContextJSON=".urlencode($ARContextJSON); ?> '>
    </p>
	
	<?php
	if ($ID_SQM != null) {
	?>
            <p>
            <img src='<?php echo "./graph_RS_Statcounter.php?ID=".$ID_SQM."&instance_id=".$instance_id."&counter_id=6000&counter_name=CmdsWritten&ARContextJSON=".urlencode($ARContextJSON); ?> '>
            </p>


            <p>
            <img src='<?php echo "./graph_RS_Statcounter.php?ID=".$ID_SQMR."&instance_id=".$instance_id."&counter_id=62000&counter_name=CmdsRead&ARContextJSON=".urlencode($ARContextJSON); ?> '>
            </p>

            <p>
            <img src='<?php echo "./graph_RS_Statcounter.php?ID=".$ID_SQM."&instance_id=".$instance_id."&counter_id=6002&counter_name=BlocksWritten&ARContextJSON=".urlencode($ARContextJSON); ?> '>
            </p>

            <p>
            <img src='<?php echo "./graph_RS_Statcounter.php?ID=".$ID_SQMR."&instance_id=".$instance_id."&counter_id=62002&counter_name=BlocksRead&ARContextJSON=".urlencode($ARContextJSON); ?> '>
            </p>

            <p>
            <img src='<?php echo "./graph_RS_Statcounter.php?ID=".$ID_SQMR."&instance_id=".$instance_id."&counter_id=62004&counter_name=BlocksReadCached&ARContextJSON=".urlencode($ARContextJSON); ?> '>
            </p>

            <p>
            <img src='<?php echo "./graph_RS_Statcounter.php?ID=".$ID_SQM."&instance_id=".$instance_id."&counter_id=6057&counter_name=AvgSQMWriteTime_ms&ARContextJSON=".urlencode($ARContextJSON); ?> '>
            </p>

            <p>
            <img src='<?php echo "./graph_RS_Statcounter.php?ID=".$ID_SQMR."&instance_id=".$instance_id."&counter_id=62011&counter_name=AvgSQMRReadTime_ms&ARContextJSON=".urlencode($ARContextJSON); ?> '>
            </p>


    <?php
	}
	?>
</center>
</form>
</body>
