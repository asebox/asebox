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

  
  $ConnCreateTime = $_GET['ConnCreateTime'];
  $IQconnID = $_GET['IQconnID'];

?>

<title>Asemon Report - IQCnx detail</title>
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
	

function getStmtDetail(StmtID)
{
  ARContextJSON = document.inputparam.ARContextJSON.value;
  WindowObjectReference = window.open("IQ/statement_detail.php?StmtID="+StmtID+"&ARContextJSON="+ARContextJSON+"#top",
    "_blank");
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

   <H1> IQ Connection Detail </H1>


   <?php


    // Check if IQCnx table has Hostname, Programname, ClientLibrary fields (added in asemon_logger V2.6.4)
    $result = sybase_query("select cnt=count(*)
                                from dbo.syscolumns where id=object_id('".$ServerName."_IQCnx') and name in ('Hostname', 'Programname', 'ClientLibrary')"
                                , $pid);
    $row = sybase_fetch_array($result);
    if ( $row["cnt"] > 0 ) {
            $host_selclause = ",Hostname ";
    }
    else {
            $host_selclause = "";
    }


        // get cnx statistics 
	$query = "select 
             ConnHandle,
             Name,
             Userid,
             CLink=substring(CommLink,1,30),
             NodeAddr=max(NodeAddr),
             maxIQThreads=max(IQthreads),
             maxTempTableSpaceKB=max(TempTableSpaceKB),
             maxTempWorkSpaceKB=max(TempWorkSpaceKB),
             sumSatoiq_count=sum(1.*satoiq_count),
             sumIqtosa_count=sum(1.*iqtosa_count)
             ".$host_selclause."/*,
             Programname,
             ClientLibrary*/
         from ".$ServerName."_IQCnx
         where IQconnID=".$IQconnID."
          and ConnCreateTime='".$ConnCreateTime."'
          and Timestamp >= '".$StartTimestamp."'
          and Timestamp <= '".$EndTimestamp."' 
/*         group by IQconnID,ConnCreateTime,ConnHandle,Name,Userid,Hostname,Programname,ClientLibrary, substring(CommLink,1,30)*/
         group by IQconnID,ConnCreateTime,ConnHandle,Name,Userid".$host_selclause."/*,Programname,ClientLibrary*/, substring(CommLink,1,30)";


        //echo $query;	
	$result = sybase_query($query,$pid);
	if ($result==false){ 
		sybase_close($pid); 
		$pid=0;
		include ("../connectArchiveServer.php");	
		echo "Error";
		return(0);
	}

	$rw=0;
	$cpt=0;
        $row = sybase_fetch_array($result);
        
        $ConnHandle = $row["ConnHandle"];
        $Name = $row["Name"];
	$Userid = $row["Userid"];
   ?>



<div class="boxinmain" style="max-width:1000px;float:none">
<div class="boxtop">
<img src="../images/boxtop-corner-left.jpg" style="float:left;margin:0px"/>
<div class="title" style="width:65%">Connection Statistics</div>
<img src="../images/boxtop-corner-right.jpg" style="float:right;margin:0px;"/>
</div>

<div class="boxcontent">

<div class="statMainTable" style="overflow-y:visible">

<div class="statMainInfo">
<table cellspacing="2" cellpadding="4" class="infobox">
<tr class="infobox">
<td class="infobox">
<b class="newsparatitle">Process Info :</b><br>
</td>
<td class="infobox">
<b class="newsparatitle">Process Statistics :</b><br>
</td>
</tr>
<tr>
<td valign="top" > <center>
  <table border="0" cellspacing="2" cellpadding="0" class="statInfo" >
      <tr> <td> ConnCreateTime       </td> <td> : </td> <td> <?php echo $ConnCreateTime ?> </td> </tr>
      <tr> <td> IQconnID             </td> <td> : </td> <td> <?php echo $IQconnID ?> </td> </tr>
      <tr> <td> ConnHandle           </td> <td> : </td> <td> <?php echo $ConnHandle ?> </td> </tr>         
      <tr> <td> Name                 </td> <td> : </td> <td> <?php echo $Name ?> </td> </tr>               
      <tr> <td> Userid               </td> <td> : </td> <td> <?php echo $Userid ?> </td> </tr>             
      <tr> <td> CommLink             </td> <td> : </td> <td> <?php echo $row["CLink"] ?> </td> </tr>           
      <tr> <td> NodeAddr             </td> <td> : </td> <td> <?php echo $row["NodeAddr"] ?> </td> </tr>           
      <tr> <td> Host                 </td> <td> : </td> <td> <?php echo $row["Hostname"] ?> </td> </tr>           
      <tr> <td> Program 	     </td> <td> : </td> <td> <?php echo $row["Programname"] ?> </td> </tr>           
      <tr> <td> Client Library 	     </td> <td> : </td> <td> <?php echo $row["ClientLibrary"] ?> </td> </tr> 
  </table></center>
</td>
<td valign="top" > <center>
  <table border="0" cellspacing="2" cellpadding="0" class="statInfo">
      <tr> <td> maxIQThreads         </td> <td> : </td> <td> <?php echo $row["maxIQThreads"] ?> </td> </tr>       
      <tr> <td> maxTempTableSpaceKB  </td> <td> : </td> <td> <?php echo $row["maxTempTableSpaceKB"] ?> </td> </tr>
      <tr> <td> maxTempWorkSpaceKB   </td> <td> : </td> <td> <?php echo $row["maxTempWorkSpaceKB"] ?> </td> </tr> 
      <tr> <td> sumSatoiq_count      </td> <td> : </td> <td> <?php echo $row["sumSatoiq_count"] ?> </td> </tr>    
      <tr> <td> sumIqtosa_count      </td> <td> : </td> <td> <?php echo $row["sumIqtosa_count"] ?> </td> </tr>    
  </table> </center>
</td>
</tr>
</table>
</DIV>
</DIV>
</DIV>
</DIV>




<p>
   <img src='<?php echo "./graphIQCnx_SaIq.php?ConnCreateTime=".urlencode($ConnCreateTime)."&IQconnID=".urlencode($IQconnID)."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
</p>

<p>
   <img src='<?php echo "./graphIQCnx_VersRelease.php?IQconnID=".urlencode($IQconnID)."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
</p>
<p>
   <img src='<?php echo "./graphIQCnx_Commits.php?ConnCreateTime=".urlencode($ConnCreateTime)."&IQconnID=".urlencode($IQconnID)."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
</p>
<p>
   <img src='<?php echo "./graphIQCnx_TempUse.php?ConnCreateTime=".urlencode($ConnCreateTime)."&IQconnID=".urlencode($IQconnID)."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
</p>






<center>
<?php
    if ( isset($_POST['orderTxn'])        ) $orderTxn         = $_POST['orderTxn'];         else $orderTxn         = "A.TxnCreateTime";
    if ( isset($_POST['rowcnt'])          ) $rowcnt           = $_POST['rowcnt'];           else $rowcnt           = 200;
    if ( isset($_POST['filterTxnID'])     ) $filterTxnID      = $_POST['filterTxnID'];      else $filterTxnID      = "";
    if ( isset($_POST['filterstatus'])    ) $filterstatus     = $_POST['filterstatus'];     else $filterstatus     = "";
    include ("sql/sql_IQTrans_statistics.php");
?>


        <div class="boxinmain" style="max-width:1000px;float:none;">
        <div class="boxtop">
        <img src="../images/boxtop-corner-left.jpg" style="float:left;margin:0px"/>
        <div style="float:left; position: relative; top: 3px;"><?php include './export/export-table.php' ?></div>
        <div class="title">Transactions</div>
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
        <td>
                <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; order by : ".$orderTxn; ?>
        </td>
        </tr></table>
        </div>
        <div class="statMainTable" style="height:250px">
        <table cellspacing=2 cellpadding=4>
    <tr> 
      <td class="statTabletitle" > TxnCreateTime       </td>         
      <td class="statTabletitle" > TxnID               </td>           
      <td class="statTabletitle" > Status              </td>          
      <td class="statTabletitle" > CmtID               </td>               
      <td class="statTabletitle" > MaxMainTableKBCr    </td>           
      <td class="statTabletitle" > MaxMainTableKBDr    </td>          
      <td class="statTabletitle" > MaxTempTableKBCr    </td>        
      <td class="statTabletitle" > MaxTempTableKBDr    </td>        
      <td class="statTabletitle" > MaxTempWorkSpaceKB  </td>
    </tr>
    <tr>  
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderTxn"  VALUE="A.TxnCreateTime        " <?php if ($orderTxn=="A.TxnCreateTime        ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderTxn"  VALUE="TxnID                  " <?php if ($orderTxn=="TxnID                  ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderTxn"  VALUE="status                 " <?php if ($orderTxn=="status                 ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderTxn"  VALUE="CmtID                  " <?php if ($orderTxn=="CmtID                  ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderTxn"  VALUE="MaxMainTableKBCr   DESC" <?php if ($orderTxn=="MaxMainTableKBCr   DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderTxn"  VALUE="MaxMainTableKBDr   DESC" <?php if ($orderTxn=="MaxMainTableKBDr   DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderTxn"  VALUE="MaxTempTableKBCr   DESC" <?php if ($orderTxn=="MaxTempTableKBCr   DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderTxn"  VALUE="MaxTempTableKBDr   DESC" <?php if ($orderTxn=="MaxTempTableKBDr   DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderTxn"  VALUE="MaxTempWorkSpaceKB DESC" <?php if ($orderTxn=="MaxTempWorkSpaceKB DESC") echo "CHECKED"; ?> > </td>



    </tr>
    <tr> 
      <td></td> 
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterTxnID"  value="<?php if( isset($filterTxnID) ){ echo $filterTxnID ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterstatus"  value="<?php if( isset($filterstatus) ){ echo $filterstatus ; } ?>" > </td>
      <td></td> 
      <td></td> 
      <td></td> 
      <td></td> 
      <td></td> 
      <td></td> 
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
            <tr class="statTable<?php echo $parite; ?>"  onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';" Onclick='javascript:getTransDetail("<?php echo $row["TxnCreateTime"]?>","<?php echo $row["TxnID"]?>","<?php echo $StartTimestamp?>","<?php echo $EndTimestamp?>" )' >
			<?php
			$cpt=1-$cpt;
?>
    <td class="statTable" NOWRAP> <?php echo $row["TxnCreateTime"] ?> </td> 
    <td class="statTable" >       <?php echo $row["TxnID"] ?> </td> 
    <td class="statTable" >       <?php echo $row["status"] ?> </td> 
    <td class="statTable" >       <?php echo $row["CmtID"] ?> </td> 
    <td class="statTable" >       <?php echo $row["MaxMainTableKBCr"] ?> </td> 
    <td class="statTable" >       <?php echo $row["MaxMainTableKBDr"] ?> </td> 
    <td class="statTable" >       <?php echo $row["MaxTempTableKBCr"] ?> </td> 
    <td class="statTable" >       <?php echo $row["MaxTempTableKBDr"] ?> </td> 
    <td class="statTable" >       <?php echo $row["MaxTempWorkSpaceKB"] ?> </td> 
    <td class="statTable" >       <?php echo $row["IQconnID"] ?> </td> 
    <td class="statTable" >       <?php echo $row["ConnHandle"] ?> </td> 
    <td class="statTable" >       <?php echo $row["Name"] ?> </td> 
    <td class="statTable" >       <?php echo $row["Userid"] ?> </td> 
    </tr> 
    <?php
        }
?>
</table>
</DIV>
</DIV>
</DIV>


<?php
    if ( isset($_POST['orderTxn'])        ) $orderTxn         = $_POST['orderTxn'];         else $orderTxn         = "TxnCreateTime";
    if ( isset($_POST['rowcnt'])          ) $rowcnt           = $_POST['rowcnt'];           else $rowcnt           = 200;
    if ( isset($_POST['filterTxnID'])     ) $filterTxnID      = $_POST['filterTxnID'];      else $filterTxnID      = "";
    if ( isset($_POST['filterstatus'])    ) $filterstatus     = $_POST['filterstatus'];     else $filterstatus     = "";
    include ("sql/sql_IQCtx_statistics.php");
?>


        <div class="boxinmain" style="max-width:1000px;float:none;">
        <div class="boxtop">
        <img src="../images/boxtop-corner-left.jpg" style="float:left;margin:0px"/>
        <div style="float:left; position: relative; top: 3px;"><?php include './export/export-table.php' ?></div>
        <div class="title">Connection activity</div>
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
        <table cellspacing=2 cellpadding=4>
    <tr> 
      <td class="statTabletitle" > Timestamp            </td>         
      <td class="statTabletitle" > Interval             </td>           
      <td class="statTabletitle" > TxnID                </td>          
      <td class="statTabletitle" > LastReqTime          </td>               
      <td class="statTabletitle" > ReqType              </td>           
      <td class="statTabletitle" > IQCmdType            </td>          
      <td class="statTabletitle" > LastIQCmdTime        </td>        
      <td class="statTabletitle" > IQCursors            </td>        
      <td class="statTabletitle" > LowestIQCursorState  </td>
      <td class="statTabletitle" > IQthreads            </td>
      <td class="statTabletitle" > TempTableSpaceKB     </td>
      <td class="statTabletitle" > TempWorkSpaceKB      </td>
      <td class="statTabletitle" > satoiq_count         </td>
      <td class="statTabletitle" > iqtosa_count         </td>
      <td class="statTabletitle" > ConnOrCursor         </td>
      <td class="statTabletitle" > numIQCursors         </td>
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
            <tr class="statTable<?php echo $parite; ?>"  onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';" >
            <?php

			$cpt=1-$cpt;
?>
    <td class="statTable" NOWRAP> <?php echo $row["Timestamp"] ?>  </td> 
    <td class="statTable" NOWRAP> <?php echo $row["Interval"] ?>  </td>
    <td class="statTable" NOWRAP> <?php echo $row["TxnID"] ?>  </td>
    <td class="statTable" NOWRAP> <?php echo $row["LastReqTime"] ?>  </td>
    <td class="statTable" NOWRAP> <?php echo $row["ReqType"] ?>  </td>
    <td class="statTable" NOWRAP> <?php echo $row["IQCmdType"] ?>  </td>
    <td class="statTable" NOWRAP> <?php echo $row["LastIQCmdTime"] ?>  </td>
    <td class="statTable" NOWRAP> <?php echo $row["IQCursors"] ?>  </td>
    <td class="statTable" NOWRAP> <?php echo $row["LowestIQCursorState"] ?>  </td>
    <td class="statTable" NOWRAP> <?php echo $row["IQthreads"] ?>  </td>
    <td class="statTable" NOWRAP> <?php echo $row["TempTableSpaceKB"] ?>  </td>
    <td class="statTable" NOWRAP> <?php echo $row["TempWorkSpaceKB"] ?>  </td>
    <td class="statTable" NOWRAP> <?php echo $row["satoiq_count"] ?>  </td>
    <td class="statTable" NOWRAP> <?php echo $row["iqtosa_count"] ?>  </td>
    <td class="statTable" NOWRAP> <?php echo $row["ConnOrCursor"] ?>  </td>
    <td class="statTable" NOWRAP> <?php echo $row["numIQCursors"] ?>  </td>
    <td class="statTable" NOWRAP> <?php echo $row["ConnOrCurCreateTime"] ?>  </td>
    <td class="statTable" NOWRAP> <?php echo $row["IQGovernPriority"] ?>  </td>
    <td class="statTable" NOWRAP> <?php echo $row["CmdLine"] ?>  </td>
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
