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

  
    if ( isset($_POST['rowcnt'])          ) $rowcnt           = $_POST['rowcnt'];           else $rowcnt           = 200;

  $TxnCreateTime = $_GET['TxnCreateTime'];
  $TxnID = $_GET['TxnID'];
  $IQconnID = $_GET['IQconnID'];
  $ConnHandle = $_GET['ConnHandle'];

?>

<title>Asemon Report - Transaction detail</title>

<script LANGUAGE="javascript">
var WindowObjectReference; // global variable

function urlencode(str) {
return escape(str).replace(/\+/g,'%2B').replace(/%20/g, '+').replace(/\*/g, '%2A').replace(/\//g, '%2F').replace(/@/g, '%40');
}

function getSql(w,h, query_name)
{
  //"resizable=yes,scrollbars=yes,menubar=yes,toolbar=yes,status=no");
  var winl = (screen.width - w) / 2;
  var wint = (screen.height - h) / 2;
  var query = document.getElementsByName(query_name)[0].value;
  WindowObjectReference = window.open("../show_sql.php?QUERY=" + urlencode(query),
    "SQLText",
    "scrollbars=no,status=no,location=no,toolbar=no,menubar=no,directories=no,resizable=no,width=" + w + ",height=" + h + ",top=" + wint + ",left=" + winl + "");
  //"resizable=yes,scrollbars=yes,menubar=yes,toolbar=yes,status=no");
  WindowObjectReference.focus();
}
	
</script>

</head>

<body>

  <form name="inputparam" method="POST" action="">
  <?php  
  $displaylevel=1;
  include ("../asemon_search_panel.php");
  ?>
  <INPUT type="HIDDEN" name="ARContextJSON" value='<?php echo $ARContextJSON;?>' >

  <center>

  <H1> IQ Transaction Detail </H1>


  <?php
  include ("sql/sql_IQTrans_detail.php");
  ?>
  <div class="boxinmain" style="max-width:1000px;float:none;">
  <div class="boxtop">
  <img src="../images/boxtop-corner-left.jpg" style="float:left;margin:0px"/>
  <div style="float:left; position: relative; top: 3px;"><?php include './export/export-table.php' ?></div>
  <div class="title">Transaction detail</div>
  <img src="../images/boxtop-corner-right.jpg" style="float:right;margin:0px;"/>
  </div>
  <div class="boxcontent">
  <div class="boxbtns" >
  <table align="left" cellspacing="2px" ><tr>
  <td>Max rows (0 = unlimited) :</td>
  <td>
  	<input type="text" name="rowcnt" value="<?php if( isset($rowcnt) ){ echo $rowcnt ; } ?>">
  </td>
  <td>
  	<img src="../images/button_sideLt.gif"  class="btn" height="20px" >
      <INPUT style="height:20px; " class="btn" type="submit" value="Refresh" name="RefreshStmt" >
      <img src="../images/button_sideRt.gif"  class="btn" height="20px">
  </td>
  </tr></table>
  </div>
  <div class="statMainTable" style="height:250px">
  <table cellspacing=2 cellpadding=4 >
  <tr> 
    <td class="statTabletitle" > Timestamp            </td>
    <td class="statTabletitle" > ConnOrCursor         </td>
    <td class="statTabletitle" > Name                 </td>
    <td class="statTabletitle" > Userid               </td>
    <td class="statTabletitle" > numIQCursors         </td>
    <td class="statTabletitle" > IQthreads            </td>
    <td class="statTabletitle" > ConnOrCurCreateTime  </td>
    <td class="statTabletitle" > IQGovernPriority     </td>
    <td class="statTabletitle" > CmdLine              </td>
  </tr>
  


  <?php
        //echo $query;
        $result = sybase_query("set rowcount ".$rowcnt."
                                       ".$query."
                                       set rowcount 0",
                                       $pid);                       
        if ($result==false){ 
        		sybase_close($pid); 
        		$pid=0;
        		include ("../connectArchiveServer.php");	
        		echo "<tr><td>Error</td></tr></table>";
        		return(0);
        }
	
	
	      $rw=0;
	      $cpt=0;
        while($row = sybase_fetch_array($result))
        {
			      $rw++;
            if($cpt==0)
                 $parite="impair";
            else
                 $parite="pair";
            ?>
            <tr class="statTable<?php echo $parite; ?>"  onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>  )';" >
			      <?php
			      $cpt=1-$cpt;
            ?>
            <td class="statTable" NOWRAP> <?php echo $row["Timestamp"] ?> </td> 
            <td class="statTable" >       <?php echo $row["ConnOrCursor"] ?> </td> 
            <td class="statTable" >       <?php echo $row["Name"] ?> </td> 
            <td class="statTable" >       <?php echo $row["Userid"] ?> </td> 
            <td class="statTable" >       <?php echo $row["numIQCursors"] ?> </td> 
            <td class="statTable" >       <?php echo $row["IQthreads"] ?> </td> 
            <td class="statTable" >       <?php echo $row["ConnOrCurCreateTime"] ?> </td> 
            <td class="statTable" >       <?php echo $row["IQGovernPriority"] ?> </td> 
            <td class="statTable" NOWRAP>       <?php echo $row["CmdLine"] ?> </td> 
            </tr> 
            <?php
        }
    ?>
  </table>
  </DIV>
  </DIV>
  </DIV>


  </center>



  </FORM>
</body>
