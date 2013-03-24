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

    if ( isset($_POST['StartTimestamp' ]) ) $StartTimestamp= $_POST['StartTimestamp'];
    if ( isset($_POST['EndTimestamp'   ]) ) $EndTimestamp=   $_POST['EndTimestamp'];
    if ( isset($_POST['SrvType' ])        ) $SrvType=        $_POST['SrvType'];
    if ( isset($_POST['ServerName'     ]) ) $ServerName=     $_POST['ServerName'];
    if ( isset($_POST['DFormat'  ])       ) $DFormat=        $_POST['DFormat'];

    include ("../connectArchiveServer.php");	

    if ( isset($_GET['filter_clause'   ]) ) $filter_clause=   $_GET['filter_clause'];    else $filter_clause="";
    
    // Retreive search panel parameters
    if ( isset($_POST['orderPrc'      ]) ) $orderPrc=        $_POST['orderPrc'];        else $orderPrc=$order_by;
    if ( isset($_POST['rowcnt'        ]) ) $rowcnt=          $_POST['rowcnt'];          else $rowcnt=200;
    if ( isset($_POST['filterprogram' ]) ) $filterprogram=   $_POST['filterprogram'];   else $filterprogram="";    
    if ( isset($_POST['filtermessage' ]) ) $filtermessage=   $_POST['filtermessage'];   else $filtermessage="";
    if ( isset($_POST['filterlogtype' ]) ) $filterlogtype=   $_POST['filterlogtype'];   else $filterlogtype="";
    if ( isset($_POST['filterusername']) ) $filterusername=  $_POST['filterusername'];  else $filterusername="";
    if ( isset($_POST['filterspid'    ]) ) $filterspid=      $_POST['filterspid'];      else $filterspid="";
    if ( isset($_POST['filtermintime' ]) ) $filtermintime=   $_POST['filtermintime'];   else $filtermintime="";

    $title = $ServerName."-Procedures";
    ?>

    <title> <?php echo $title ?> </title>

</head>

<body onload="setLeftPaneHeight();init();">
  <script type=text/javascript> setMainDivSize(false); </script>
  <form name="inputparam" method="POST" action="">
  <?php
  $displaylevel=0;
  include ("../compare_search_panel.php");
  ?>
  <INPUT type="HIDDEN" name="ARContextJSON" value='<?php echo $ARContextJSON;?>' >
   
  <center>

<!--
<P>
   <img src='<?php echo "./graph_AppLog_Procs.php?group=login&indicator=logical&filterprogram=spu_amd_affi_crs_dis_list_ctrl&filter_clause=".urlencode($filter_clause)."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
</p>
-->
<!--
<P>
   <img src='<?php echo "./graph_AppLog_Procs2.php?group=login&indicator=logical&filter_clause=".urlencode($filter_clause)."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
</p>
-->

<P>
   <img src='<?php echo "./graph_AppLog_Procs3.php?group=login&indicator=logical&filter_clause=".urlencode($filter_clause)."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
</p>

  </center>
</form>
</body>
