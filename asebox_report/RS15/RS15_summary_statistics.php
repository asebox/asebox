<div  style="clear:left;overflow:visible" class="boxinmain">



<div class="boxtop">
<img src="images/boxtop-corner-left.jpg" style="float:left;margin:0px"/>
<div class="title" style="width:85%" >Summary Statistics</div>
<img src="images/boxtop-corner-right.jpg" style="float:right;margin:0px;"/>
</div>
<div class="boxcontent">


<table  border="0" cellspacing="1" cellpadding="0">
    
	<?php 
	
	// Compute elapsed time
	$result=sybase_query("select elapsed_s=datediff(ss,  (select min(S.Timestamp)  
                              from ".$ServerName."_RSStats S
                              where S.Timestamp >='".$StartTimestamp."'
                              and S.Timestamp <='".$EndTimestamp."'), 
                              (select max(S.Timestamp)  
                              from ".$ServerName."_RSStats S
                              where S.Timestamp >='".$StartTimestamp."'
                              and S.Timestamp <='".$EndTimestamp."')) ",$pid);
	$row=sybase_fetch_array($result);
	$elapsed_s= $row["elapsed_s"];
	
	
	
		// Get stable device stats
        $blocksize = 16; // This is currently fixed but should be based on config
		$result=sybase_query("select
Capacity_Mb=avg(Total_segs_byts)*64*".$blocksize."/1024, 
UsedAvg_Mb=avg(Used_segs_byts)*64*".$blocksize."/1024, 
UsedMax_Mb=max(Used_segs_byts)*64*".$blocksize."/1024,
UsedLast_Mb=(select sum(Used_segs)*64*".$blocksize."/1024 
             from ".$ServerName."_DISKSPCE
             where Timestamp = 
                      (select max(Timestamp) from ".$ServerName."_DISKSPCE where Timestamp <='".$EndTimestamp."')
		     )
from (
select 
Total_segs_byts=sum(Total_segs), 
Used_segs_byts=sum(Used_segs)
from ".$ServerName."_DISKSPCE D
where Timestamp >='".$StartTimestamp."'
and Timestamp <='".$EndTimestamp."'
group by Timestamp
) stat_by_ts",$pid);
		$row=sybase_fetch_array($result);
			$Capacity_Mb= $row["Capacity_Mb"];
			$UsedAvg_Mb= $row["UsedAvg_Mb"];
			$UsedMax_Mb= $row["UsedMax_Mb"];
			$UsedLast_Mb= $row["UsedLast_Mb"];




    // Get RSStats stats
		$result=sybase_query("select 
    Commands_received      = sum(case when counter_id=58000 then (convert(numeric(14,0),counter_obs)  ) else null end), 
    Applied_commands       = sum(case when counter_id=58001 then (convert(numeric(14,0),counter_obs)  ) else null end),
    KBytes_received        = sum(case when counter_id=58011 then convert(numeric(14,0),counter_total) else null end)/1024, 
    RA_Ks                  = sum(case when counter_id=58011 then convert(numeric(14,0),counter_total) else null end) /(".$elapsed_s." * 1024),

    DSITranGroupsCommit    = sum(case when counter_id=5024 then (convert(numeric(14,0),counter_obs)  ) else null end), 
    DSITransUngroupedCommit= sum(case when counter_id=5026 then (convert(numeric(14,0),counter_total)  ) else null end),
    DSICmdsSucceed         = sum(case when counter_id=5028 then convert(numeric(14,0),counter_total) else null end), 
    DSIEBytesSucceed_Ks    = sum(case when counter_id=57148 then convert(numeric(14,0),counter_total) else null end) /(".$elapsed_s." * 1024),

    BlocksWritten          = sum(case when counter_id=6002 then 1.*counter_obs else null end),
    writes                 = sum(case when counter_id=6057 then 1.*counter_obs else null end),
    avgWriteTime_ms        = sum(case when counter_id=6057 then 1.*counter_total else null end)/sum(case when counter_id=6057 then 1.*counter_obs else null end),
    BytesWritten_Kb        = sum(case when counter_id=6004 then 1.*counter_total else null end)/1024, 
    BytesWritten_Ks        = sum(case when counter_id=6004 then 1.*counter_total else null end)/(".$elapsed_s." * 1024),

    CmdsRead               = sum(case when counter_id=62000 then 1.*counter_obs else null end),
    BlocksRead             = sum(case when counter_id=62002 then 1.*counter_obs else null end),
    BlocksReadCached       = sum(case when counter_id=62004 then 1.*counter_obs else null end),
    SQMRReads              = sum(case when counter_id=62011 then 1.*counter_obs else null end),
    AVGSQMRReadTime_ms     = sum(case when counter_id=62011 then 1.*counter_total else null end) 
                                  / sum(case when counter_id=62011 then 1.*counter_obs else null end),

    /* RSI */
    BytesSent_Kb           = sum(case when counter_id=4000 then 1.*counter_total else null end)/1024,
    BytesSent_Ks           = sum(case when counter_id=4000 then 1.*counter_total else null end)/(".$elapsed_s." * 1024),
    PacketsSent            = sum(case when counter_id=4002 then 1.*counter_obs else null end),
    MsgsSent               = sum(case when counter_id=4004 then 1.*counter_obs else null end),
    AvgSendPTTime_ms       = sum(case when counter_id=4009 then 1.*counter_total else null end)
                                 / sum(case when counter_id=4002 then 1.*counter_obs else null end),

    /* RSI USER */
    RSIUBytsRcvd_Kb        = sum(case when counter_id=59016 then 1.*counter_total else null end)/1024,
    RSIUBytsRcvd_Ks        = sum(case when counter_id=59016 then 1.*counter_total else null end)/(".$elapsed_s." * 1024),
    RSIUBuffsRcvd          = sum(case when counter_id=59013 then 1.*counter_obs else null end),
    RSIUMsgRecv            = sum(case when counter_id=59001 then 1.*counter_obs else null end)
    
    from ".$ServerName."_RSStats S
    where S.Timestamp >='".$StartTimestamp."'
    and S.Timestamp <='".$EndTimestamp."'
	/*and S.counter_id in (58000,58001,58011,5024,5026,5028,57148,6002,6057,6004,62000,62002,62004,62011)*/
",$pid);
		$row=sybase_fetch_array($result);
			$Commands_received= $row["Commands_received"];
			$Applied_commands= $row["Applied_commands"];
			$KBytes_received= $row["KBytes_received"];
			$RA_Ks= $row["RA_Ks"];	

			$DSITranGroupsCommit= $row["DSITranGroupsCommit"];
			$DSITransUngroupedCommit= $row["DSITransUngroupedCommit"];
			$DSICmdsSucceed= $row["DSICmdsSucceed"];
			$DSIEBytesSucceed_Ks= $row["DSIEBytesSucceed_Ks"];

			$BlocksWritten= $row["BlocksWritten"];
			$writes= $row["writes"];
			$avgWriteTime_ms= $row["avgWriteTime_ms"];
			$BytesWritten_Kb= $row["BytesWritten_Kb"];
			$BytesWritten_Ks= $row["BytesWritten_Ks"];

			$CmdsRead= $row["CmdsRead"];
			$BlocksRead= $row["BlocksRead"];
			$BlocksReadCached= $row["BlocksReadCached"];
			$SQMRReads= $row["SQMRReads"];
			$AVGSQMRReadTime_ms= $row["AVGSQMRReadTime_ms"];
			
			$BytesSent_Kb= $row["BytesSent_Kb"];
			$BytesSent_Ks= $row["BytesSent_Ks"];
			$PacketsSent= $row["PacketsSent"];
			$MsgsSent= $row["MsgsSent"];
			$AvgSendPTTime_ms= $row["AvgSendPTTime_ms"];

			$RSIUBytsRcvd_Kb= $row["RSIUBytsRcvd_Kb"];
			$RSIUBytsRcvd_Ks= $row["RSIUBytsRcvd_Ks"];
			$RSIUBuffsRcvd= $row["RSIUBuffsRcvd"];
			$RSIUMsgRecv= $row["RSIUMsgRecv"];
?>
		
    <tr> 
      <td class="statTableUpperTitle" colspan="6"> Stable Devices : </td>
    </tr>
    <tr align="center"> 
      <td class="statTabletitle" colspan="2"> Capacity_Mb  </td>
      <td class="statTabletitle" colspan="2"> UsedAvg_Mb  </td>
      <td class="statTabletitle" colspan="1"> UsedMax_Mb  </td>
      <td class="statTabletitle" colspan="1"> UsedLast_Mb  </td>
    </tr>
    <tr align="right">
      <td class="statTable" colspan="2">  <?php echo number_format($Capacity_Mb) ?> </td>
      <td class="statTable" colspan="2">  <?php echo number_format($UsedAvg_Mb) ?> </td>
      <td class="statTable" colspan="1">  <?php echo number_format($UsedMax_Mb) ?> </td>
      <td class="statTable" colspan="1">  <?php echo number_format($UsedLast_Mb) ?> </td>
    </tr>
    


	<?php 
		// Get RA stats


        // Get avg number of active Rep Agents
		$result=sybase_query(
"select avgActiveRA=avg(1.*cnt)
from (
select cnt=count(*)
    from  ".$ServerName."_RSStats 
    where counter_id=58000
    and Timestamp >='".$StartTimestamp."'
    and Timestamp <='".$EndTimestamp."'
group by Timestamp) ActiveRA",$pid);
		$row=sybase_fetch_array($result);
			$avgActiveRA= $row["avgActiveRA"];
	
	?>
		
    <tr> 
      <td class="statTableUpperTitle" colspan="6"> Rep Agents : </td>
    </tr>
    <tr align="center"> 
      <td class="statTabletitle" colspan="1"> avgActiveRA  </td>
      <td class="statTabletitle" colspan="1"> Commands_received  </td>
      <td class="statTabletitle" colspan="2"> Applied_commands  </td>
      <td class="statTabletitle" colspan="1"> KBytes_received  </td>
      <td class="statTabletitle" colspan="1"> AvgInput Kb/s  </td>
    </tr>
    <tr align="right">
      <td class="statTable" colspan="1">  <?php echo number_format($avgActiveRA,2) ?> </td>
      <td class="statTable" colspan="1">  <?php echo number_format($Commands_received) ?> </td>
      <td class="statTable" colspan="2">  <?php echo number_format($Applied_commands) ?> </td>
      <td class="statTable" colspan="1">  <?php echo number_format($KBytes_received) ?> </td>
      <td class="statTable" colspan="1">  <?php echo number_format($RA_Ks,2) ?> </td>
    </tr>
    





    <tr> 
      <td class="statTableUpperTitle" colspan="6"> RSI : </td>
    </tr>
    <tr align="center"> 
      <td class="statTabletitle" colspan="1">   </td>
      <td class="statTabletitle" colspan="1"> BytesSent Kb  </td>
      <td class="statTabletitle" colspan="2"> BytesSent Kb/s  </td>
      <td class="statTabletitle" colspan="1"> MsgsSent  </td>
      <td class="statTabletitle" colspan="1"> AvgSendPTTime_ms  </td>
    </tr>
    <tr align="right">
      <td class="statTable" colspan="1"> Output </td>
      <td class="statTable" colspan="1">  <?php echo number_format($BytesSent_Kb) ?> </td>
      <td class="statTable" colspan="2">  <?php echo number_format($BytesSent_Ks,2) ?> </td>
      <td class="statTable" colspan="1">  <?php echo number_format($MsgsSent) ?> </td>
      <td class="statTable" colspan="1">  <?php echo number_format($AvgSendPTTime_ms,2) ?> </td>
    </tr>


    <tr align="center"> 
      <td class="statTabletitle" colspan="1">   </td>
      <td class="statTabletitle" colspan="1"> RSIUBytsRcvd Kb  </td>
      <td class="statTabletitle" colspan="2"> RSIUBytsRcvd Kb/s  </td>
      <td class="statTabletitle" colspan="1"> RSIUMsgRecv  </td>
      <td class="statTabletitle" colspan="1">   </td>
    </tr>
    <tr align="right">
      <td class="statTable" colspan="1"> Input </td>
      <td class="statTable" colspan="1">  <?php echo number_format($RSIUBytsRcvd_Kb) ?> </td>
      <td class="statTable" colspan="2">  <?php echo number_format($RSIUBytsRcvd_Ks,2) ?> </td>
      <td class="statTable" colspan="1">  <?php echo number_format($RSIUMsgRecv) ?> </td>
      <td class="statTable" colspan="1">   </td>
    </tr>





	<?php 
		// Get DSI stats

        // Get avg number of active DSI
		$result=sybase_query(
"select avgActiveDSI=avg(1.*cnt)
from (
select cnt=count(*)
    from  ".$ServerName."_RSStats 
    where counter_id=5028
    and Timestamp >='".$StartTimestamp."'
    and Timestamp <='".$EndTimestamp."'
group by Timestamp) ActiveDSI",$pid);
		$row=sybase_fetch_array($result);
			$avgActiveDSI= $row["avgActiveDSI"];

	?>
	

		
    <tr> 
      <td class="statTableUpperTitle" colspan="6"> DSI's : </td>
    </tr>
    <tr align="center"> 
      <td class="statTabletitle" colspan="1"> avgActiveDSI  </td>
      <td class="statTabletitle" colspan="1"> DSITranGroupsCommit  </td>
      <td class="statTabletitle" colspan="2"> DSITransUngroupedCommit  </td>
      <td class="statTabletitle" colspan="1"> DSICmdsSucceed  </td>
      <td class="statTabletitle" colspan="1"> AvgOutput Kb/s  </td>
    </tr>
    <tr align="right">
      <td class="statTable" colspan="1">  <?php echo number_format($avgActiveDSI,2) ?> </td>
      <td class="statTable" colspan="1">  <?php echo number_format($DSITranGroupsCommit) ?> </td>
      <td class="statTable" colspan="2">  <?php echo number_format($DSITransUngroupedCommit) ?> </td>
      <td class="statTable" colspan="1">  <?php echo number_format($DSICmdsSucceed) ?> </td>
      <td class="statTable" colspan="1">  <?php echo number_format($DSIEBytesSucceed_Ks,2) ?> </td>
    </tr>
    



	<?php 
		// Get SQM stats

        // Get avg number of active SQM
		$result=sybase_query(
"select avgActiveSQM=avg(1.*cnt)
from (
select cnt=count(*)
    from  ".$ServerName."_RSStats 
    where counter_id=6000
    and Timestamp >='".$StartTimestamp."'
    and Timestamp <='".$EndTimestamp."'
group by Timestamp) ActiveSQM",$pid);
		$row=sybase_fetch_array($result);
			$avgActiveSQM= $row["avgActiveSQM"];

	?>
	

		
    <tr> 
      <td class="statTableUpperTitle" colspan="6"> SQM's : </td>
    </tr>
    <tr align="center"> 
      <td class="statTabletitle" colspan="1"> avgActiveSQM  </td>
      <td class="statTabletitle" colspan="1"> BlocksWritten  </td>
      <td class="statTabletitle" colspan="1"> writes  </td>
      <td class="statTabletitle" colspan="1"> avgWriteTime_ms  </td>
      <td class="statTabletitle" colspan="1"> KBWritten  </td>
      <td class="statTabletitle" colspan="1"> AvgWrite Kb/s  </td>
    </tr>
    <tr align="right">
      <td class="statTable" colspan="1">  <?php echo number_format($avgActiveSQM,2) ?> </td>
      <td class="statTable" colspan="1">  <?php echo number_format($BlocksWritten) ?> </td>
      <td class="statTable" colspan="1">  <?php echo number_format($writes) ?> </td>
      <td class="statTable" colspan="1">  <?php echo number_format($avgWriteTime_ms,2) ?> </td>
      <td class="statTable" colspan="1">  <?php echo number_format($BytesWritten_Kb) ?> </td>
      <td class="statTable" colspan="1">  <?php echo number_format($BytesWritten_Ks,2) ?> </td>
    </tr>



	<?php 
		// Get SQMR stats

        // Get avg number of active SQM
		$result=sybase_query(
"select avgActiveSQMR=avg(1.*cnt)
from (
select cnt=count(*)
    from  ".$ServerName."_RSStats 
    where counter_id=62000
    and Timestamp >='".$StartTimestamp."'
    and Timestamp <='".$EndTimestamp."'
group by Timestamp) ActiveSQMR",$pid);
		$row=sybase_fetch_array($result);
			$avgActiveSQMR= $row["avgActiveSQMR"];

	?>
	

		
    <tr align="center"> 
      <td class="statTabletitle" colspan="1"> avgActiveSQMR  </td>
      <td class="statTabletitle" colspan="1"> BlocksRead  </td>
      <td class="statTabletitle" colspan="1"> reads  </td>
      <td class="statTabletitle" colspan="1"> avgReadTime_ms  </td>
      <td class="statTabletitle" colspan="1"> CmdsRead  </td>
      <td class="statTabletitle" colspan="1"> BlocksReadCached  </td>
    </tr>
    <tr align="right">
      <td class="statTable" colspan="1">  <?php echo number_format($avgActiveSQMR,2) ?> </td>
      <td class="statTable" colspan="1">  <?php echo number_format($BlocksRead) ?> </td>
      <td class="statTable" colspan="1">  <?php echo number_format($SQMRReads) ?> </td>
      <td class="statTable" colspan="1">  <?php echo number_format($AVGSQMRReadTime_ms,2) ?> </td>
      <td class="statTable" colspan="1">  <?php echo number_format($CmdsRead) ?> </td>
      <td class="statTable" colspan="1">  <?php echo number_format($BlocksReadCached) ?> </td>
    </tr>


	</table>

  </DIV>


</DIV>