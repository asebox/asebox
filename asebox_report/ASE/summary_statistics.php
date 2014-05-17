<div  style="clear:left;overflow:visible" class="boxinmain">



<div class="boxtop">
<div class="title" style="width:85%" >Summary Statistics</div>
</div>
<div class="boxcontent">


<table width="50%" border="0" cellspacing="1" cellpadding="0">
    
	<?php 
		// Get avg Online Engines	
		$result=sybase_query(	"select avgNbEnginesOnline=avg(NbEnginesOnline)
                                         from (
                                               select NbEnginesOnline =count(*)
                                               from ".$ServerName."_Engines
	                                       where Timestamp >='".$StartTimestamp."'
	                                       and Timestamp <='".$EndTimestamp."'
	                                       and ContextSwitches>0 -- for online engines only
	                                       group by Timestamp
                                          ) engOnline",
		                     $pid);
		while (($row=sybase_fetch_array($result)))
		{
			$avgNbEnginesOnline= $row["avgNbEnginesOnline"];
		}

		// Get avg CPU	
		$result=sybase_query(	"select avgUserCPU_pct=avg(convert(float,UserCPUTime*1000)/Interval)*100
		from ".$ServerName."_Engines
		where Timestamp >='".$StartTimestamp."'
		and Timestamp <='".$EndTimestamp."'
		and ContextSwitches>0",
		 $pid);
		while (($row=sybase_fetch_array($result)))
		{
			$avgUserCPU_pct= $row["avgUserCPU_pct"];
		}

		// Get num deadlocks	
		$result=sybase_query(	"select numdeadlocks =sum(NumDeadlocks), OpenedConnections = sum(OpenedConnections) 
		from ".$ServerName."_MonState
		where Timestamp >='".$StartTimestamp."'
		and Timestamp <='".$EndTimestamp."'",
		 $pid);
		while (($row=sybase_fetch_array($result)))
		{
			$numdeadlocks= $row["numdeadlocks"];
			$OpenedConnections = $row["OpenedConnections"];
		}
	
	?>
		
    <tr> 
      <td class="statTableUpperTitle" colspan="6"> CPU : </td>
    </tr>
    <tr align="center"> 
      <td class="statTabletitle" colspan="2"> avgNbEnginesOnline  </td>
      <td class="statTabletitle" colspan="2"> avgUserCPU_pct  </td>
      <td class="statTabletitle" colspan="1"> OpenedConnections  </td>
      <td class="statTabletitle" colspan="1"> deadlocks  </td>
    </tr>
    <tr align="right">
      <td class="statTable" colspan="2">  <?php echo number_format($avgNbEnginesOnline) ?> </td>
      <td class="statTable" colspan="2">  <?php echo number_format($avgUserCPU_pct,2) ?> </td>
      <td class="statTable" colspan="1">  <?php echo number_format($OpenedConnections) ?> </td>
      <td class="statTable" colspan="1">  <?php echo number_format($numdeadlocks) ?> </td>
    </tr>
    
    	<?php
		// Get DATA IO statistics	
		$result=sybase_query(	"select 
		Reads= sum (convert(numeric(16,0),Reads)), 
		APFReads = sum (convert(numeric(16,0),APFReads )), 
		Writes= sum (convert(numeric(16,0),Writes)),
		avgserv_ms=str(case when sum(1.*Reads+APFReads+Writes) = 0 then 0 else sum(convert(numeric(20,0),IOTime)) / sum(convert(numeric(20,0),1.*Reads+APFReads+Writes)) end,5,2)
		from ".$ServerName."_DevIO
		where Timestamp >='".$StartTimestamp."'
		and Timestamp <='".$EndTimestamp."'
                and LogicalName not like 'SYSDEV$[_][_]%' /* ignore crazy values for devices of archive db's */
		and lower(LogicalName) not like '%tempdb%'
		and lower(LogicalName) not like '%ramfs%'
		and lower(LogicalName) not like 'vdisk%'
		and lower(LogicalName) not like '%log%'",
		 $pid);
		while (($row=sybase_fetch_array($result)))
		{
			$DATAReads= $row["Reads"];
			$DATAAPFReads= $row["APFReads"];
			$DATAWrites= $row["Writes"];
			$DATAavgserv_ms= $row["avgserv_ms"];
		}

		// Get LOG IO statistics	
		$result=sybase_query(	"select 
		Reads= sum (convert(numeric(16,0),Reads)), 
		APFReads = sum (convert(numeric(16,0),APFReads )), 
		Writes= sum (convert(numeric(16,0),Writes)),
		avgserv_ms=str(case when sum(1.*Reads+APFReads+Writes) = 0 then 0 else sum(convert(numeric(20,0),IOTime)) / sum(convert(numeric(20,0),1.*Reads+APFReads+Writes)) end,5,2)
		from ".$ServerName."_DevIO
		where Timestamp >='".$StartTimestamp."'
		and Timestamp <='".$EndTimestamp."'
		and lower(LogicalName) like '%log%' and lower(LogicalName) not like '%tempdb%' and lower(LogicalName) not like '%ramfs%' and lower(LogicalName) not like 'vdisk%'",
		 $pid);
		while (($row=sybase_fetch_array($result)))
		{
			$LOGReads= $row["Reads"];
			$LOGAPFReads= $row["APFReads"];
			$LOGWrites= $row["Writes"];
			$LOGavgserv_ms= $row["avgserv_ms"];
		}

		// Get TEMPDB IO statistics	
		$result=sybase_query(	"select 
		Reads= sum (convert(numeric(16,0),Reads)), 
		APFReads = sum (convert(numeric(16,0),APFReads )), 
		Writes= sum (convert(numeric(16,0),Writes)),
		avgserv_ms=str(case when sum(1.*Reads+APFReads+Writes) = 0 then 0 else sum(convert(numeric(20,0),IOTime)) / sum(convert(numeric(20,0),1.*Reads+APFReads+Writes)) end,5,2)
		from ".$ServerName."_DevIO
		where Timestamp >='".$StartTimestamp."'
		and Timestamp <='".$EndTimestamp."'
		and (lower(LogicalName) like '%tempdb%'
		or lower(LogicalName) like '%ramfs%'
		or lower(LogicalName) like 'vdisk%')",
		 $pid);
		while (($row=sybase_fetch_array($result)))
		{
			$TEMPDBReads= $row["Reads"];
			$TEMPDBAPFReads= $row["APFReads"];
			$TEMPDBWrites= $row["Writes"];
			$TEMPDBavgserv_ms= $row["avgserv_ms"];
		}
	
	?>
    <tr> 
      <td class="statTableUpperTitle"  colspan="6"> Device IO : </td>
    </tr>
    <tr align="center"> 
      <td class="statTabletitle" colspan = "2"> Type  </td>
      <td class="statTabletitle" > Reads  </td>
      <td class="statTabletitle" > APFReads  </td>
      <td class="statTabletitle" > Writes  </td>
      <td class="statTabletitle" > Avgserv_ms  </td>
    </tr>
    <tr align="right">
      <td class="statTable" colspan="2" align="left"> DATA : </td>
      <td class="statTable"> <?php echo number_format($DATAReads) ?>      </td>
      <td class="statTable"> <?php echo number_format($DATAAPFReads) ?>   </td>
      <td class="statTable"> <?php echo number_format($DATAWrites) ?>     </td>
      <td class="statTable"> <?php echo number_format($DATAavgserv_ms,2) ?> </td>
    </tr>
    <tr align="right">
      <td class="statTable" colspan="2" align="left"> LOG : </td>
      <td class="statTable"> <?php echo number_format($LOGReads) ?>      </td>
      <td class="statTable"> <?php echo number_format($LOGAPFReads) ?>   </td>
      <td class="statTable"> <?php echo number_format($LOGWrites) ?>     </td>
      <td class="statTable"> <?php echo number_format($LOGavgserv_ms,2) ?> </td>
    </tr>
    <tr align="right">
      <td class="statTable" colspan="2" align="left"> TEMPDB : </td>
      <td class="statTable"> <?php echo number_format($TEMPDBReads) ?>      </td>
      <td class="statTable"> <?php echo number_format($TEMPDBAPFReads) ?>   </td>
      <td class="statTable"> <?php echo number_format($TEMPDBWrites) ?>     </td>
      <td class="statTable"> <?php echo number_format($TEMPDBavgserv_ms,2) ?> </td>
    </tr>
	<?php
		// Get stored procs activity
		$query = "select sumExec=sum(1.0*Requests), sumloads=sum(1.0*Loads), 
		procCacheHit= convert(float,sum(Requests)-sum(Loads))*100/sum(Requests),
		sumWrites=sum(1.0*Writes), sumStalls=sum(1.0*Stalls)
		from ".$ServerName."_ProcCache          
		where Timestamp >='".$StartTimestamp."'        
		and Timestamp <'".$EndTimestamp."'
		and Requests >=0 -- ignore negative counters
		and Loads >=0
		and Writes >=0";

		$result = sybase_query($query,$pid);
        	$row = sybase_fetch_array($result);

		$sumExec = $row["sumExec"];
    		$sumloads = $row["sumloads"];
    		$procCacheHit = $row["procCacheHit"];
    		$sumWrites = $row["sumWrites"];
    		$sumStalls = $row["sumStalls"];
	?>
    <tr> 
      <td class="statTableUpperTitle" colspan= "6" > Stored proc activity : </td>
    </tr>
    <tr align="center"> 
      <td class="statTabletitle" colspan= "2"> sumExec  </td>
      <td class="statTabletitle" > sumloads  </td>
      <td class="statTabletitle" > procCacheHit   </td>
      <td class="statTabletitle" > sumWrites   </td>
      <td class="statTabletitle" > sumStalls   </td>
    </tr>
    <tr align="right">
      <td class="statTable" colspan= "2"> <?php echo number_format($sumExec) ?>  </td>
      <td class="statTable"> <?php echo number_format($sumloads) ?>  </td>
      <td class="statTable"> <?php echo number_format($procCacheHit,2) ?>  </td>
      <td class="statTable"> <?php echo number_format($sumWrites) ?>  </td>
      <td class="statTable"> <?php echo number_format($sumStalls) ?>  </td>
    </tr>

	<?php
		// Get network activity
		$query = "select PacketsSnt=sum(convert(numeric(15,0),case when PacketsSent<0 then power(2.,31)+PacketsSent else PacketsSent end)), 
		PacketsRcvd=sum(convert(numeric(15,0),case when PacketsReceived<0 then power(2.,31)+PacketsReceived else PacketsReceived end)), 
		BytesSnt=str(1.*sum(convert(numeric(15,0),case when BytesSent<0 then power(2.,31)+BytesSent else BytesSent end))/(1024*1024),12,2)+' Mb', 
		BytesRcvd=str(1.*sum(convert(numeric(15,0),case when BytesReceived <0 then power(2.,31)+BytesReceived else BytesReceived end))/(1024*1024),12,2)+' Mb',
		avgPktRcvSize=str(sum(convert(numeric(15,0),case when BytesReceived <0 then power(2.,31)+BytesReceived else BytesReceived end))/sum(case when PacketsReceived<0 then power(2.,31)+PacketsReceived else PacketsReceived end),8,2),
		avgPktSentSize=str(sum(convert(numeric(15,0),case when BytesSent<0 then power(2.,31)+BytesSent else BytesSent end))/sum(case when PacketsSent<0 then power(2.,31)+PacketsSent else PacketsSent end),8,2)
		from ".$ServerName."_NetworkIO
		where Timestamp >='".$StartTimestamp."'        
		and Timestamp <'".$EndTimestamp."'";
	
		$result = sybase_query($query,$pid);
    $row = sybase_fetch_array($result);

		$PacketsSent = $row["PacketsSnt"];
    		$PacketsReceived = $row["PacketsRcvd"];
    		$BytesSent = $row["BytesSnt"];
    		$BytesReceived = $row["BytesRcvd"];
    		$avgPktRcvSize = $row["avgPktRcvSize"];
    		$avgPktSentSize = $row["avgPktSentSize"];
	?>
    <tr> 
      <td class="statTableUpperTitle" colspan= "6" > Network activity : </td>
    </tr>
    <tr align="center"> 
      <td class="statTabletitle" colspan= "3">   </td>
      <td class="statTabletitle" > Packets  </td>
      <td class="statTabletitle" > Bytes  </td>
      <td class="statTabletitle" > avgSize   </td>
    </tr>
    <tr align="right">
      <td class="statTable" colspan= "3" align="left" nowrap> RECEIVED : </td>
      <td class="statTable"> <?php echo number_format($PacketsReceived) ?>  </td>
      <td class="statTable"> <?php echo $BytesReceived ?>  </td>
      <td class="statTable"> <?php echo $avgPktRcvSize ?>  </td>
    </tr>
    <tr align="right">
      <td class="statTable" colspan= "3" align="left"> SENT : </td>
      <td class="statTable"> <?php echo number_format($PacketsSent) ?>  </td>
      <td class="statTable"> <?php echo $BytesSent ?>  </td>
      <td class="statTable"> <?php echo $avgPktSentSize ?>  </td>
    </tr>


    <?php
    // Check if table xxxx_StmtCache exists
    $query = "select id from sysobjects where name ='".$ServerName."_StmtCache'";
    $result = sybase_query($query,$pid);
    $rw=0;
    while($row = sybase_fetch_array($result))
    {
    $rw++;
    }	
    if ($rw == 1)   // Check if xxxx_StmtCache exists
    {
        $query = 
        "select MaxTotalSizeKB=max(TotalSizeKB), AvgUsedSizeKB=avg(UsedSizeKB),MaxUsedSizeKB=max(UsedSizeKB),
         AvgNumStatements=Avg(NumStatements), MaxNumStatements=max(NumStatements),
         NumSearches=sum(1.*NumSearches), HitCount=sum(1.*HitCount), CacheHit= case when sum(1.*NumSearches) =0 then 100 else 100.*sum(1.*HitCount)/ sum(1.*NumSearches) end,
         NumInserts=sum(1.*NumInserts),  NumRemovals=sum(1.*NumRemovals),
         NumRecompilesSchemaChanges=sum(1.*NumRecompilesSchemaChanges),
         NumRecompilesPlanFlushes =sum(1.*NumRecompilesPlanFlushes)
        from ".$ServerName."_StmtCache
        where Timestamp >='".$StartTimestamp."'        
    		  and Timestamp <'".$EndTimestamp."'";
    
        $result = sybase_query($query,$pid);
        $row = sybase_fetch_array($result);
    
    		$MaxTotalSizeKB = $row["MaxTotalSizeKB"];
    		$AvgUsedSizeKB = $row["AvgUsedSizeKB"];
    		$MaxUsedSizeKB = $row["MaxUsedSizeKB"];
    		$AvgNumStatements = $row["AvgNumStatements"];
    		$MaxNumStatements = $row["MaxNumStatements"];
    		$NumSearches = $row["NumSearches"];
    		$HitCount = $row["HitCount"];
    		$CacheHit = $row["CacheHit"];
    		$NumInserts = $row["NumInserts"];
    		$NumRemovals = $row["NumRemovals"];
    		$NumRecompilesSchemaChanges = $row["NumRecompilesSchemaChanges"];
    		$NumRecompilesPlanFlushes = $row["NumRecompilesPlanFlushes"];
        
        if ($MaxTotalSizeKB!="") {
            ?>
            <tr> 
              <td class="statTableUpperTitle" colspan= "6" > Statement cache activity : </td>
            </tr>
            <tr align="center"> 
              <td class="statTabletitle" colspan="2"> MaxTotalSizeKB  </td>
              <td class="statTabletitle" > AvgUsedSizeKB  </td>
              <td class="statTabletitle" > MaxUsedSizeKB   </td>
              <td class="statTabletitle" > AvgNumStatements   </td>
              <td class="statTabletitle" > MaxNumStatements   </td>
            </tr>
            <tr align="right">
              <td class="statTable" colspan="2"> <?php echo number_format($MaxTotalSizeKB,0) ?>  </td>
              <td class="statTable"> <?php echo number_format($AvgUsedSizeKB,0) ?>  </td>
              <td class="statTable"> <?php echo number_format($MaxUsedSizeKB) ?>  </td>
              <td class="statTable"> <?php echo number_format($AvgNumStatements,0) ?>  </td>
              <td class="statTable"> <?php echo number_format($MaxNumStatements) ?>  </td>
            </tr>
                
            <tr align="center"> 
              <td class="statTabletitle" colspan="2"> NumSearches  </td>
              <td class="statTabletitle" > HitCount  </td>
              <td class="statTabletitle" > CacheHit   </td>
              <td class="statTabletitle" > NumInserts   </td>
              <td class="statTabletitle" > NumRemovals   </td>
            </tr>
            <tr align="right">
              <td class="statTable" colspan="2"> <?php echo number_format($NumSearches) ?>  </td>
              <td class="statTable"> <?php echo number_format($HitCount) ?>  </td>
              <td class="statTable"> <?php echo number_format($CacheHit,2) ?>  </td>
              <td class="statTable"> <?php echo number_format($NumInserts) ?>  </td>
              <td class="statTable"> <?php echo number_format($NumRemovals) ?>  </td>
            </tr>
        
            <tr align="center"> 
              <td class="statTabletitle" colspan="3"> NumRecompilesSchemaChanges  </td>
              <td class="statTabletitle" colspan="3"> NumRecompilesPlanFlushes  </td>
            </tr>
            <tr align="right">
              <td class="statTable" colspan="3"> <?php echo number_format($NumRecompilesSchemaChanges) ?>  </td>
              <td class="statTable" colspan="3"> <?php echo number_format($NumRecompilesPlanFlushes) ?>  </td>
            </tr>
            <?php
        }
    
    } // end Check if xxxx_StmtCache exists 
    ?>



  </table>

  </DIV>


</DIV>