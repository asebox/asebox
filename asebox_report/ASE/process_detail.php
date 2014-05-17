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


    $Loggedindatetime = $_GET['Loggedindatetime'];
    $Spid = $_GET['Spid'];

    if ($Loggedindatetime == "") {
        //echo "no loggedindatetime";
        // search loggedindatetime of this spid
        $StartTimestamp= $_GET['StartTimestamp'];
        $EndTimestamp=   $_GET['EndTimestamp'];
        $query = "select Loggedindatetime=convert(varchar,max(Loggedindatetime),109), TsDiff=datediff(ss,'".$StartTimestamp."', '".$EndTimestamp."') from ".$ServerName."_Cnx where Loggedindatetime<='".$StartTimestamp."' and Spid=".$Spid;
        //echo $query;
        $result = sybase_query($query,$pid);
        $row = sybase_fetch_array($result);
        $Loggedindatetime = $row['Loggedindatetime'];
        $TsDiff=$row['TsDiff'];
        if ($TsDiff < 60) {
            $result = sybase_query("select EndTimestamp=convert(varchar,dateadd(ss,60,'".$StartTimestamp."'),109)",$pid);
            $row = sybase_fetch_array($result);
            $EndTimestamp= $row['EndTimestamp'];
            //echo $EndTimestamp;
        }
    }

    $title = $ServerName."-Process Detail";
    ?>


    <title> <?php echo $title ?> </title>

<script type="text/javascript">
var WindowObjectReference; // global variable

function getStmtDetail(StmtID)
{
  ARContextJSON = document.inputparam.ARContextJSON.value;
  WindowObjectReference = window.open("./statement_detail.php?StmtID="+StmtID+"&ARContextJSON="+ARContextJSON+"#top",
    "_blank");
  WindowObjectReference.focus();
}
</script>



</head>

<body>
  <form name="inputparam" method="POST" action="">
  <?php  
  $displaylevel=1;
  include ("../compare_search_panel.php");
  ?>
  <INPUT type="HIDDEN" name="ARContextJSON" value='<?php echo $ARContextJSON;?>' >

   <center>

   <?php


        // Check if CnxActiv has tmp_pages fields and Cnx has tempdbid and tempdbname (added in asemon_logger V2.3.4)
        $result = sybase_query("select cnt=count(*)
                                from (
                                select name from syscolumns where id=object_id('".$ServerName."_Cnx') and name in ('tempdbid','tempdbname')
                                union all
                                select name from syscolumns where id=object_id('".$ServerName."_CnxActiv') and name in ('tmp_pages')
                                ) x"
                                , $pid);
	if ($result==false){ 
		sybase_close($pid); 
		$pid=0;
		include ("../connectArchiveServer.php");	
		echo "<tr><td>Error</td></tr></table>";
		return(0);
	}
        $row = sybase_fetch_array($result);
        if ( $row["cnt"] == 3 )
                $dbid_tmp_pages_exists = 1;
        else
                $dbid_tmp_pages_exists = 0;


        if ($dbid_tmp_pages_exists == 1) {
           $sel_tempdb_clause = ", tempdbid, tempdbname, Maxtmp_pages=max(tmp_pages)";
           $grp_tempdb_clause = ", tempdbid, tempdbname";
        }
        else {
           $sel_tempdb_clause = "";
           $grp_tempdb_clause = "";
        }

        // get process statistics 
	$query = 
	"select 
          C.Loggedindatetime,
          C.Kpid,
          C.Spid,
          UserName,
          program_name,
          DBName,
          execlass,
          ipaddr,
          hostname,
          hostprocess,
          clientname,
          clienthostname,
          clientapplname,
          CPUTime=sum(CPUTime),
          WaitTime=max(WaitTime)-min(WaitTime),
          LogicalReads=sum(convert(float,LogicalReads)),
          PhysicalReads=sum(convert(float,PhysicalReads)),
          PagesRead=sum(convert(float,PagesRead)),
          PhysicalWrites=sum(convert(float,PhysicalWrites)),
          PagesWritten=sum(convert(float,PagesWritten)),
          ScanPgs=sum(convert(float,ScanPgs)),
          IdxPgs=sum(convert(float,IdxPgs)),
          TmpTbl=sum(TmpTbl),
          UlcBytWrite=sum(convert(float,UlcBytWrite)),
          UlcFlush=sum(UlcFlush),
          ULCFlushFull=sum(ULCFlushFull),
          Transactions=sum(Transactions),
          Commits=sum(Commits),
          Rollbacks=sum(Rollbacks),
          PacketsSent=sum(PacketsSent),
          PacketsReceived=sum(PacketsReceived),
          BytesSent=sum(convert(float,BytesSent)),
          BytesReceived=sum(convert(float,BytesReceived)),
          MaxLocksHeld=max(LocksHeld),
          MaxMemusageKB=max(memusage)*2
          ".$sel_tempdb_clause."
         from ".$ServerName."_Cnx C, ".$ServerName."_CnxActiv A   
         where C.Spid=".$Spid."
          and C.Loggedindatetime between dateadd(ss,-1,'".$Loggedindatetime."') and dateadd(ss,+1,'".$Loggedindatetime."') -- added between because IQ has more precision on timestamps
          and C.Loggedindatetime=A.Loggedindatetime
          and C.Spid=A.Spid
          and C.Kpid=A.Kpid
          and Timestamp >= '".$StartTimestamp."'
          and Timestamp <= '".$EndTimestamp."' 
         group by
          C.Loggedindatetime,
          C.Kpid,
          C.Spid,
          UserName,
          program_name,
          DBName,
          execlass,
          ipaddr,
          hostname,
          hostprocess,
          clientname,
          clienthostname,
          clientapplname
          ".$grp_tempdb_clause;


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
      if ($row == null) {
          echo "No data for this connection";
          return;
      }
      $Kpid=$row["Kpid"];
   ?>



<?php /* Process INFO */ ?>


<div class="boxinmain" style="max-width:1000px;float:none">
<div class="boxtop" >
<div class="title" >Process Statistics </div>
</div>

<div class="boxcontent" >

<div class="statMainTable" style="overflow-x:visible">

<div class="statMainInfo" >
<table cellspacing=0 cellpadding=0 >
<tr class="infobox">
<td class="infobox">
<b class="newsparatitle">Process Info :</b><br>
</td>
<td class="infobox">
<b class="newsparatitle">Process Statistics :</b><br>
</td>
</tr>
<tr>
<td class="infobox" valign="top" > <center>
  <table border="0" cellspacing="2" cellpadding="0" class="statInfo" >
      <tr> <td> Loggedindatetime</td> <td> : </td> <td> <?php echo $Loggedindatetime ?> </td> </tr>
      <tr> <td> Kpid            </td> <td> : </td> <td> <?php echo $row["Kpid"] ?> </td> </tr>
      <tr> <td> Spid            </td> <td> : </td> <td> <?php echo $row["Spid"] ?> </td> </tr>
      <tr> <td> UserName        </td> <td> : </td> <td> <?php echo $row["UserName"] ?> </td> </tr>
      <tr> <td> program_name    </td> <td> : </td> <td> <?php echo $row["program_name"] ?> </td> </tr>
      <tr> <td> DBName          </td> <td> : </td> <td> <?php echo $row["DBName"] ?> </td> </tr>
      <tr> <td> execlass        </td> <td> : </td> <td> <?php echo $row["execlass"] ?> </td> </tr>
      <tr> <td> ipaddr          </td> <td> : </td> <td> <?php echo $row["ipaddr"] ?> </td> </tr>
      <tr> <td> hostname        </td> <td> : </td> <td> <?php echo $row["hostname"] ?> </td> </tr>
      <tr> <td> hostprocess     </td> <td> : </td> <td> <?php echo $row["hostprocess"] ?> </td> </tr>
      <tr> <td> clientname      </td> <td> : </td> <td> <?php echo $row["clientname"] ?> </td> </tr>
      <tr> <td> clienthostname  </td> <td> : </td> <td> <?php echo $row["clienthostname"] ?> </td> </tr>
      <tr> <td> clientapplname  </td> <td> : </td> <td> <?php echo $row["clientapplname"] ?> </td> </tr>
      <?php
      if ($dbid_tmp_pages_exists == 1) {
      ?>
      <tr> <td> Tempdb_id  </td> <td> : </td> <td> <?php echo $row["tempdbid"] ?> </td> </tr>
      <tr> <td> Tempdb_name  </td> <td> : </td> <td> <?php echo $row["tempdbname"] ?> </td> </tr>      
      <?php
      }
      ?>
      
  </table></center>
</td>
<td class="infobox" valign="top" > <center>
  <table border="0" cellspacing="1" cellpadding="0" class="statInfo">
      <TR> <TD> CPUTime_ms        </TD> <td> : </td> <td ALIGN="right"> <?php echo number_format($row["CPUTime"]        ) ?> </td> </tr>
      <TR> <TD> WaitTime_ms       </TD> <td> : </td> <td ALIGN="right"> <?php echo number_format($row["WaitTime"]       ) ?> </td> </tr>
      <TR> <TD> LogicalReads      </TD> <td> : </td> <td ALIGN="right"> <?php echo number_format($row["LogicalReads"]   ) ?> </td> </tr>
      <TR> <TD> PhysicalReads     </TD> <td> : </td> <td ALIGN="right"> <?php echo number_format($row["PhysicalReads"]  ) ?> </td> </tr>
      <TR> <TD> PagesRead         </TD> <td> : </td> <td ALIGN="right"> <?php echo number_format($row["PagesRead"]      ) ?> </td> </tr>
      <TR> <TD> PhysicalWrites    </TD> <td> : </td> <td ALIGN="right"> <?php echo number_format($row["PhysicalWrites"] ) ?> </td> </tr>
      <TR> <TD> PagesWritten      </TD> <td> : </td> <td ALIGN="right"> <?php echo number_format($row["PagesWritten"]   ) ?> </td> </tr>
      <TR> <TD> ScanPgs           </TD> <td> : </td> <td ALIGN="right"> <?php echo number_format($row["ScanPgs"]        ) ?> </td> </tr>
      <TR> <TD> IdxPgs            </TD> <td> : </td> <td ALIGN="right"> <?php echo number_format($row["IdxPgs"]         ) ?> </td> </tr>
      <TR> <TD> TmpTbl            </TD> <td> : </td> <td ALIGN="right"> <?php echo number_format($row["TmpTbl"]         ) ?> </td> </tr>
      <TR> <TD> UlcBytWrite       </TD> <td> : </td> <td ALIGN="right"> <?php echo number_format($row["UlcBytWrite"]    ) ?> </td> </tr>
      <TR> <TD> UlcFlush          </TD> <td> : </td> <td ALIGN="right"> <?php echo number_format($row["UlcFlush"]       ) ?> </td> </tr>
      <TR> <TD> ULCFlushFull      </TD> <td> : </td> <td ALIGN="right"> <?php echo number_format($row["ULCFlushFull"]   ) ?> </td> </tr>
      <TR> <TD> Transactions      </TD> <td> : </td> <td ALIGN="right"> <?php echo number_format($row["Transactions"]   ) ?> </td> </tr>
      <TR> <TD> Commits           </TD> <td> : </td> <td ALIGN="right"> <?php echo number_format($row["Commits"]        ) ?> </td> </tr>
      <TR> <TD> Rollbacks         </TD> <td> : </td> <td ALIGN="right"> <?php echo number_format($row["Rollbacks"]      ) ?> </td> </tr>
      <TR> <TD> PacketsSent       </TD> <td> : </td> <td ALIGN="right"> <?php echo number_format($row["PacketsSent"]    ) ?> </td> </tr>
      <TR> <TD> PacketsReceived   </TD> <td> : </td> <td ALIGN="right"> <?php echo number_format($row["PacketsReceived"]) ?> </td> </tr>
      <TR> <TD> BytesSent         </TD> <td> : </td> <td ALIGN="right"> <?php echo number_format($row["BytesSent"]      ) ?> </td> </tr>
      <TR> <TD> BytesReceived     </TD> <td> : </td> <td ALIGN="right"> <?php echo number_format($row["BytesReceived"]  ) ?> </td> </tr>
      <TR> <TD> MaxLocksHeld      </TD> <td> : </td> <td ALIGN="right"> <?php echo number_format($row["MaxLocksHeld"]   ) ?> </td> </tr>
      <TR> <TD> MaxMemusageKB     </TD> <td> : </td> <td ALIGN="right"> <?php echo number_format($row["MaxMemusageKB"]  ) ?> </td> </tr>
      <?php
      // save CPUTime. Used latter in elasped time graph 
      $CPUTime = $row["CPUTime"];
      if ($dbid_tmp_pages_exists == 1) {
      ?>
      <tr> <td> Maxtmp_pages    </td> <td> : </td> <td ALIGN="right"> <?php echo number_format($row["Maxtmp_pages"]) ?> </td> </tr>
      <?php
      }
      ?>
  </table> </center>
</td>
</tr>
</table>
</DIV>
</DIV>
</DIV>
</DIV>











<p>
   <img src='<?php echo "./graphLReadscnx.php?Loggedindatetime=".urlencode($Loggedindatetime)."&Spid=".urlencode($Spid)."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
</p>


<p>
   <img src='<?php echo "./graphIOcnx.php?Loggedindatetime=".urlencode($Loggedindatetime)."&Spid=".urlencode($Spid)."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
</p>

<p>
   <img src='<?php echo "./graphNetcnx.php?Loggedindatetime=".urlencode($Loggedindatetime)."&Spid=".urlencode($Spid)."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
</p>
<?php
if ($dbid_tmp_pages_exists == 1) {
?>
    <p>
       <img src='<?php echo "./graphTmpPages_cnx.php?Loggedindatetime=".urlencode($Loggedindatetime)."&Spid=".urlencode($Spid)."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
    </p>
<?php
}
?>
<p>
   <img src='<?php echo "./graphLocksHeld_cnx.php?Loggedindatetime=".urlencode($Loggedindatetime)."&Spid=".urlencode($Spid)."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
</p>









    <?php /* Statements */ ?>


    <?php
    // Check if table xxxx_StmtStat exists
    $query = "select id from sysobjects where name ='".$ServerName."_StmtStat'";
	$result = sybase_query($query,$pid);
	if ($result==false){ 
		sybase_close($pid); 
		$pid=0;
		include ("../connectArchiveServer.php");	
		echo "<tr><td>Error</td></tr></table>";
		return(0);
	}
	$rw=0;
        while($row = sybase_fetch_array($result))
        {
	  $rw++;
	}	


    if ($rw == 1)
    {
    	// Table StmtStat exists, get statements
	    $param_list=array(
	    	'orderStmt',
	    	'rowcnt',
	    	'ProcName'
	    );
	    foreach ($param_list as $param)
	    	@$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];
        
           if ( !isset($orderStmt) ) $orderStmt="StmtID";
           if ( !isset($rowcnt) ) $rowcnt=200;
           if ( !isset($ProcName) ) $ProcName="";

        $query = "set rowcount ".$rowcnt."
        select 
        StmtID,
            Debut=convert(varchar,StartTime,109),
            Elapsed_s=datediff(ss,StartTime,EndTime),
            SPID,
            DBID,
            ProcName,
            LineNumber,
            CpuTime,
            WaitTime,
            MemUsageKB,
            PhysicalReads,
            LogicalReads,
            PagesModified,
            PacketsSent,
            PacketsReceived
        from ".$ServerName."_StmtStat   
        where SPID=".$Spid."
        and KPID=".$Kpid."
        and StartTime >='".$StartTimestamp."'        
        and StartTime <'".$EndTimestamp."' 
        and (ProcName like '%".$ProcName."%' or ProcName is null)
        order by ".$orderStmt."
        set rowcount 0";
        $query_name="stmt_query";
        //echo $query;
        ?>
        
        <div class="boxinmain" style="max-width:1000px;float:none;">
        <div class="boxtop">
        <img src="../images/boxtop-corner-left.jpg" style="float:left;margin:0px"/>
        <div style="float:left; position: relative; top: 3px;"><?php include '../export/export-table.php' ?></div>
        <div class="title">Statements</div>
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
        				Procedure Name:
        				<input type="text" name="ProcName" value="<?php if( isset($ProcName) ){ echo $ProcName ; } ?>">
        </td>
        <td>
        	<img src="../images/button_sideLt.gif"  class="btn" height="20px" >
            <INPUT style="height:20px; " class="btn" type="submit" value="Refresh" name="RefreshStmt" >
            <img src="../images/button_sideRt.gif"  class="btn" height="20px">
        </td>
        <td>
                <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; order by : ".$orderStmt; ?>
        </td>
        </tr></table>
        </div>
        <div class="statMainTable" style="height:250px">
        <table cellspacing=2 cellpadding=4>
        <tr> 
          <td  class="statTabletitle"> StmtID </td>
          <td  class="statTabletitle"> StartTime</td>
          <td  class="statTabletitle"> Elapsed_s </td>
          <td  class="statTabletitle"> SPID </td>
          <td  class="statTabletitle"> DBID </td>
          <td  class="statTabletitle"> Proc </td>
          <td  class="statTabletitle"> Line </td>
          <td  class="statTabletitle"> CpuTime_ms </td>
          <td  class="statTabletitle"> WaitTime_ms </td>
          <td  class="statTabletitle"> MemUsageKB </td>
          <td  class="statTabletitle"> PReads </td>
          <td  class="statTabletitle"> LReads </td>
          <td  class="statTabletitle"> PgsModified </td>
          <td  class="statTabletitle"> PktsSent </td>
          <td  class="statTabletitle"> PktsRcved </td>
        </tr>
        <tr>   
          <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderStmt"  VALUE="StmtID"      <?php if ($orderStmt=="StmtID")      echo "CHECKED";  ?> > </td>
          <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderStmt"  VALUE="StartTime"   <?php if ($orderStmt=="StartTime")   echo "CHECKED";  ?> > </td>
          <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderStmt"  VALUE="Elapsed_s DESC"   <?php if ($orderStmt=="Elapsed_s DESC")   echo "CHECKED";  ?> > </td>
          <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderStmt"  VALUE="SPID"        <?php if ($orderStmt=="SPID")        echo "CHECKED";  ?> > </td>
          <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderStmt"  VALUE="DBID"        <?php if ($orderStmt=="DBID")        echo "CHECKED";  ?> > </td>
          <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderStmt"  VALUE="ProcName"        <?php if ($orderStmt=="ProcName")        echo "CHECKED";  ?> > </td>
          <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderStmt"  VALUE="LineNumber"        <?php if ($orderStmt=="LineNumber")        echo "CHECKED";  ?> > </td>
          <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderStmt"  VALUE="CpuTime DESC"     <?php if ($orderStmt=="CpuTime DESC")     echo "CHECKED";  ?> > </td>
          <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderStmt"  VALUE="WaitTime DESC"    <?php if ($orderStmt=="WaitTime DESC")    echo "CHECKED";  ?> > </td>
          <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderStmt"  VALUE="MemUsageKB DESC"  <?php if ($orderStmt=="MemUsageKB DESC")  echo "CHECKED";  ?> > </td>
          <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderStmt"  VALUE="PhysicalReads DESC"      <?php if ($orderStmt=="PhysicalReads DESC")      echo "CHECKED";  ?> > </td>
          <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderStmt"  VALUE="LogicalReads DESC"      <?php if ($orderStmt=="LogicalReads DESC")      echo "CHECKED";  ?> > </td>
          <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderStmt"  VALUE="PagesModified DESC" <?php if ($orderStmt=="PagesModified DESC") echo "CHECKED";  ?> > </td>
          <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderStmt"  VALUE="PacketsSent DESC"    <?php if ($orderStmt=="PacketsSent DESC")    echo "CHECKED";  ?> > </td>
          <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderStmt"  VALUE="PacketsReceived DESC"   <?php if ($orderStmt=="PacketsReceived DESC")   echo "CHECKED";  ?> > </td>
        </tr>
        <?php
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
            while($row = sybase_fetch_array($result))
            {
        		$rw++;
                if($cpt==0)
                     $parite="impair";
                else
                     $parite="pair";
                ?>
                <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';" Onclick='javascript:getStmtDetail("<?php echo $row["StmtID"]?>" )'>
        		<?php
        		$cpt=1-$cpt;
                ?>
                <td class="statTablePtr"> <?php echo $row["StmtID"] ?> </td>
                <td class="statTablePtr" NOWRAP> <?php echo $row["Debut"] ?>  </td>
                <td class="statTablePtr"> <?php echo number_format($row["Elapsed_s"]) ?>  </td>
                <td class="statTablePtr"> <?php echo $row["SPID"] ?>  </td>
                <td class="statTablePtr"> <?php echo $row["DBID"] ?>  </td>
                <td class="statTablePtr" NOWRAP> <?php echo $row["ProcName"] ?>  </td>
                <td class="statTablePtr"> <?php echo $row["LineNumber"] ?>  </td>
                <td class="statTablePtr"> <?php echo number_format($row["CpuTime"]        ) ?>  </td>
                <td class="statTablePtr"> <?php echo number_format($row["WaitTime"]       ) ?>  </td>
                <td class="statTablePtr"> <?php echo number_format($row["MemUsageKB"]     ) ?>  </td>
                <td class="statTablePtr"> <?php echo number_format($row["PhysicalReads"]  ) ?>  </td>
                <td class="statTablePtr"> <?php echo number_format($row["LogicalReads"]   ) ?>  </td>
                <td class="statTablePtr"> <?php echo number_format($row["PagesModified"]  ) ?>  </td>
                <td class="statTablePtr"> <?php echo number_format($row["PacketsSent"]    ) ?>  </td>
                <td class="statTablePtr"> <?php echo number_format($row["PacketsReceived"]) ?>  </td>
                </tr> 
                <?php
            }
        ?>
        </table>
        </DIV>
        </DIV>
        </DIV>

    <?php
	}  // End IF table StmtStat exists
	?>











    <?php /* Used procedures */ ?>
    
    <?php // Select used procs
    $query = "
    select ts=convert(varchar,Timestamp,109), proc_name, linenum, tran_name, LocksHeld, memusageKB=memusage*2
    from ".$ServerName."_CnxActiv   
    where Spid=".$Spid."
    and Kpid=".$Kpid."
    and Loggedindatetime='".$Loggedindatetime."'
    and Timestamp >='".$StartTimestamp."'        
    and Timestamp <'".$EndTimestamp."' 
    and proc_name is not null
    order by Timestamp";
    $query_name="proc_query";
    ?>
    
    <div class="boxinmain" style="min-width:800px;float:none;"">
    <div class="boxtop">
    <img src="../images/boxtop-corner-left.jpg" style="float:left;margin:0px"/>
    <div style="float:left; position: relative; top: 3px;"><?php include '../export/export-table.php' ?></div>
    <div class="title" style="width:80%">Procedures captured for this connection </div>
    <img src="../images/boxtop-corner-right.jpg" style="float:right;margin:0px;"/>
    </div>
    
    <div class="boxcontent">
    
    <div class="statMainTable" style="height:250px";overflow-x:visible">
        <table cellspacing=2 cellpadding=4>
        <tr> 
          <td  class="statTabletitle"> Timestamp </td>
          <td  class="statTabletitle"> Procname </td>
          <td  class="statTabletitle"> Linenum </td>
          <td  class="statTabletitle"> tran_name </td>
          <td  class="statTabletitle"> LocksHeld </td>
          <td  class="statTabletitle"> memusage (Kb) </td>
        </tr>
        <?php
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
        <td class="statTable" NOWRAP> <?php echo $row["ts"] ?>  </td>
        <td class="statTable"> <?php echo $row["proc_name"] ?>  </td>
        <td class="statTable"> <?php echo $row["linenum"] ?>  </td>
        <td class="statTable"> <?php echo $row["tran_name"] ?>  </td>
        <td class="statTable"> <?php echo number_format($row["LocksHeld"] )?>  </td>
        <td class="statTable"> <?php echo number_format($row["memusageKB"]) ?>  </td>
        </tr> 
        <?php
    
        
            }
    ?>
        </table>
    </DIV>
    </DIV>
    </DIV>





        <?php

        // Check if table xxxx_CnxWaits exists
        $query = "select id from sysobjects where name ='".$ServerName."_CnxWaits'";
	$result = sybase_query($query,$pid);
	if ($result==false){ 
		sybase_close($pid); 
		$pid=0;
		include ("../connectArchiveServer.php");	
		echo "<tr><td>Error</td></tr></table>";
		return(0);
	}
	$rw=0;
        while($row = sybase_fetch_array($result))
        {
	  $rw++;
	}	

        if ($rw == 1)
        {
        	// Table CnxWaits exists, check if there is data for the analyzed period
            $query = "select cnt=count(*)
                  from ".$ServerName."_CnxWaits W
                  where Spid=".$Spid."
	                 and Kpid=".$Kpid."
                   and W.Timestamp >= '".$Loggedindatetime."'
                   and W.Timestamp < (select isnull(min(Loggedindatetime), '1/1/3000')
                                       from ".$ServerName."_Cnx cnx
                                       where cnx.Kpid = W.Kpid
                                         and cnx.Spid = W.Spid
                                         and cnx.Loggedindatetime > '".$Loggedindatetime."'
                                       )
	                 and W.Timestamp >='".$StartTimestamp."'        
	                 and W.Timestamp <'".$EndTimestamp."'";
             $result = sybase_query($query,$pid);
             $row = sybase_fetch_array($result);
             if ($row['cnt'] > 0 ) {
             	   // Table CnxWaits contains data for the analyzed period

        ?>





<div class="boxinmain" style="float:none;min-width:680px">
<div class="boxtop">
<img src="../images/boxtop-corner-left.jpg" style="float:left;margin:0px"/>
<div class="title" style="width:90%">Process elasped time distribution</div>
<img src="../images/boxtop-corner-right.jpg" style="float:right;margin:0px;"/>
</div>

<div class="boxcontent">
    
<div class="statMainTable" style="overflow:visible">
    <table cellspacing=2 cellpadding=4>    
    <tr><td>
    <img src='<?php echo "./graphWaitsPerClassEvents.php?ARContextJSON=".urlencode($ARContextJSON)."&Kpid=".urlencode($Kpid)."&Loggedindatetime=".urlencode($Loggedindatetime)."&CPUTime=".$CPUTime;  ?> '>
    </td></tr>
    <tr class="infobox"> 
    <td class="infobox"> <CENTER>
    <table cellspacing=2 cellpadding=4>
    <tr class=statTableTitle> 
      <td  class="statTabletitle"> Class </td>
      <td  class="statTabletitle"> WaitEventID</td>
      <td  class="statTabletitle"> Waits </td>
      <td  class="statTabletitle"> WaitTime_ms </td>
      <td  class="statTabletitle"> Description </td>
    </tr>
    <?php
    $query = "select Class=C.Description, W.WaitEventID, Waits=sum(Waits), WaitTime_ms=sum(WaitTime)    , E.Description
                  from ".$ServerName."_CnxWaits W,".$ServerName."_WEvInf E, ".$ServerName."_WClassInf C
                  where W.WaitEventID = E.WaitEventID
                    and E.WaitClassID = C.WaitClassID
	            and Spid=".$Spid."
	            and Kpid=".$Kpid."
                    and W.Timestamp >= '".$Loggedindatetime."'
                    and W.Timestamp < (select isnull(min(Loggedindatetime), '1/1/3000')
                                       from ".$ServerName."_Cnx cnx
                                       where cnx.Kpid = W.Kpid
                                         and cnx.Spid = W.Spid
                                         and cnx.Loggedindatetime > '".$Loggedindatetime."'
                                       )
	            and W.Timestamp >='".$StartTimestamp."'        
	            and W.Timestamp <'".$EndTimestamp."' 
                  group by C.Description, W.WaitEventID  , E.Description
                  order by WaitTime_ms desc";
                  
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
    <td class="statTablePtr" NOWRAP> <?php echo $row["Class"] ?> </td>
    <td class="statTablePtr" ALIGN="right"> <?php echo $row["WaitEventID"] ?>  </td>
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["Waits"]      ) ?>  </td>
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["WaitTime_ms"]) ?>  </td>
    <td class="statTablePtr" NOWRAP> <?php echo $row["Description"] ?>  </td>
    </tr> 
    <?php

    
        }
?>
    </table>
    </CENTER>
    </td></tr>
</table>
</DIV>
</DIV>
</DIV>
        <?php
	}  // End IF table CnxWaits contains data
} // End IF table CnxWaits exists
	else
	    echo "Process wait events not activated";
	?>



</form>
</body>
