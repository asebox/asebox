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
  
  $StmtID = $_GET['StmtID'];
  
  $Host_1="#8EA3E3";
  $Host_2="#CCCCFF";
  $Host_3="#F3F3FE";

  $title = $ServerName."-Stmt Detail";

    // Check if table xxxx_StmtStat supports 15.7
    $query = "select cnt=count(*) from syscolumns where id =object_id('".$ServerName."_StmtStat') and name in ('ClientName', 'ClientHostName', 'ClientApplName')";
    $result = sybase_query($query,$pid);
    $row = sybase_fetch_array($result);
    if ($row["cnt"] == 3)
        $support157=1;
    else
        $support157=0;



    ?>


    <title> <?php echo $title ?> </title>

</head>

<body>
    <?php
    $displaylevel=2;
    include ("../compare_search_panel.php");
    ?>
   <P>
   <center>




   <?php
        // get statement statistics 
    if ($support157 ==1)
      $selClientInfo = ", ClientName, ClientHostName, ClientApplName";
    else
      $selClientInfo = "";

	$query = "
	select 
	StmtID,
        BootID,
        KPID,
        SPID,
        BatchID,
        StartTime,
        Elapsed_s=str(convert(float,datediff(ms,StartTime,EndTime))/1000,8,3),
        ExactStat,
        [Login],
        Application,
        ClientHost,
        ClientIP,
        ClientOSPID,
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
        PacketsReceived,
        NetworkPacketSize,
        PlansAltered,
        PlanID,
        ContextID,
        RowsAffected
        ".$selClientInfo."
	from ".$ServerName."_StmtStat   
	where StmtID=".$StmtID;
        //echo $query;	
	$result = sybase_query($query,$pid);
	if ($result==false){ 
		echo "<tr><td>Error</td></tr></table>";
		return(0);
	}

	$rw=0;
	$cpt=0;
        $row = sybase_fetch_array($result);

        // Key key for SQL info
        $BootID = $row["BootID"];
        $KPID = $row["KPID"];
        $SPID = $row["SPID"];
        $BatchID = $row["BatchID"];
   ?>





<div class="boxinmain" style="min-width:780px;float:none">
<div class="boxtop">
<div class="title" style="width:65%">Statement Detail</div>
</div>

<div class="boxcontent">
<div class="statMainInfo">


<table cellspacing=0 cellpadding=0 class="infobox" align="left">
  <tr class="infobox"><td class="infobox"></td><td><td></td></tr>
  <tr class="infobox"><td class="infobox" align="left"><b>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp StmtID     </td>   <td>&nbsp:&nbsp</td>  </b><td><b><?php echo $row["StmtID"]                   ?></b></td></tr>  
  <tr class="infobox"><td class="infobox" align="left"><b>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp Elapsed_s  </td>   <td>&nbsp:&nbsp</td>  </b><td><b><?php echo number_format($row["Elapsed_s"]) ?></b></td></tr>
  <tr class="infobox"><td class="infobox" align="left"><b>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp Procedure  </td>   <td>&nbsp:&nbsp</td>  </b><td><b><?php echo $row["ProcName"]                 ?></b></td></tr>
  <tr class="infobox"><td class="infobox" align="left"><b>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp LineNumber </td>   <td>&nbsp:&nbsp</td>  </b><td><b><?php echo $row["LineNumber"]               ?></b></td></tr>
  <tr class="infobox"><td class="infobox"></td><td><td></td></tr>
</table>
</br>
</br>
</br>
</br>




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
      <tr> <td> StartTime </td> <td> : </td> <td> <?php echo $row["StartTime"] ?> </td> </tr>
      <tr> <td> Login </td> <td> : </td> <td> <?php echo $row["Login"] ?> </td> </tr>
      <tr> <td> Application </td> <td> : </td> <td> <?php echo $row["Application"] ?> </td> </tr>
      <tr> <td> ClientHost </td> <td> : </td> <td> <?php echo $row["ClientHost"] ?> </td> </tr>
      <tr> <td> ClientIP </td> <td> : </td> <td> <?php echo $row["ClientIP"] ?> </td> </tr>
      <tr> <td> ClientOSPID </td> <td> : </td> <td> <?php echo $row["ClientOSPID"] ?> </td> </tr>
      <?php if ($support157==1) { ?>
        <tr> <td> ClientName </td> <td> : </td> <td> <?php echo $row["ClientName"] ?> </td> </tr>
        <tr> <td> ClientHostName </td> <td> : </td> <td> <?php echo $row["ClientHostName"] ?> </td> </tr>
        <tr> <td> ClientApplName </td> <td> : </td> <td> <?php echo $row["ClientApplName"] ?> </td> </tr>        
      <?php } ?>
      <tr> <td> DBID </td> <td> : </td> <td> <?php echo $row["DBID"] ?> </td> </tr>
      <tr> <td> SPID </td> <td> : </td> <td> <?php echo $row["SPID"] ?> </td> </tr>
      <tr> <td> KPID </td> <td> : </td> <td> <?php echo $row["KPID"] ?> </td> </tr>
      <tr> <td> PlanID </td> <td> : </td> <td> <?php echo $row["PlanID"] ?> </td> </tr>
      <tr> <td> BatchID </td> <td> : </td> <td> <?php echo $row["BatchID"] ?> </td> </tr>
      <tr> <td> ContextID </td> <td> : </td> <td> <?php echo $row["ContextID"] ?> </td> </tr>
  </table></center>
</td>
<td class="infobox" valign="top"> <center>
  <table border="0" cellspacing="1" cellpadding="0" class="statInfo">
      <tr><td>CpuTime_ms       </td><td>: </td><td align="right"> <?php echo number_format($row["CpuTime"]          ) ?></td></tr>
      <tr><td>WaitTime_ms      </td><td>: </td><td align="right"> <?php echo number_format($row["WaitTime"]         ) ?></td></tr>
      <tr><td>MemUsageKB       </td><td>: </td><td align="right"> <?php echo number_format($row["MemUsageKB"]       ) ?></td></tr>
      <tr><td>PhysicalReads    </td><td>: </td><td align="right"> <?php echo number_format($row["PhysicalReads"]    ) ?></td></tr>
      <tr><td>LogicalReads     </td><td>: </td><td align="right"> <?php echo number_format($row["LogicalReads"]     ) ?></td></tr>
      <tr><td>PagesModified    </td><td>: </td><td align="right"> <?php echo number_format($row["PagesModified"]    ) ?></td></tr>
      <tr><td>PacketsSent      </td><td>: </td><td align="right"> <?php echo number_format($row["PacketsSent"]      ) ?></td></tr>
      <tr><td>PacketsReceived  </td><td>: </td><td align="right"> <?php echo number_format($row["PacketsReceived"]  ) ?></td></tr>
      <tr><td>NetworkPacketSize</td><td>: </td><td align="right"> <?php echo number_format($row["NetworkPacketSize"]) ?></td></tr>
      <tr><td>PlansAltered     </td><td>: </td><td align="right"> <?php echo number_format($row["PlansAltered"]     ) ?></td></tr>
      <tr><td>RowsAffected     </td><td>: </td><td align="right"> <?php echo number_format($row["RowsAffected"]     ) ?></td></tr>
  </table> </center>
</td>
</tr>
<tr> <td colspan=2  class="infobox">
<?php
if ($row["ExactStat"]=="N") 
   echo "Remark : Not exact due to sampling";
else
   echo "Exact Statistics";
?>
</td> </tr>
</table>
</DIV>
</DIV>
</DIV>


<br>


    <?php /* Objects Statistics */   ?>

    <div class="boxinmain" style="min-width:700px;max-width:1000px;float:none;">
    <div class="boxtop">
    <div class="title" >Objects Statistics</div>
    </div>
    
    <div class="boxcontent">
    
    <div class="statMainTable" style="overflow-y:visible">
        <table cellspacing="2" cellpadding="4" >
        <?php
        // check if objects stats is available
        $query = "select cnt=count(*) 
                  from ".$ServerName."_StmtObj   
                  where StmtID =".$StmtID;
        $result = sybase_query($query,$pid);
        $row = sybase_fetch_array($result);
        if ($row["cnt"] == 0) {
            echo "<tr class=\"infobox\"> <td class=\"infobox\">";
            echo "<table><tr> <td class='textInfoError'> No info available </td> </tr></table>";
            echo "</td></tr>";
        }
        else {
    
            // Check number of cols of StmtObj table
            $query = "select cnt=count(*) 
                      from syscolumns 
                      where id=object_id('".$ServerName."_StmtObj')";   
            $result = sybase_query($query,$pid);
            $row = sybase_fetch_array($result);
            if ($row["cnt"] == 10) {
            	// version of montored server < V15
                $version = "old";
            }
            else 
                $version = "new";  
    
    
        ?>
            
            <tr> 
              <td class=statTabletitle > DBName </td>
              <td class=statTabletitle > Owner </td>
              <td class=statTabletitle > ObjectName </td>
              <td class=statTabletitle > ObjectType </td>
              <td class=statTabletitle > IndID </td>
              <td class=statTabletitle > LogReads </td>
              <td class=statTabletitle > PhyReads </td>
              <td class=statTabletitle > PhyAPFRds </td>
              <?php if ($version=="old") { ?>
                 <td class=statTabletitle > TblSize(Kb) </td>
              <?php } else {?>
    <!--             <td class=statTableTitle > PartitionName </td> -->
                 <td class=statTabletitle > PtnSize(Kb) </td>
                 <td class=statTabletitle > IdxName </td>
              <?php } ?>
            </tr>
            
            <?php
            if ( $version == "old" )
                $query = "
                select 
                 DBName ,
                 OwnerUserID ,
                 ObjectName ,
                 ObjectType ,
                 IndexID ,
                 LogicalReads ,
                 PhysicalReads ,
                 PhysicalAPFReads ,
                 TableSize 
                from ".$ServerName."_StmtObj   
                where StmtID =".$StmtID."
                order by 1,2,3,5";
            else
                $query = "
                select 
                 DBName ,
                 OwnerUserID ,
                 ObjectName ,
                 ObjectType ,
                 IndexID ,
                 LogicalReads ,
                 PhysicalReads ,
                 PhysicalAPFReads ,
                 PartitionID, 
                 PartitionName, 
                 PartitionSize,
                 IdxName 
                from ".$ServerName."_StmtObj   
                where StmtID =".$StmtID."
                order by 1,2,3,5";
    
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
                <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';">
    	    	<?php
    
    	    $cpt=1-$cpt; 
                ?>
                <td class="statTable"> <?php echo $row["DBName"] ?> </td>
                <td class="statTable"> <?php echo $row["OwnerUserID"] ?> </td>
                <td class="statTable"> <?php echo $row["ObjectName"] ?> </td>
                <td class="statTable"> <?php echo $row["ObjectType"] ?> </td>
                <td class="statTable"> <?php echo $row["IndexID"] ?> </td>
                <td class="statTable" align="right"> <?php echo number_format($row["LogicalReads"]    ) ?> </td>
                <td class="statTable" align="right"> <?php echo number_format($row["PhysicalReads"]   ) ?> </td>
                <td class="statTable" align="right"> <?php echo number_format($row["PhysicalAPFReads"]) ?> </td>
                <?php if ($version=="old") { ?>
                   <td class="statTable" align="right"> <?php echo number_format($row["TableSize"]) ?> </td>
                <?php } else {?>
    <!--               <td class="statTable"> <?php echo $row["PartitionName"] ?> </td> -->
                   <td class="statTable" align="right"> <?php echo number_format($row["PartitionSize"]) ?> </td>
                   <td class="statTable"> <?php echo $row["IdxName"] ?> </td>
                <?php } ?>
                </tr> 
                <?php
            }
        }
        ?>
    </table>
    </DIV>
    </DIV>
    </DIV>
    
    
    <br>






    <?php /* Batch SQL */   ?>

    <?php
	$query = "
	select 
          LineNumber,
          SequenceInLine,
          SQLText
	from ".$ServerName."_StmtSQL   
	where BootID = ".$BootID."
          and KPID = ".$KPID."
          and SPID = ".$SPID."
          and BatchID = ".$BatchID."
	order by 1,2";
	
	$result = sybase_query($query,$pid);
	if ($result==false){ 
		sybase_close($pid); 
		$pid=0;
		include ("../connectArchiveServer.php");	
		echo "<tr><td>Error</td></tr></table>";
		return(0);
	}
    ?>	


    <div class="boxinmain" style="min-width:600px;max-width:1000px;float:none;">
    <div class="boxtop">
    <div class="title" >Batch SQL</div>
    </div>
    <div class="boxcontent">
    <table width="100%" class="statMainTable" cellspacing=10 cellpadding=0> 
    <tr> <td>
    <table cellspacing=8 cellpadding=0 >
    <?php
	$lastLineNumber=-1;
    while($row = sybase_fetch_array($result))
    {
    //  echo  "<tr class='textInfo'>  <td class='textInfo'>".$row["LineNumber"]."&nbsp;</td><td class='textInfo'>".str_replace(" ","&nbsp;",$row["SQLText"])  ."</td> </tr> ";
        $LineNumber=$row["LineNumber"];
        if ($LineNumber==$lastLineNumber) $dispLine=$dispLine.str_replace("\n","<BR>",$row['SQLText']);
        else {
            // New line
            if ($lastLineNumber!=-1)
                echo  "<tr>  <td valign='top' class='textLineInfo'>".$lastLineNumber."&nbsp;</td><td class='textInfo'>".$dispLine."</td> </tr> ";
            $dispLine = str_replace("\n","<BR>",$row['SQLText']);
            $lastLineNumber=$LineNumber;
        }
    } 
    if ($lastLineNumber!=-1)
        echo  "<tr>  <td valign='top' class='textLineInfo'>".$lastLineNumber."&nbsp;</td><td class='textInfo'>".$dispLine."</td> </tr> ";
    else {
        echo "<tr class='textInfo'> <td class='textInfoError'> No info available </td> </tr>";
    }
    ?>
    </table>
    </td> </tr>
    </table>
    </DIV>
    </DIV>



    <br>









    <?php /* SQL Plan */   ?>

    <?php
	$query = "
	select 
          Sequence,
          SQLPlan
	from ".$ServerName."_StmtPlan
	where StmtID =".$StmtID."
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



    <div class="boxinmain" style="min-width:600px;max-width:1000px;float:none;">
    <div class="boxtop">
    <div class="title" >SQL Plan</div>
    </div>
    <div class="boxcontent">
    <table width="100%" class="statMainTable" cellspacing=10 cellpadding=0> 
        <tr> <td>
        <table cellspacing=8 cellpadding=0 >
            <tr>
            <?php
            if (sybase_num_rows($result)==0) {
                ?>
                <td class='textInfoError'> No info available </td>
                <?php 
            } else {
            	?>
            	<td>
            	<?php 
                $cntrows=0;
                while($row = sybase_fetch_array($result))
                {
                    echo str_replace(" ","&nbsp;",$row["SQLPlan"])   ."<BR>";
                    $cntrows++;
                } 
                ?></td><?php
            } 
           ?>
           </tr>
        </table> 
        </td> </tr>
    </Table>
    </DIV>
    </DIV>


</body>
