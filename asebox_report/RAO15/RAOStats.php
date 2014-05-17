<?php
  function display_counter($counter_name, $RAOCntDesc, $row)
  {
     echo "<tr> <td>";
     if ($RAOCntDesc[$counter_name]["graph"]=="1") {
         echo "<INPUT TYPE=\"checkbox\" NAME=graphCKB[] VALUE=\"".$counter_name."\" ";
         if ($RAOCntDesc[$counter_name]["isChecked"]=="1")
             echo "CHECKED";
         echo ">";
     }
     echo $RAOCntDesc[$counter_name]["module"];
     echo "<ACRONYM TITLE=\"".$RAOCntDesc[$counter_name]["display_name"]." (".$RAOCntDesc[$counter_name]["counter_type"].")\" > ".$counter_name." </ACRONYM>";
     
     if ($RAOCntDesc[$counter_name]["counter_type"]=="AVG")
         $displayinfo=number_format($row[trim($counter_name)],2);
     if ($RAOCntDesc[$counter_name]["counter_type"]=="MAX")
         $displayinfo=number_format($row[trim($counter_name)]);
     if ($RAOCntDesc[$counter_name]["counter_type"]=="SUM")
         $displayinfo=number_format($row[trim($counter_name)]);
     if ($RAOCntDesc[$counter_name]["counter_type"]=="LAST")
         $displayinfo=$row[trim($counter_name)];
     
     echo " </td> <td> : </td> <td align='right'> ".$displayinfo."</td> </tr>";


  }
?>

<script>
function clearAllCheckBoxes() {
  var elem = document.getElementsByName("graphCKB[]");
  for(var i = 0; i < elem.length; i++)
  {
    elem[i].checked = false;
  }

// document.inputparam.submit()
}
</script>


  <?php





    // Initialize RAOCntDesc config structure
    $RAOCntDesc = array();
    $RAOCntDesc["avg_bytes_second_during_trans "] = array ("counter_type" => "AVG",  "module" => "LTI", "graph" => 1, "isChecked" => 0,  "display_name" => "Avg Bytes/second during transmission                    ");          
    $RAOCntDesc["avg_changeset_send_time       "] = array ("counter_type" => "AVG",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Avg ChangeSet send time (ms)                            ");          
    $RAOCntDesc["avg_data_arrival_time         "] = array ("counter_type" => "AVG",  "module" => "LTI", "graph" => 1, "isChecked" => 0,  "display_name" => "Avg data arrival time                                   ");          
    $RAOCntDesc["avg_ltl_buffer_cache_time     "] = array ("counter_type" => "AVG",  "module" => "LTI", "graph" => 1, "isChecked" => 0,  "display_name" => "Avg LTL buffer cache time                               ");          
    $RAOCntDesc["avg_ltl_buffer_size           "] = array ("counter_type" => "AVG",  "module" => "LTI", "graph" => 1, "isChecked" => 0,  "display_name" => "Avg LTL buffer size                                     ");          
    $RAOCntDesc["avg_ltl_command_size          "] = array ("counter_type" => "AVG",  "module" => "LTI", "graph" => 1, "isChecked" => 0,  "display_name" => "Avg LTL command size                                    ");          
    $RAOCntDesc["avg_ltl_commands_buffer       "] = array ("counter_type" => "AVG",  "module" => "LTI", "graph" => 1, "isChecked" => 0,  "display_name" => "Avg LTL commands/buffer                                 ");          
    $RAOCntDesc["avg_ltl_commands_sec          "] = array ("counter_type" => "AVG",  "module" => "LTI", "graph" => 1, "isChecked" => 0,  "display_name" => "Avg LTL commands/sec                                    ");          
    $RAOCntDesc["avg_ltm_buffer_utilization    "] = array ("counter_type" => "AVG",  "module" => "LTI", "graph" => 1, "isChecked" => 0,  "display_name" => "Avg LTM buffer utilization (%)                          ");          
    $RAOCntDesc["avg_nb_of_log_records_per_ckpt"] = array ("counter_type" => "AVG",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Average number of log records per checkpoint            ");          
    $RAOCntDesc["avg_nb_of_s_between_log_r_ckpt"] = array ("counter_type" => "AVG",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Average number of seconds between log record checkpoints");          
    $RAOCntDesc["avg_number_of_bytes_per_record"] = array ("counter_type" => "AVG",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Average number of bytes per record                      ");          
    $RAOCntDesc["avg_number_of_bytes_read_per_s"] = array ("counter_type" => "AVG",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Average number of bytes read per second                 ");          
    $RAOCntDesc["avg_oper_process_time         "] = array ("counter_type" => "AVG",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Avg operation processing time                           ");          
    $RAOCntDesc["avg_ops_per_transaction       "] = array ("counter_type" => "AVG",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Avg ops per transaction                                 ");          
    $RAOCntDesc["avg_RBA_search_time           "] = array ("counter_type" => "AVG",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Average RBA search time (ms)                            ");          
    $RAOCntDesc["avg_rep_srvr_turnaround_time  "] = array ("counter_type" => "AVG",  "module" => "LTI", "graph" => 1, "isChecked" => 0,  "display_name" => "Avg Rep Server turnaround time                          ");          
    $RAOCntDesc["avg_sender_oper_process_time  "] = array ("counter_type" => "AVG",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Avg sender operation processing time (ms)               ");          
    $RAOCntDesc["avg_sender_oper_wait_time     "] = array ("counter_type" => "AVG",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Avg sender operation wait time (ms)                     ");          
    $RAOCntDesc["avg_time_per_arch_log_dev_read"] = array ("counter_type" => "AVG",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Average time (ms) per archive log device read           ");          
    $RAOCntDesc["avg_time_per_onln_log_dev_read"] = array ("counter_type" => "AVG",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Average time (ms) per online log device read            ");          
    $RAOCntDesc["avg_time_to_create_distrib    "] = array ("counter_type" => "AVG",  "module" => "LTI", "graph" => 1, "isChecked" => 0,  "display_name" => "Avg time to create distributes                          ");          
    $RAOCntDesc["avg_xlog_oper_wait_time       "] = array ("counter_type" => "AVG",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Avg xlog operation wait time (ms)                       ");          
    $RAOCntDesc["bytes_sent                    "] = array ("counter_type" => "SUM",  "module" => "LTI", "graph" => 1, "isChecked" => 0,  "display_name" => "Total bytes sent                                        ");          
    $RAOCntDesc["Cur_RASD_marked_obj_cache_size"] = array ("counter_type" => "AVG",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Current marked objects cache size                       ");          
    $RAOCntDesc["Curr_RASD_article_cache_size  "] = array ("counter_type" => "AVG",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Current RASD article cache size                         ");          
    $RAOCntDesc["current_marked_obj_cache_size "] = array ("counter_type" => "AVG",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Current RASD marked object cache size                   ");          
    $RAOCntDesc["current_oper_queue_size       "] = array ("counter_type" => "AVG",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Current operation queue size                            ");          
    $RAOCntDesc["current_scan_buffer_size      "] = array ("counter_type" => "AVG",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Current scan buffer size                                ");          
    $RAOCntDesc["current_session_cache_size    "] = array ("counter_type" => "AVG",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Current session cache size                              ");          
    $RAOCntDesc["encoded_column_name_cache_size"] = array ("counter_type" => "AVG",  "module" => "LTI", "graph" => 1, "isChecked" => 0,  "display_name" => "Encoded column name cache size                          ");          
    $RAOCntDesc["input_queue_size              "] = array ("counter_type" => "AVG",  "module" => "LTI", "graph" => 1, "isChecked" => 0,  "display_name" => "Input queue size                                        ");          
    $RAOCntDesc["items_held_in_global_lrucache "] = array ("counter_type" => "AVG",  "module" => "LTM", "graph" => 1, "isChecked" => 0,  "display_name" => "Items held in Global LRUCache                           ");          
    $RAOCntDesc["last_processed_oper_locator   "] = array ("counter_type" => "LAST", "module" => "LR ", "graph" => 0, "isChecked" => 0,  "display_name" => "Last processed operation locator                        ");          
    $RAOCntDesc["last_qid_sent                 "] = array ("counter_type" => "LAST", "module" => "LTI", "graph" => 0, "isChecked" => 0,  "display_name" => "Last QID sent                                           ");          
    $RAOCntDesc["last_transaction_id_sent      "] = array ("counter_type" => "LAST", "module" => "LTI", "graph" => 0, "isChecked" => 0,  "display_name" => "Last transaction id sent                                ");          
    $RAOCntDesc["log_reposition_point_locator  "] = array ("counter_type" => "LAST", "module" => "LR ", "graph" => 0, "isChecked" => 0,  "display_name" => "Log reposition point locator                            ");          
    $RAOCntDesc["Log_scan_checkpoint_set_size  "] = array ("counter_type" => "AVG",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Log scan checkpoint set size                            ");          
    $RAOCntDesc["maint_user_opers_filtered     "] = array ("counter_type" => "SUM",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Total maintenance user operations filtered              ");          
    $RAOCntDesc["number_of_ltl_commands_sent   "] = array ("counter_type" => "SUM",  "module" => "LTI", "graph" => 1, "isChecked" => 0,  "display_name" => "Number of LTL commands sent                             ");          
    $RAOCntDesc["opers_processed               "] = array ("counter_type" => "SUM",  "module" => "LR ", "graph" => 1, "isChecked" => 1,  "display_name" => "Total operations processed                              ");          
    $RAOCntDesc["opers_scanned                 "] = array ("counter_type" => "SUM",  "module" => "LR ", "graph" => 1, "isChecked" => 1,  "display_name" => "Total operations scanned                                ");          
    $RAOCntDesc["opers_skipped                 "] = array ("counter_type" => "SUM",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Total operations skipped                                ");          
    $RAOCntDesc["output_queue_size             "] = array ("counter_type" => "SUM",  "module" => "LTI", "graph" => 1, "isChecked" => 0,  "display_name" => "Output queue size                                       ");          
    $RAOCntDesc["sender_opers_processed        "] = array ("counter_type" => "SUM",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Total sender operations processed                       ");          
    $RAOCntDesc["system_transactions_skipped   "] = array ("counter_type" => "SUM",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Total system transactions skipped                       ");          
    $RAOCntDesc["time_replication_last_started "] = array ("counter_type" => "LAST", "module" => "LTM", "graph" => 0, "isChecked" => 0,  "display_name" => "Time replication last started                           ");          
    $RAOCntDesc["time_statistics_last_reset    "] = array ("counter_type" => "LAST", "module" => "LTM", "graph" => 0, "isChecked" => 0,  "display_name" => "Time statistics last reset                              ");          
    $RAOCntDesc["time_statistics_obtained      "] = array ("counter_type" => "LAST", "module" => "LTM", "graph" => 0, "isChecked" => 0,  "display_name" => "Time statistics obtained                                ");          
    $RAOCntDesc["Total_bytes_read              "] = array ("counter_type" => "SUM",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Total bytes read                                        ");          
    $RAOCntDesc["Total_log_records_filtered    "] = array ("counter_type" => "SUM",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Total log records filtered                              ");          
    $RAOCntDesc["Total_log_records_queued      "] = array ("counter_type" => "SUM",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Total log records queued                                ");          
    $RAOCntDesc["Total_log_records_read        "] = array ("counter_type" => "SUM",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Total log records read                                  ");          
    $RAOCntDesc["Total_online_log_read_time    "] = array ("counter_type" => "SUM",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Total online log read time (ms)                         ");          
    $RAOCntDesc["Total_archive_log_read_time   "] = array ("counter_type" => "SUM",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Total archive log read time (ms)                        ");          
    $RAOCntDesc["TotNum_of_RASD_art_cache_hits "] = array ("counter_type" => "SUM",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Total number of RASD article cache hits                 ");          
    $RAOCntDesc["TotNum_of_RASD_art_cache_miss "] = array ("counter_type" => "SUM",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Total number of RASD article cache misses               ");          
    $RAOCntDesc["TotNum_of_RASD_marked_obj_hits"] = array ("counter_type" => "SUM",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Total number of RASD marked object cache hits           ");          
    $RAOCntDesc["TotNum_of_RASD_marked_obj_miss"] = array ("counter_type" => "SUM",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Total number of RASD marked object cache misses         ");          
    $RAOCntDesc["transactions_aborted          "] = array ("counter_type" => "SUM",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Total transactions aborted (rolled back)                ");          
    $RAOCntDesc["transactions_closed           "] = array ("counter_type" => "SUM",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Total transactions closed                               ");          
    $RAOCntDesc["transactions_committed        "] = array ("counter_type" => "SUM",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Total transactions committed                            ");          
    $RAOCntDesc["transactions_opened           "] = array ("counter_type" => "SUM",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Total transactions opened                               ");          
    $RAOCntDesc["transactions_processed        "] = array ("counter_type" => "SUM",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Total transactions processed                            ");          
    $RAOCntDesc["transactions_skipped          "] = array ("counter_type" => "SUM",  "module" => "LR ", "graph" => 1, "isChecked" => 0,  "display_name" => "Total transactions skipped                              ");          
    $RAOCntDesc["vm__max_memory_used           "] = array ("counter_type" => "MAX",  "module" => "VM ", "graph" => 1, "isChecked" => 0,  "display_name" => "VM % max memory used                                    ");          
    $RAOCntDesc["vm_free_memory                "] = array ("counter_type" => "AVG",  "module" => "VM ", "graph" => 1, "isChecked" => 0,  "display_name" => "VM free memory                                          ");          
    $RAOCntDesc["vm_maximum_memory             "] = array ("counter_type" => "MAX",  "module" => "VM ", "graph" => 1, "isChecked" => 0,  "display_name" => "VM maximum memory                                       ");          
    $RAOCntDesc["vm_memory_usage               "] = array ("counter_type" => "AVG",  "module" => "VM ", "graph" => 1, "isChecked" => 0,  "display_name" => "VM memory usage                                         ");          
    $RAOCntDesc["vm_total_memory_allocated     "] = array ("counter_type" => "MAX",  "module" => "VM ", "graph" => 1, "isChecked" => 0,  "display_name" => "VM total memory allocated                               ");          



    // set the checked boxes values in the CntDesc array
    if (isset($_POST['graphCKB']))
    {
    	$graphCKB = $_POST['graphCKB'];
    	
    	// User has set the checkbox, reinitialize "isChecked" value in array in order to override by user choices
      foreach ($RAOCntDesc as &$value) {
        $value["isChecked"] = 0;
      }
      // Set user choices
      while (list ($key,$val) = @each ($graphCKB)) {
        $RAOCntDesc[$val]["isChecked"]=1;
      }
    }


    include ("./RAO15/sql/RAO_summary_stats1.php");
    //echo $query_RAO_summary_stats; 
    $result=sybase_query($query_RAO_summary_stats1, $pid);
    $row1 = sybase_fetch_array($result);


    include ("./RAO15/sql/RAO_summary_stats2.php");
    //echo $query_RAO_summary_stats2; 
    $result=sybase_query($query_RAO_summary_stats2, $pid);
    $row2 = sybase_fetch_array($result);
    ?>





<div class="boxinmain" style="min-width:900px;float:none">
<div class="boxtop">
<img src="images/boxtop-corner-left.jpg" style="float:left;margin:0px"/>
<div class="title" style="width:90%"> RAO Statistics </div>
<img src="images/boxtop-corner-right.jpg" style="float:right;margin:0px;"/>
</div>

<div class="boxcontent">

<div class="boxbtns" >
<table align="left" cellspacing="2px" width="100%"><tr>
<td> <span style="float:left;margin-left:10px" > (Use checkboxes to graph desired indicator) </span> </td>
<td> <span style="float:right;margin-right:10px" >
	    <img src="images/button_sideLt.gif"  class="btn" height="20px" >
    	<input  style="height:20px; " class="btn" type="button" value="ClearAll" name="ClearBtn" onClick="clearAllCheckBoxes();"> 
        <img src="images/button_sideRt.gif"  class="btn" height="20px">
     </span>
</td>
</tr></table>
</div>



<div class="statMainTable" style="overflow:visible">

<div class="statMainInfo">
  <table border="0" cellspacing="2" cellpadding="4" width="100%" >
    <tr>
    <td valign="top" > 

    <tr>
    <td class="infobox" colspan="2" align="center"> 
      <table border="0" cellspacing="1" cellpadding="0" class="statInfo" style="width:600px;text-align:left">
          <?php display_counter("last_processed_oper_locator   ", $RAOCntDesc, $row1); ?>
          <?php display_counter("log_reposition_point_locator  ", $RAOCntDesc, $row1); ?>
          <?php display_counter("last_qid_sent                 ", $RAOCntDesc, $row1); ?>
          <?php display_counter("last_transaction_id_sent      ", $RAOCntDesc, $row1); ?>
          <?php display_counter("time_replication_last_started ", $RAOCntDesc, $row1); ?>
          <?php display_counter("time_statistics_last_reset    ", $RAOCntDesc, $row1); ?>
          <?php display_counter("time_statistics_obtained      ", $RAOCntDesc, $row1); ?>
      </table>
    </td>
    </tr>

    <tr valign=top>
    <td class="infobox" > <center>
      <table border="0" cellspacing="1" cellpadding="0" class="statInfo" style="width:400px;text-align:left">
          <?php display_counter("avg_changeset_send_time       ", $RAOCntDesc, $row2); ?>
          <?php display_counter("avg_oper_process_time         ", $RAOCntDesc, $row2); ?>
          <?php display_counter("avg_ops_per_transaction       ", $RAOCntDesc, $row2); ?>
          <?php display_counter("avg_sender_oper_process_time  ", $RAOCntDesc, $row2); ?>
          <?php display_counter("avg_sender_oper_wait_time     ", $RAOCntDesc, $row2); ?>
          <?php display_counter("avg_xlog_oper_wait_time       ", $RAOCntDesc, $row2); ?>
          <?php display_counter("current_marked_obj_cache_size ", $RAOCntDesc, $row2); ?>
          <?php display_counter("current_oper_queue_size       ", $RAOCntDesc, $row2); ?>
          <?php display_counter("current_scan_buffer_size      ", $RAOCntDesc, $row2); ?>
          <?php display_counter("current_session_cache_size    ", $RAOCntDesc, $row2); ?>
          <?php display_counter("maint_user_opers_filtered     ", $RAOCntDesc, $row2); ?>
          <?php display_counter("opers_processed               ", $RAOCntDesc, $row2); ?>
          <?php display_counter("opers_scanned                 ", $RAOCntDesc, $row2); ?>
          <?php display_counter("opers_skipped                 ", $RAOCntDesc, $row2); ?>
          <?php display_counter("sender_opers_processed        ", $RAOCntDesc, $row2); ?>
          <?php display_counter("system_transactions_skipped   ", $RAOCntDesc, $row2); ?>
          <?php display_counter("transactions_aborted          ", $RAOCntDesc, $row2); ?>
          <?php display_counter("transactions_closed           ", $RAOCntDesc, $row2); ?>
          <?php display_counter("transactions_committed        ", $RAOCntDesc, $row2); ?>
          <?php display_counter("transactions_opened           ", $RAOCntDesc, $row2); ?>
          <?php display_counter("transactions_processed        ", $RAOCntDesc, $row2); ?>
          <?php display_counter("transactions_skipped          ", $RAOCntDesc, $row2); ?>
          <?php display_counter("avg_RBA_search_time           ", $RAOCntDesc, $row2); ?>
          <?php display_counter("avg_number_of_bytes_per_record", $RAOCntDesc, $row2); ?>
          <?php display_counter("avg_number_of_bytes_read_per_s", $RAOCntDesc, $row2); ?>
          <?php display_counter("avg_nb_of_log_records_per_ckpt", $RAOCntDesc, $row2); ?>
          <?php display_counter("avg_nb_of_s_between_log_r_ckpt", $RAOCntDesc, $row2); ?>
          <?php display_counter("avg_time_per_arch_log_dev_read", $RAOCntDesc, $row2); ?>
          <?php display_counter("avg_time_per_onln_log_dev_read", $RAOCntDesc, $row2); ?>
          <?php display_counter("Curr_RASD_article_cache_size  ", $RAOCntDesc, $row2); ?>
          <?php display_counter("Cur_RASD_marked_obj_cache_size", $RAOCntDesc, $row2); ?>
          <?php display_counter("Log_scan_checkpoint_set_size  ", $RAOCntDesc, $row2); ?>
          <?php display_counter("Total_bytes_read              ", $RAOCntDesc, $row2); ?>
          <?php display_counter("Total_log_records_filtered    ", $RAOCntDesc, $row2); ?>
          <?php display_counter("Total_log_records_queued      ", $RAOCntDesc, $row2); ?>
          <?php display_counter("Total_log_records_read        ", $RAOCntDesc, $row2); ?>
          <?php display_counter("TotNum_of_RASD_art_cache_hits ", $RAOCntDesc, $row2); ?>
          <?php display_counter("TotNum_of_RASD_art_cache_miss ", $RAOCntDesc, $row2); ?>
          <?php display_counter("TotNum_of_RASD_marked_obj_hits", $RAOCntDesc, $row2); ?>
          <?php display_counter("TotNum_of_RASD_marked_obj_miss", $RAOCntDesc, $row2); ?>
          <?php display_counter("Total_online_log_read_time    ", $RAOCntDesc, $row2); ?>
          <?php display_counter("Total_archive_log_read_time   ", $RAOCntDesc, $row2); ?>


      </table></center>
    </td>
    <td class="infobox"> <center>
      <table border="0" cellspacing="1" cellpadding="0" class="statInfo" style="width:400px;text-align:left">
          <?php display_counter("avg_bytes_second_during_trans ", $RAOCntDesc, $row2); ?>
          <?php display_counter("avg_data_arrival_time         ", $RAOCntDesc, $row2); ?>
          <?php display_counter("avg_ltl_buffer_cache_time     ", $RAOCntDesc, $row2); ?>
          <?php display_counter("avg_ltl_buffer_size           ", $RAOCntDesc, $row2); ?>
          <?php display_counter("avg_ltl_command_size          ", $RAOCntDesc, $row2); ?>
          <?php display_counter("avg_ltl_commands_buffer       ", $RAOCntDesc, $row2); ?>
          <?php display_counter("avg_ltl_commands_sec          ", $RAOCntDesc, $row2); ?>
          <?php display_counter("avg_ltm_buffer_utilization    ", $RAOCntDesc, $row2); ?>
          <?php display_counter("avg_rep_srvr_turnaround_time  ", $RAOCntDesc, $row2); ?>
          <?php display_counter("avg_time_to_create_distrib    ", $RAOCntDesc, $row2); ?>
          <?php display_counter("encoded_column_name_cache_size", $RAOCntDesc, $row2); ?>
          <?php display_counter("input_queue_size              ", $RAOCntDesc, $row2); ?>
          <?php display_counter("number_of_ltl_commands_sent   ", $RAOCntDesc, $row2); ?>
          <?php display_counter("output_queue_size             ", $RAOCntDesc, $row2); ?>
          <?php display_counter("bytes_sent                    ", $RAOCntDesc, $row2); ?>
          <?php display_counter("items_held_in_global_lrucache ", $RAOCntDesc, $row2); ?>
          <?php display_counter("vm__max_memory_used           ", $RAOCntDesc, $row2); ?>
          <?php display_counter("vm_free_memory                ", $RAOCntDesc, $row2); ?>
          <?php display_counter("vm_maximum_memory             ", $RAOCntDesc, $row2); ?>
          <?php display_counter("vm_memory_usage               ", $RAOCntDesc, $row2); ?>
          <?php display_counter("vm_total_memory_allocated     ", $RAOCntDesc, $row2); ?>
      </table> </center>
    </td>
    </tr>
    </table>
</DIV>
</DIV>
</DIV>
</DIV>



    <P>
    <H2> Indicator graphs </H2>

    <?php
    // Display indicator graph if  checked 
    foreach ($RAOCntDesc as $CntName => $aCntDesc)
    {
        $type = $aCntDesc["counter_type"] ;
        if ( $aCntDesc["isChecked"] == 1  ) {
            ?>
            <p>
            <img src='<?php echo "./RAO15/graph_RAOcounter.php?counter_name=".$CntName."&type=".$type."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
            </p>
            <?php
        }
    }
    ?>
