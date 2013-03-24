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
  
    $dbname = $_GET['dbname'];
    $uid = $_GET['uid'];
    $id = $_GET['id'];
    $hashkey = $_GET['hashkey'];

    $title = $ServerName."-QPMetric Detail";
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

   <?php
        // get statement statistics 
	$query = "
    select 
    usecount=sum(cnt) ,
    lio_avg =avg(lio_avg )   ,
    pio_avg =avg(pio_avg )   ,
    exec_avg=avg(exec_avg)   ,
    elap_avg=avg(elap_avg)  
	from ".$ServerName."_QPMetrics
	where dbname = '".$dbname."'
	and uid=".$uid."
	and id=".$id."
	and hashkey=".$hashkey."
    and Timestamp >='".$StartTimestamp."'        
	and Timestamp <'".$EndTimestamp."'"	;


	$result = sybase_query($query,$pid);
	if ($result==false){ 
		sybase_close($pid); 
		$pid=0;
		include ("../connectArchiveServer.php");	
		echo "<tr><td>Error</td></tr></table>";
		return(0);
	}

	$rw=0;
	$cpt=0;
        $row = sybase_fetch_array($result);

   ?>


<div class="boxinmain" style="max-width:780px;float:none">
<div class="boxtop">
<div class="title" style="width:65%">Statement Statistics</div>
</div>

<div class="boxcontent">
<div class="statMainInfo">


<table cellspacing=0 cellpadding=0 class="infobox">
<tr class="infobox">
<td class="infobox">
<b class="newsparatitle">Statement Info :</b><br>
</td>
<td class="infobox">
<b class="newsparatitle">Statement Statistics :</b><br>
</td>
</tr>
<tr>
<td class="infobox" valign="top"> <center>
  <table border="0" cellspacing="1" cellpadding="0" class="statInfo" >
      <tr> <td> dbname </td> <td> : </td> <td> <?php echo $dbname ?> </td> </tr>
      <tr> <td> uid </td> <td> : </td> <td> <?php echo $uid ?> </td> </tr>
      <tr> <td> id </td> <td> : </td> <td> <?php echo $id ?> </td> </tr>
      <tr> <td> hashkey </td> <td> : </td> <td> <?php echo $hashkey ?> </td> </tr>
  </table></center>
</td>
<td class="infobox" valign="top"> <center>
  <table border="0" cellspacing="1" cellpadding="0" class="statInfo">
      <tr> <td> UseCount                      </td> <td> : </td> <td ALIGN="right"> <?php echo number_format($row["usecount"])                   ?> </td> </tr>  
      <tr> <td> lio_avg                       </td> <td> : </td> <td ALIGN="right"> <?php echo number_format($row["lio_avg"] )                    ?> </td> </tr>  
      <tr> <td> pio_avg                       </td> <td> : </td> <td ALIGN="right"> <?php echo number_format($row["pio_avg"] )                    ?> </td> </tr>  
      <tr> <td> exec_avg                      </td> <td> : </td> <td ALIGN="right"> <?php echo number_format($row["exec_avg"])                     ?> </td> </tr>  
      <tr> <td> elap_avg                      </td> <td> : </td> <td ALIGN="right"> <?php echo number_format($row["elap_avg"])                     ?> </td> </tr>  
  </table> </center>
</td>
</tr>
</table>
</DIV>
</DIV>
</DIV>



    <?php
	$query = "
	select distinct
          sequence,
          qtext
	from ".$ServerName."_QPMSQL  
	where dbname = '".$dbname."'
          and uid = ".$uid."
          and id = ".$id."
          and hashkey = ".$hashkey."
	order by 1";
	
	$result = sybase_query($query,$pid);
	if ($result==false){ 
		sybase_close($pid); 
		$pid=0;
		include ("../connectArchiveServer.php");	
		echo "<tr><td>Error</td></tr></table>";
		return(0);
	}
        ?>	
    <div class="boxinmain" style="max-width:1000px;float:none;">
    <div class="boxtop">
    <img src="../images/boxtop-corner-left.jpg" style="float:left;margin:0px"/>
    <div class="title" >Batch SQL</div>
    <img src="../images/boxtop-corner-right.jpg" style="float:right;margin:0px;"/>
    </div>
    <div class="boxcontent">
    <table width="100%" class="statMainTable" cellspacing=10 cellpadding=0> 
    <tr> <td>
    <table cellspacing=8 cellpadding=0 >
        <?php
	    $cntrows=0;
	    $sqltext="";
        while($row = sybase_fetch_array($result))
        {
//            echo  "<tr class='textInfo'>  <td class='textInfo'>".$row["LineNumber"]."&nbsp;</td><td class='textInfo'>".str_replace(" ","&nbsp;",$row["SQLText"])  ."</td> </tr> ";
            $sqltext = $sqltext.str_replace("\n","<BR>",$row['qtext']);
            $cntrows++;
        } 
        if ($cntrows == 0) {
            echo "<tr class='textInfo'> <td class='textInfoError'> No info available </td> </tr>";
        }
        else
            echo  "<tr class='textInfo'>  <td class='textInfo'>".$sqltext."</td> </tr> ";

        ?>
    </table>
    </td> </tr>
    </table>
    </DIV>
    </DIV>


</center>
</form>
</body>
