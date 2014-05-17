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

    if ( isset($_POST['StartTimestamp' ]) ) $StartTimestamp= $_POST['StartTimestamp'];
    if ( isset($_POST['EndTimestamp'   ]) ) $EndTimestamp=   $_POST['EndTimestamp'];
    if ( isset($_POST['DFormat'  ])       ) $DFormat=        $_POST['DFormat'];

    include ("../connectArchiveServer.php");	

	$param_list=array(
		'DBID',
		'ProcName'
	);
	foreach ($param_list as $param)
    @$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];

    if ( isset($_POST['days'     ]) ) $days=$_POST['days'];      else $days="";


    ?>
    <title> <?php echo $ProcName ?> </title>

</head>

<body>
<form name="inputparam" method="POST" action="">
  <?php  
  $displaylevel=1;
  include ("../compare_search_panel.php");
  ?>

<center>  
    <?php
    // Check if Trends tables exist
    $query = "select cnt=count(*) 
              from sysobjects 
              where name in ( '".$ServerName."_TrendProc',  '".$ServerName."_TrendsCfg')";   
    $result = sybase_query($query,$pid);
    $row = sybase_fetch_array($result);
    if ($row["cnt"] < 2) {

 echo "TrendProc data is not available. The indicators aggregation has not been activated for server ".$ServerName.".<P> (Add Trends.xml in the asemon_logger config file)";
        exit();
        
    }
    
  ?>



<div class="boxinmain" style="min-width:800px">
<div class="boxtop">
<div class="title">TrendProcs - Per day statistics</div>
<a   href="ttp://github.com/asebox/asebox?title=AseRep_TrendProcs" TARGET="_blank"> <img class="help" SRC="../images/Help-circle-blue-32.png" ALT="TrendProcs help" TITLE="TrendProcs help"  /> </a>
</div>

<div class="boxcontent">
<div class="boxbtns" >
<table align="left" cellspacing="2px" ><tr>
<td>
  	Days : <select name="days" > 
          <option <?php if ($days=='' ) {echo "SELECTED";  } ?> > All days </option>
          <option <?php if ($days=='no_wenkend' ) {echo "SELECTED";  } ?> > no_wenkend </option>
          <option <?php if ($days=='Monday' ) {echo "SELECTED";  } ?> > Monday </option>
          <option <?php if ($days=='Tuesday' ) {echo "SELECTED";  } ?> > Tuesday </option>
          <option <?php if ($days=='Wednesday' ) {echo "SELECTED";  } ?> > Wednesday </option>
          <option <?php if ($days=='Thursday' ) {echo "SELECTED";  } ?> > Thursday </option>
          <option <?php if ($days=='Friday' ) {echo "SELECTED";  } ?> > Friday </option>
          <option <?php if ($days=='Saturday' ) {echo "SELECTED";  } ?> > Saturday </option>
          <option <?php if ($days=='Sunday' ) {echo "SELECTED";  } ?> > Sunday </option>
        </select>

</td>
<td>
	<img src="../images/button_sideLt.gif"  class="btn" height="20px" >
    <INPUT style="height:20px; " class="btn" type="submit" value="Refresh" name="RefreshStmt" >
    <img src="../images/button_sideRt.gif"  class="btn" height="20px">
</td>
<td> DBID : </td>
<td><input type="text" size="4" name="DBID" value="<?php echo $DBID ?>" ></td>
<td> ProcName : </td>
<td><input type="text"  name="ProcName" value="<?php echo $ProcName ?>" ></td>
</tr></table>
</div>
        
        
<table cellspacing=10 cellpadding=0 class="textInfo" > 
    <tr> <td> <center>
            <p>
              <img src='<?php echo "./graph_TrendProc.php?DBID=".urlencode($DBID)."&ProcName=".urlencode($ProcName)."&days=".urlencode($days)."&indic=Executions"."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
            </p>
            <p>
              <img src='<?php echo "./graph_TrendProc.php?DBID=".urlencode($DBID)."&ProcName=".urlencode($ProcName)."&days=".urlencode($days)."&indic=NbPlans"."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
            </p>
            <p>
              <img src='<?php echo "./graph_TrendProc.php?DBID=".urlencode($DBID)."&ProcName=".urlencode($ProcName)."&days=".urlencode($days)."&indic=AvgLogicalReads"."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
            </p>
            <p>
              <img src='<?php echo "./graph_TrendProc.php?DBID=".urlencode($DBID)."&ProcName=".urlencode($ProcName)."&days=".urlencode($days)."&indic=AvgPhysicalReads"."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
            </p>
            <p>
              <img src='<?php echo "./graph_TrendProc.php?DBID=".urlencode($DBID)."&ProcName=".urlencode($ProcName)."&days=".urlencode($days)."&indic=AvgCpuTime"."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
            </p>
            <p>
              <img src='<?php echo "./graph_TrendProc.php?DBID=".urlencode($DBID)."&ProcName=".urlencode($ProcName)."&days=".urlencode($days)."&indic=AvgWaitTime"."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
            </p>
            <p>
              <img src='<?php echo "./graph_TrendProc.php?DBID=".urlencode($DBID)."&ProcName=".urlencode($ProcName)."&days=".urlencode($days)."&indic=AvgMemUsageKB"."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
            </p>
            <p>
              <img src='<?php echo "./graph_TrendProc.php?DBID=".urlencode($DBID)."&ProcName=".urlencode($ProcName)."&days=".urlencode($days)."&indic=AvgPagesModified"."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
            </p>
            <p>
              <img src='<?php echo "./graph_TrendProc.php?DBID=".urlencode($DBID)."&ProcName=".urlencode($ProcName)."&days=".urlencode($days)."&indic=AvgPacketsSent"."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
            </p>
            <p>
              <img src='<?php echo "./graph_TrendProc.php?DBID=".urlencode($DBID)."&ProcName=".urlencode($ProcName)."&days=".urlencode($days)."&indic=AvgPacketsReceived"."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
            </p>
            <p>
              <img src='<?php echo "./graph_TrendProc.php?DBID=".urlencode($DBID)."&ProcName=".urlencode($ProcName)."&days=".urlencode($days)."&indic=AvgRowsAffected"."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
            </p>

    </center>
    </td> </tr>
</table>

</DIV>
</DIV>
</center>
</form>
</body>
