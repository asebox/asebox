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


    if ( isset($_GET['DbName'])  )     $DbName= $_GET['DbName'];   else $DbName="";
    if ( isset($_GET['Owner'])  )      $Owner= $_GET['Owner'];   else $Owner="";
    if ( isset($_GET['TabName'])  )    $TabName= $_GET['TabName'];   else $TabName="";
    if ( isset($_GET['IndId'])  )      $IndId= $_GET['IndId'];   else $IndId="";
    if ( isset($_GET['IndexName'])  )  $IndexName= $_GET['IndexName'];   else $IndexName="";
    if ( isset($_GET['LockMode'])  )   $LockMode= $_GET['LockMode'];   else $LockMode="";
    if ( isset($_GET['Clu'])  )        $Clu= $_GET['Clu'];   else $Clu="";
    
    /* compute first and last timestamp of captured frag info for this object */
    $query = "select firstTS=convert(varchar,min(Timestamp),109) from ".$ServerName."_Fragment where dbname='".$DbName."' and owner='".$Owner."' and tabname='".$TabName."' and indid=".$IndId;
    //echo $query;
    $result = sybase_query($query,$pid);
    $row = sybase_fetch_array($result);
    $FirstTimestamp = $row["firstTS"];
    
    $query = "select lastTS=convert(varchar,max(Timestamp),109) from ".$ServerName."_Fragment where dbname='".$DbName."' and owner='".$Owner."' and tabname='".$TabName."' and indid=".$IndId;
    //echo $query;
    $result = sybase_query($query,$pid);
    $row = sybase_fetch_array($result);
    $LastTimestamp = $row["lastTS"];
    
    
    if ($Clu=="clu") $Clu="YES"; else $Clu="NO";
    
    $title = $ServerName."-Frag. detail";
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

  <H1> Fragmentation of object : <?php echo $DbName; ?>.<?php echo $Owner; ?>.<?php echo $TabName; ?> </H1>
  <H2> Indid = <?php echo $IndId; ?>, IndexName = <?php echo $IndexName; ?> , Clustered : <?php echo $Clu; ?> </H2>
  <H2> Lockmode = <?php echo $LockMode; ?> </H2>


  <P>
    
    <?php if ($IndId < 2) { ?>
    <p>
       <img src='<?php echo "./graph_Object_Rows.php?DbName=".urlencode($DbName)."&Owner=".urlencode($Owner)."&TabName=".urlencode($TabName)."&IndId=".urlencode($IndId)."&StartTS=".urlencode($StartTimestamp)."&EndTS=".urlencode($EndTimestamp)."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
    </p>
    <?php } ?>
    
    <p>
       <img src='<?php echo "./graph_Object_Size.php?DbName=".urlencode($DbName)."&Owner=".urlencode($Owner)."&TabName=".urlencode($TabName)."&IndId=".urlencode($IndId)."&StartTS=".urlencode($StartTimestamp)."&EndTS=".urlencode($EndTimestamp)."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
    </p>

    <p>
       <img src='<?php echo "./graph_Object_CR.php?DbName=".urlencode($DbName)."&Owner=".urlencode($Owner)."&TabName=".urlencode($TabName)."&IndId=".urlencode($IndId)."&StartTS=".urlencode($StartTimestamp)."&EndTS=".urlencode($EndTimestamp)."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
    </p>

    <p>
       <img src='<?php echo "./graph_Object_sputl_ioeff.php?DbName=".urlencode($DbName)."&Owner=".urlencode($Owner)."&TabName=".urlencode($TabName)."&IndId=".urlencode($IndId)."&StartTS=".urlencode($StartTimestamp)."&EndTS=".urlencode($EndTimestamp)."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
    </p>
    

  </center>
  </form>
</body>
