<?php
  // Check if monitored ASE is V15 or less (column 'HkgcMaxQSize' exists in xxx_Engines didn't exist before V15)
  $result = sybase_query("select cnt=count(*) from syscolumns where id=object_id('".$ServerName."_Engines') and name='HkgcMaxQSize'"
  ,$pid);
  $row = sybase_fetch_array($result);
  if ($row['cnt']==0) {
     // before V15
	 $BEFORE_V15 = "Y";
     $add1 = " + 1";
     include ("sql/sql_ra_from_sysmon_statistics.php");
  }
  else {
	 $BEFORE_V15 = "N";
     $add1 = "";
     include ("sql/sql_ra_from_sysmon_statistics_V15.php");
  }

?>

<div class="boxinmain">

<div class="boxtop">
<div style="float:left; position: relative; top: 3px; left: 6px"><?php include './export/export-table.php' ?></div>
<div style="width:60%" class="title">Replication Agents Statistics</div>
</div>

<div class="boxcontent">

<div style="overflow-y:visible" class="statMainTable">

<?php
if ($BEFORE_V15 == 'Y') {
?>
<table cellspacing="2" cellpadding="4">
    <tr align="center"> 
      <td class="statTabletitle"> DbName  </td>
      <td class="statTabletitle"> LogRecordsScanned  </td>
      <td class="statTabletitle"> LogRecordsProcessed   </td>
      <td class="statTabletitle"> Updates   </td>
      <td class="statTabletitle"> Inserts   </td>
      <td class="statTabletitle"> Deletes   </td>
      <td class="statTabletitle"> StoredProcs   </td>
      <td class="statTabletitle"> DDLLogRecords   </td>
      <td class="statTabletitle"> WritetextLogRecords   </td>
      <td class="statTabletitle"> TextImageLogRecords   </td>
      <td class="statTabletitle"> Clrs   </td>
      <td class="statTabletitle"> OpenTran   </td>
      <td class="statTabletitle"> CommitTran   </td>
      <td class="statTabletitle"> AbortTran   </td>
      <td class="statTabletitle"> PreparedTran   </td>
      <td class="statTabletitle"> MaintUserTran   </td>
      <td class="statTabletitle"> PacketSent   </td>
      <td class="statTabletitle"> FullPacketSent   </td>
      <td class="statTabletitle"> LargestPacket   </td>
      <td class="statTabletitle"> TotalByteSent   </td>
      <td class="statTabletitle"> AvgPacket   </td>
      <td class="statTabletitle"> WaitRs   </td>
      <td class="statTabletitle"> TimeWaitRs_ms   </td>
      <td class="statTabletitle"> LongestWait_ms   </td>
      <td class="statTabletitle"> AvgWait_ms   </td>
    </tr>

    <?php
        
	
	$result = sybase_query($query,$pid);
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
            <td class="statTable">               <?php echo $row["dbname"]                                  ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_log_records_scanned"]  ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_log_records_processed"]) ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_xupdate_processed"]    ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_xinsert_processed"]    ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_xdelete_processed"]    ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_xexec_processed"]      ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_xcmdtext_processed"]   ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_xwrtext_processed"]    ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_xrowimage_processed"]  ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_xclr_processed"]       ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_open_xact"]            ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_commit_xact"]          ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_abort_xact"]           ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_prepare_xact"]         ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_maintuser_xact"]       ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_packets_sent"]         ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_full_packets_sent"]    ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_largest_packet"]       ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_sum_packet"]           ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php if ($row["ra_packets_sent"] > 0 ) echo number_format($row["ra_sum_packet"]/$row["ra_packets_sent"],2); else echo number_format($row["ra_packets_sent"]);?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_io_wait"]              ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_sum_io_wait"]          ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_longest_io_wait"]      ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php if ($row["ra_io_wait"] > 0 ) echo number_format($row["ra_sum_io_wait"]/$row["ra_io_wait"],2); else echo number_format($row["ra_io_wait"],2); ?> </td>
          </tr> 
        <?php
        }
        ?>
</table>
<?php }
else {

     // Version is V15 or higher
?>

<table cellspacing="2" cellpadding="4">
    <tr align="center"> 
      <td class="statTabletitle"> DbName  </td>
      <td class="statTabletitle"> LogRecordsScanned  </td>
      <td class="statTabletitle"> LogRecordsProcessed   </td>
      <td class="statTabletitle"> Updates   </td>
      <td class="statTabletitle"> Inserts   </td>
      <td class="statTabletitle"> Deletes   </td>
      <td class="statTabletitle"> StoredProcs   </td>
      <td class="statTabletitle"> DDLLogRecords   </td>
      <td class="statTabletitle"> WritetextLogRecords   </td>
      <td class="statTabletitle"> TextImageLogRecords   </td>
      <td class="statTabletitle"> Clrs   </td>
      <td class="statTabletitle"> OpenTran   </td>
      <td class="statTabletitle"> CommitTran   </td>
      <td class="statTabletitle"> AbortTran   </td>
      <td class="statTabletitle"> PreparedTran   </td>
      <td class="statTabletitle"> MaintUserTran   </td>
      <td class="statTabletitle"> PacketSent   </td>
      <td class="statTabletitle"> FullPacketSent   </td>
      <td class="statTabletitle"> TotalByteSent   </td>
      <td class="statTabletitle"> AvgPacket   </td>
      <td class="statTabletitle"> WaitSendRs   </td>
      <td class="statTabletitle"> TimeWaitSendRs_ms   </td>
      <td class="statTabletitle"> AvgWaitSendRS_ms   </td>
      <td class="statTabletitle"> WaitRecvRs   </td>
      <td class="statTabletitle"> TimeWaitRecvRs_ms   </td>
      <td class="statTabletitle"> AvgWaitRecvRS_ms   </td>
    </tr>

    <?php
        
	
	$result = sybase_query($query,$pid);
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
            <td class="statTable">               <?php echo $row["dbname"]                                  ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_log_records_scanned"]  ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_log_records_processed"]) ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_xupdate_processed"]    ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_xinsert_processed"]    ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_xdelete_processed"]    ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_xexec_processed"]      ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_xcmdtext_processed"]   ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_xwrtext_processed"]    ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_xrowimage_processed"]  ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_xclr_processed"]       ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_open_xact"]            ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_commit_xact"]          ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_abort_xact"]           ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_prepare_xact"]         ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_maintuser_xact"]       ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_packets_sent"]         ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_full_packets_sent"]    ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_sum_packet"]           ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php if ($row["ra_packets_sent"] > 0 ) echo number_format($row["ra_sum_packet"]/$row["ra_packets_sent"],2); else echo number_format($row["ra_packets_sent"]);?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_io_send"]              ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_sum_io_send_wait"]          ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php if ($row["ra_io_send"] > 0 ) echo number_format($row["ra_sum_io_send_wait"]/$row["ra_io_send"],2); else echo number_format($row["ra_io_send"],2); ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_io_recv"]              ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php echo number_format($row["ra_sum_io_recv_wait"]          ) ?> </td>
            <td class="statTable" align="RIGHT"> <?php if ($row["ra_io_recv"] > 0 ) echo number_format($row["ra_sum_io_recv_wait"]/$row["ra_io_recv"],2); else echo number_format($row["ra_io_recv"],2); ?> </td>
          </tr> 
        <?php
        }
        ?>
</table>


<?php
}  // End if $BEFORE_V15
?>
</DIV>
</DIV>
</DIV>
