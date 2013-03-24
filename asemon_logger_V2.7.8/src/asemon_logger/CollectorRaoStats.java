/**
* <p>CollectorRaoStats</p>
* <p>Asemon_logger : class managing Replication Agent counters and for computing differences</p>
* <p>Copyright: Jean-Paul Martin (jpmartin@sybase.com) Copyright (c) 2004</p>
* @version 2.6.4
*/

package asemon_logger;
import java.sql.*;
import java.text.SimpleDateFormat;

public class CollectorRaoStats extends Collector {
    String sql;

  String srvName;
  String structName;

  Timestamp oldTime;
  Timestamp newTime;
  Long interval;
  
  StatRAO currentStat;

  public CollectorRaoStats (MonitoredSRV ms, MetricDescriptor md) {
      super (ms, md);
      srvName = msrv.srvNormalized;
      structName = metricDescriptor.metricName;
  }

  class StatRAO {
    private double     avg_changeset_send_time                   ;
    private double     avg_operation_processing_time             ;
    private double     avg_ops_per_transaction                   ;
    private double     avg_sender_operation_processing_time      ;
    private double     avg_sender_operation_wait_time            ;
    private double     avg_xlog_operation_wait_time              ;
    private int        current_marked_objects_cache_size         ;
    private int        current_operation_queue_size              ;
    private int        current_scan_buffer_size                  ;
    private int        current_session_cache_size                ;
    private String     last_processed_operation_locator          ;
    private String     log_reposition_point_locator              ;
    private int        total_maintenance_user_operations_filtered;
    private int        total_operations_processed                ;
    private int        total_operations_scanned                  ;
    private int        total_operations_skipped                  ;
    private int        total_sender_operations_processed         ;
    private int        total_system_transactions_skipped         ;
    private int        total_transactions_aborted                ;
    private int        total_transactions_closed                 ;
    private int        total_transactions_committed              ;
    private int        total_transactions_opened                 ;
    private int        total_transactions_processed              ;
    private int        total_transactions_skipped                ;
    private double     avg_bytes_second_during_transmission      ;
    private double     avg_data_arrival_time                     ;
    private double     avg_ltl_buffer_cache_time                 ;
    private double     avg_ltl_buffer_size                       ;
    private double     avg_ltl_command_size                      ;
    private double     avg_ltl_commands_buffer                   ;
    private double     avg_ltl_commands_sec                      ;
    private double     avg_ltm_buffer_utilization                ;
    private double     avg_rep_server_turnaround_time            ;
    private double     avg_time_to_create_distributes            ;
    private double     encoded_column_name_cache_size            ;
    private double     input_queue_size                          ;
    private String     last_qid_sent                             ;
    private String     last_transaction_id_sent                  ;
    private int        number_of_ltl_commands_sent               ;
    private double     output_queue_size                         ;
    private double     total_bytes_sent                          ;
    private int        items_held_in_global_lrucache             ;
    private String     time_replication_last_started             ;
    private String     time_statistics_last_reset                ;
    private String     time_statistics_obtained                  ;
    private double     vm__max_memory_used                       ;
    private double        vm_free_memory                            ;
    private double        vm_maximum_memory                         ;
    private double        vm_memory_usage                           ;
    private double        vm_total_memory_allocated                 ;

    private double avg_RBA_search_time                                 ;
    private double avg_number_of_bytes_per_record                      ;
    private double avg_number_of_bytes_read_per_s                 ;
    private double avg_nb_of_log_records_per_ckpt            ;
    private double avg_nb_of_s_between_log_r_ckpt;
    private double avg_time_per_arch_log_dev_read                ;
    private double avg_time_per_onln_log_dev_read                 ;
    private double Curr_RASD_article_cache_size                     ;
    private double Cur_RASD_marked_obj_cache_size               ;
    private double Log_scan_checkpoint_set_size                        ;
    private double Total_archive_log_read_time                         ;
    private double Total_bytes_read                                    ;
    private double Total_log_records_filtered                          ;
    private double Total_log_records_queued                            ;
    private double Total_log_records_read                              ;
    private double TotNum_of_RASD_art_cache_hits             ;
    private double TotNum_of_RASD_art_cache_miss           ;
    private double TotNum_of_RASD_marked_obj_hits       ;
    private double TotNum_of_RASD_marked_obj_miss     ;
    private double Total_online_log_read_time                          ;


    StatRAO () {
        avg_changeset_send_time                                   = 0.0   ;    
        avg_operation_processing_time                             = 0.0   ;
        avg_ops_per_transaction                                   = 0.0   ;
        avg_sender_operation_processing_time                      = 0.0   ;
        avg_sender_operation_wait_time                            = 0.0   ;
        avg_xlog_operation_wait_time                              = 0.0   ;
        current_marked_objects_cache_size                         = 0   ;   
        current_operation_queue_size                              = 0   ;   
        current_scan_buffer_size                                  = 0   ;   
        current_session_cache_size                                = 0   ;   
        last_processed_operation_locator                          = ""   ;  
        log_reposition_point_locator                              = ""   ;  
        total_maintenance_user_operations_filtered                = 0   ;   
        total_operations_processed                                = 0   ;   
        total_operations_scanned                                  = 0   ;   
        total_operations_skipped                                  = 0   ;   
        total_sender_operations_processed                         = 0   ;   
        total_system_transactions_skipped                         = 0   ;   
        total_transactions_aborted                                = 0   ;   
        total_transactions_closed                                 = 0   ;   
        total_transactions_committed                              = 0   ;   
        total_transactions_opened                                 = 0   ;   
        total_transactions_processed                              = 0   ;   
        total_transactions_skipped                                = 0   ;   
        avg_bytes_second_during_transmission                      = 0.0   ;
        avg_data_arrival_time                                     = 0.0   ;
        avg_ltl_buffer_cache_time                                 = 0.0   ;
        avg_ltl_buffer_size                                       = 0.0   ;
        avg_ltl_command_size                                      = 0.0   ;
        avg_ltl_commands_buffer                                   = 0.0   ;
        avg_ltl_commands_sec                                      = 0.0   ;
        avg_ltm_buffer_utilization                                = 0.0   ;
        avg_rep_server_turnaround_time                            = 0.0   ;
        avg_time_to_create_distributes                            = 0.0   ;
        encoded_column_name_cache_size                            = 0.0   ;
        input_queue_size                                          = 0.0   ;
        last_qid_sent                                             = "";     
        last_transaction_id_sent                                  = "";     
        number_of_ltl_commands_sent                               = 0;      
        output_queue_size                                         = 0.0;   
        total_bytes_sent                                          = 0.0;   
        items_held_in_global_lrucache                             = 0;      
        time_replication_last_started                             = "";   
        time_statistics_last_reset                                = "";   
        time_statistics_obtained                                  = "";   
        vm__max_memory_used                                       = 0.0;   
        vm_free_memory                                            = 0.0;
        vm_maximum_memory                                         = 0.0;
        vm_memory_usage                                           = 0.0;
        vm_total_memory_allocated                                 = 0.0;

        avg_RBA_search_time                                  = 0.0;
        avg_number_of_bytes_per_record                       = 0.0;
        avg_number_of_bytes_read_per_s                  = 0.0;
        avg_nb_of_log_records_per_ckpt             = 0.0;
        avg_nb_of_s_between_log_r_ckpt = 0.0;
        avg_time_per_arch_log_dev_read                 = 0.0;
        avg_time_per_onln_log_dev_read                  = 0.0;
        Curr_RASD_article_cache_size                      = 0.0;
        Cur_RASD_marked_obj_cache_size                = 0.0;
        Log_scan_checkpoint_set_size                         = 0.0;
        Total_archive_log_read_time                          = 0.0;
        Total_bytes_read                                     = 0.0;
        Total_log_records_filtered                           = 0.0;
        Total_log_records_queued                             = 0.0;
        Total_log_records_read                               = 0.0;
        TotNum_of_RASD_art_cache_hits              = 0.0;
        TotNum_of_RASD_art_cache_miss            = 0.0;
        TotNum_of_RASD_marked_obj_hits        = 0.0;
        TotNum_of_RASD_marked_obj_miss      = 0.0;
        Total_online_log_read_time                           = 0.0;


    }
    
  }

  
  private String convertDate(String d)throws Exception {
      try {
          SimpleDateFormat sdf1 = new SimpleDateFormat("EEE MMM dd HH:mm:ss z yyyy");
          SimpleDateFormat sdf2 = new SimpleDateFormat("MM/dd/yyyy HH:mm:ss");
          //System.out.println(sdf1.parse(d));
          //System.out.println(sdf2.format(sdf1.parse(d)));
          return sdf2.format(sdf1.parse(d));
      }
      catch (Exception e) {
          return "";
      }
  }

  private int computeDiff(int newval, int oldval) {
      // If newval is lower than oldval, means that statistics have been reset, so use only the new value
      if (newval < oldval) return newval;
      else return newval - oldval; // compute the difference
  }

  private double computeDiffDouble (double newval, double oldval) {
      // If newval is lower than oldval, means that statistics have been reset, so use only the new value
      if (newval < oldval) return newval;
      else return newval - oldval; // compute the difference
  }
  
  public void getMetrics () throws Exception {
    int nbRowsToInsert = 0;  // Used to count number of rows inserted in archive database
    archRows = -1 ; // Real number of rows inserted in archive database. -1 in case of error or missing config params

    try  {

        // Allocate stat structure
        currentStat = new StatRAO();
        
	// Get values
        Statement stmt = msrv.monSrvConn.createStatement();
    	ResultSet rs = stmt.executeQuery("ra_statistics");
        newTime= new Timestamp(System.currentTimeMillis());
       	while (rs.next()) {
            String stat=rs.getString(2);
            if (rs.getString(3)==null) continue; // Skip row if Value is null

            if (stat.equalsIgnoreCase("Average RBA search time (ms)"))       currentStat.avg_RBA_search_time      = rs.getFloat(3); // New indic V15
            if (stat.equalsIgnoreCase("Average number of bytes per record"))       currentStat.avg_number_of_bytes_per_record      = rs.getFloat(3); // New indic V15
            if (stat.equalsIgnoreCase("Average number of bytes read per second"))       currentStat.avg_number_of_bytes_read_per_s      = rs.getFloat(3); // New indic V15
            if (stat.equalsIgnoreCase("Average number of log records per checkpoint"))       currentStat.avg_nb_of_log_records_per_ckpt      = rs.getFloat(3); // New indic V15
            if (stat.equalsIgnoreCase("Average number of seconds between log record checkpoints"))       currentStat.avg_nb_of_s_between_log_r_ckpt      = rs.getFloat(3); // New indic V15
            if (stat.equalsIgnoreCase("Average time (ms) per archive log device read"))       currentStat.avg_time_per_arch_log_dev_read      = rs.getFloat(3); // New indic V15
            if (stat.equalsIgnoreCase("Average time (ms) per online log device read"))       currentStat.avg_time_per_onln_log_dev_read      = rs.getFloat(3); // New indic V15
            if (stat.equalsIgnoreCase("avg bytes/second during transmission"))       currentStat.avg_bytes_second_during_transmission      = rs.getFloat(3);
            if (stat.equalsIgnoreCase("avg changeset send time (ms)"))               currentStat.avg_changeset_send_time                   = rs.getFloat(3);
            if (stat.equalsIgnoreCase("avg ltl buffer cache time"))                  currentStat.avg_ltl_buffer_cache_time                 = rs.getFloat(3);
            if (stat.equalsIgnoreCase("avg ltl buffer size"))                        currentStat.avg_ltl_buffer_size                       = rs.getFloat(3);
            if (stat.equalsIgnoreCase("avg ltl command size"))                       currentStat.avg_ltl_command_size                      = rs.getFloat(3);
            if (stat.equalsIgnoreCase("avg ltl commands/buffer"))                    currentStat.avg_ltl_commands_buffer                   = rs.getFloat(3);
            if (stat.equalsIgnoreCase("avg ltl commands/sec"))                       currentStat.avg_ltl_commands_sec                      = rs.getFloat(3);
            if (stat.equalsIgnoreCase("avg ltm buffer utilization (%)"))             currentStat.avg_ltm_buffer_utilization                = rs.getFloat(3);
            if (stat.equalsIgnoreCase("avg rep server turnaround time"))             currentStat.avg_rep_server_turnaround_time            = rs.getFloat(3);
            if (stat.equalsIgnoreCase("avg data arrival time"))                      currentStat.avg_data_arrival_time                     = rs.getFloat(3);
            if (stat.equalsIgnoreCase("avg operation processing time"))              currentStat.avg_operation_processing_time             = rs.getFloat(3);
            if (stat.equalsIgnoreCase("avg ops per transaction"))                    currentStat.avg_ops_per_transaction                   = rs.getFloat(3);
            if (stat.equalsIgnoreCase("avg sender operation processing time (ms)"))  currentStat.avg_sender_operation_processing_time      = rs.getFloat(3);
            if (stat.equalsIgnoreCase("avg sender operation wait time (ms)"))        currentStat.avg_sender_operation_wait_time            = rs.getFloat(3);
            if (stat.equalsIgnoreCase("avg time to create distributes"))             currentStat.avg_time_to_create_distributes            = rs.getFloat(3);
            if (stat.equalsIgnoreCase("avg xlog operation wait time (ms)"))          currentStat.avg_xlog_operation_wait_time              = rs.getFloat(3);
            if (stat.equalsIgnoreCase("Current RASD article cache size"))       currentStat.Curr_RASD_article_cache_size      = rs.getFloat(3); // New indic V15
            if (stat.equalsIgnoreCase("Current RASD marked object cache size"))       currentStat.Cur_RASD_marked_obj_cache_size      = rs.getFloat(3); // New indic V15
            if (stat.equalsIgnoreCase("current marked objects cache size"))          currentStat.current_marked_objects_cache_size         = rs.getInt(3);
            if (stat.equalsIgnoreCase("current operation queue size"))               currentStat.current_operation_queue_size              = rs.getInt(3);
            if (stat.equalsIgnoreCase("current scan buffer size")&&(!rs.getString(3).equalsIgnoreCase("null")))                   currentStat.current_scan_buffer_size                  = rs.getInt(3);
            if (stat.equalsIgnoreCase("current session cache size")&&(!rs.getString(3).equalsIgnoreCase("null")))                 currentStat.current_session_cache_size                = rs.getInt(3);
            if (stat.equalsIgnoreCase("encoded column name cache size"))             currentStat.encoded_column_name_cache_size            = rs.getFloat(3);
            if (stat.equalsIgnoreCase("input queue size"))                           currentStat.input_queue_size                          = rs.getFloat(3);
            if (stat.equalsIgnoreCase("items held in global lrucache"))              currentStat.items_held_in_global_lrucache             = rs.getInt(3);
            if (stat.equalsIgnoreCase("last qid sent"))                              currentStat.last_qid_sent                             = rs.getString(3);
            if (stat.equalsIgnoreCase("last processed operation locator"))           currentStat.last_processed_operation_locator          = rs.getString(3);
            if (stat.equalsIgnoreCase("last transaction id sent"))                   currentStat.last_transaction_id_sent                  = rs.getString(3);
            if (stat.equalsIgnoreCase("log reposition point locator"))               currentStat.log_reposition_point_locator              = rs.getString(3);
            if (stat.equalsIgnoreCase("Log scan checkpoint set size"))       currentStat.Log_scan_checkpoint_set_size      = rs.getFloat(3); // New indic V15
            if (stat.equalsIgnoreCase("number of ltl commands sent"))                currentStat.number_of_ltl_commands_sent               = rs.getInt(3);
            if (stat.equalsIgnoreCase("output queue size"))                          currentStat.output_queue_size                         = rs.getFloat(3);
            if (stat.equalsIgnoreCase("time replication last started"))              currentStat.time_replication_last_started             = rs.getString(3);
            if (stat.equalsIgnoreCase("time statistics last reset"))                 currentStat.time_statistics_last_reset                = rs.getString(3);
            if (stat.equalsIgnoreCase("time statistics obtained"))                   currentStat.time_statistics_obtained                  = rs.getString(3);
            if (stat.equalsIgnoreCase("Total archive log read time (ms)"))       currentStat.Total_archive_log_read_time      = rs.getFloat(3); // New indic V15
            if (stat.equalsIgnoreCase("Total bytes read"))       currentStat.Total_bytes_read      = rs.getFloat(3); // New indic V15
            if (stat.equalsIgnoreCase("total bytes sent"))                           currentStat.total_bytes_sent                          = rs.getFloat(3);
            if (stat.equalsIgnoreCase("Total log records filtered"))       currentStat.Total_log_records_filtered      = rs.getFloat(3); // New indic V15
            if (stat.equalsIgnoreCase("Total log records queued"))       currentStat.Total_log_records_queued      = rs.getFloat(3); // New indic V15
            if (stat.equalsIgnoreCase("Total log records read"))       currentStat.Total_log_records_read      = rs.getFloat(3); // New indic V15
            if (stat.equalsIgnoreCase("total maintenance user operations filtered")) currentStat.total_maintenance_user_operations_filtered= rs.getInt(3);
            if (stat.equalsIgnoreCase("Total number of RASD article cache hits"))       currentStat.TotNum_of_RASD_art_cache_hits      = rs.getFloat(3); // New indic V15
            if (stat.equalsIgnoreCase("Total number of RASD article cache misses"))       currentStat.TotNum_of_RASD_art_cache_miss      = rs.getFloat(3); // New indic V15
            if (stat.equalsIgnoreCase("Total number of RASD marked object cache hits"))       currentStat.TotNum_of_RASD_marked_obj_hits      = rs.getFloat(3); // New indic V15
            if (stat.equalsIgnoreCase("Total number of RASD marked object cache misses"))       currentStat.TotNum_of_RASD_marked_obj_miss      = rs.getFloat(3); // New indic V15
            if (stat.equalsIgnoreCase("Total online log read time (ms)"))       currentStat.Total_online_log_read_time      = rs.getFloat(3); // New indic V15
            if (stat.equalsIgnoreCase("total operations processed"))                 currentStat.total_operations_processed                = rs.getInt(3);
            if (stat.equalsIgnoreCase("total operations scanned"))                   currentStat.total_operations_scanned                  = rs.getInt(3);
            if (stat.equalsIgnoreCase("total operations skipped"))                   currentStat.total_operations_skipped                  = rs.getInt(3);
            if (stat.equalsIgnoreCase("total sender operations processed"))          currentStat.total_sender_operations_processed         = rs.getInt(3);
            if (stat.equalsIgnoreCase("total system transactions skipped"))          currentStat.total_system_transactions_skipped         = rs.getInt(3);
            if (stat.equalsIgnoreCase("total transactions aborted (rolled back)  ")) currentStat.total_transactions_aborted                = rs.getInt(3);
            if (stat.equalsIgnoreCase("total transactions closed"))                  currentStat.total_transactions_closed                 = rs.getInt(3);
            if (stat.equalsIgnoreCase("total transactions committed"))               currentStat.total_transactions_committed              = rs.getInt(3);
            if (stat.equalsIgnoreCase("total transactions opened"))                  currentStat.total_transactions_opened                 = rs.getInt(3);
            if (stat.equalsIgnoreCase("total transactions processed"))               currentStat.total_transactions_processed              = rs.getInt(3);
            if (stat.equalsIgnoreCase("total transactions skipped"))                 currentStat.total_transactions_skipped                = rs.getInt(3);
            if (stat.equalsIgnoreCase("vm % max memory used"))                       currentStat.vm__max_memory_used                       = rs.getFloat(3);
            if (stat.equalsIgnoreCase("vm free memory"))                             currentStat.vm_free_memory                            = rs.getFloat(3);
            if (stat.equalsIgnoreCase("vm maximum memory"))                          currentStat.vm_maximum_memory                         = rs.getFloat(3);
            if (stat.equalsIgnoreCase("vm memory usage"))                            currentStat.vm_memory_usage                           = rs.getFloat(3);
            if (stat.equalsIgnoreCase("vm total memory allocated"))                  currentStat.vm_total_memory_allocated                 = rs.getFloat(3);
        }
        stmt.executeUpdate("ra_statistics reset");
        stmt.close();
        //System.out.println("Oper scanned : "+currentStat.total_operations_scanned);
    }
    catch (Exception e) {
    	throw e;
    }

    if (oldTime!=null) {

        // Compute the time interval in ms
        long newTsMilli = newTime.getTime();
        long oldTsMilli = oldTime.getTime();
        int newTsNano   = newTime.getNanos();
        int oldTsNano   = oldTime.getNanos();
        // Check if TsMilli has really ms precision (not the case before JDK 1.4)
        if ( (newTsMilli - (newTsMilli/1000)*1000) == newTsNano/1000000)
          // JDK > 1.3.1
          interval = newTsMilli - oldTsMilli ;
        else
          interval = newTsMilli - oldTsMilli + (newTsNano-oldTsNano)/1000000;
    }
    else interval = (long)0;
        
        
        
        Statement stmtArch =null;
        // Insert values into database
        StringBuffer sql = new StringBuffer("insert into "+srvName+"_"+structName +
            "(Timestamp," +
            "Interval," +
            "avg_changeset_send_time," +
            "avg_oper_process_time," +
            "avg_ops_per_transaction," +
            "avg_sender_oper_process_time," +
            "avg_sender_oper_wait_time," +
            "avg_xlog_oper_wait_time," +
            "current_marked_obj_cache_size," +
            "current_oper_queue_size," +
            "current_scan_buffer_size," +
            "current_session_cache_size," +
            "last_processed_oper_locator," +
            "log_reposition_point_locator," +
            "maint_user_opers_filtered," +
            "opers_processed," +
            "opers_scanned," +
            "opers_skipped," +
            "sender_opers_processed," +
            "system_transactions_skipped," +
            "transactions_aborted," +
            "transactions_closed," +
            "transactions_committed," +
            "transactions_opened," +
            "transactions_processed," +
            "transactions_skipped," +
            "avg_bytes_second_during_trans," +
            "avg_data_arrival_time," +
            "avg_ltl_buffer_cache_time," +
            "avg_ltl_buffer_size," +
            "avg_ltl_command_size," +
            "avg_ltl_commands_buffer," +
            "avg_ltl_commands_sec," +
            "avg_ltm_buffer_utilization," +
            "avg_rep_srvr_turnaround_time," +
            "avg_time_to_create_distrib," +
            "encoded_column_name_cache_size," +
            "input_queue_size," +
            "last_qid_sent," +
            "last_transaction_id_sent," +
            "number_of_ltl_commands_sent," +
            "output_queue_size," +
            "bytes_sent," +
            "items_held_in_global_lrucache," +
            "time_replication_last_started," +
            "time_statistics_last_reset," +
            "time_statistics_obtained," +
            "vm__max_memory_used," +
            "vm_free_memory," +
            "vm_maximum_memory," +
            "vm_memory_usage," +
            "vm_total_memory_allocated," +

            "avg_RBA_search_time," +
            "avg_number_of_bytes_per_record," +
            "avg_number_of_bytes_read_per_s," +
            "avg_nb_of_log_records_per_ckpt," +
            "avg_nb_of_s_between_log_r_ckpt," +
            "avg_time_per_arch_log_dev_read," +
            "avg_time_per_onln_log_dev_read," +
            "Curr_RASD_article_cache_size," +
            "Cur_RASD_marked_obj_cache_size," +
            "Log_scan_checkpoint_set_size," +
            "Total_archive_log_read_time," +
            "Total_bytes_read," +
            "Total_log_records_filtered," +
            "Total_log_records_queued," +
            "Total_log_records_read," +
            "TotNum_of_RASD_art_cache_hits," +
            "TotNum_of_RASD_art_cache_miss," +
            "TotNum_of_RASD_marked_obj_hits," +
            "TotNum_of_RASD_marked_obj_miss," +
            "Total_online_log_read_time)" +

            " values (");

        sql = sql.append("'"+newTime+"'");
        sql = sql.append(","+interval);

        sql = sql.append(","  + currentStat.avg_changeset_send_time                    + "");
        sql = sql.append(","  + currentStat.avg_operation_processing_time              + "");
        sql = sql.append(","  + currentStat.avg_ops_per_transaction                    + "");
        sql = sql.append(","  + currentStat.avg_sender_operation_processing_time       + "");
        sql = sql.append(","  + currentStat.avg_sender_operation_wait_time             + "");
        sql = sql.append(","  + currentStat.avg_xlog_operation_wait_time               + "");
        sql = sql.append(","  + currentStat.current_marked_objects_cache_size          + "");
        sql = sql.append(","  + currentStat.current_operation_queue_size               + "");
        sql = sql.append(","  + currentStat.current_scan_buffer_size                   + "");
        sql = sql.append(","  + currentStat.current_session_cache_size                 + "");
        sql = sql.append(",'" + currentStat.last_processed_operation_locator           + "'");
        sql = sql.append(",'" + currentStat.log_reposition_point_locator               + "'");
        sql = sql.append(","  + currentStat.total_maintenance_user_operations_filtered);
        sql = sql.append(","  + currentStat.total_operations_processed                );
        sql = sql.append(","  + currentStat.total_operations_scanned                  );
        sql = sql.append(","  + currentStat.total_operations_skipped                  );
        sql = sql.append(","  + currentStat.total_sender_operations_processed         );
        sql = sql.append(","  + currentStat.total_system_transactions_skipped         );
        sql = sql.append(","  + currentStat.total_transactions_aborted                );
        sql = sql.append(","  + currentStat.total_transactions_closed                 );
        sql = sql.append(","  + currentStat.total_transactions_committed              );
        sql = sql.append(","  + currentStat.total_transactions_opened                 );
        sql = sql.append(","  + currentStat.total_transactions_processed              );
        sql = sql.append(","  + currentStat.total_transactions_skipped                );
        sql = sql.append(","  + currentStat.avg_bytes_second_during_transmission       + "");
        sql = sql.append(","  + currentStat.avg_data_arrival_time                      + "");
        sql = sql.append(","  + currentStat.avg_ltl_buffer_cache_time                  + "");
        sql = sql.append(","  + currentStat.avg_ltl_buffer_size                        + "");
        sql = sql.append(","  + currentStat.avg_ltl_command_size                       + "");
        sql = sql.append(","  + currentStat.avg_ltl_commands_buffer                    + "");
        sql = sql.append(","  + currentStat.avg_ltl_commands_sec                       + "");
        sql = sql.append(","  + currentStat.avg_ltm_buffer_utilization                 + "");
        sql = sql.append(","  + currentStat.avg_rep_server_turnaround_time             + "");
        sql = sql.append(","  + currentStat.avg_time_to_create_distributes             + "");
        sql = sql.append(","  + currentStat.encoded_column_name_cache_size             + "");
        sql = sql.append(","  + currentStat.input_queue_size                           + "");
        sql = sql.append(",'" + currentStat.last_qid_sent                              + "'");
        sql = sql.append(",'" + currentStat.last_transaction_id_sent                   + "'");
        sql = sql.append(","  + currentStat.number_of_ltl_commands_sent                           );
        sql = sql.append(","  + currentStat.output_queue_size                          + "");
        sql = sql.append(","  + currentStat.total_bytes_sent                                      );
        sql = sql.append(","  + currentStat.items_held_in_global_lrucache              + "");
        sql = sql.append(",'" + convertDate(currentStat.time_replication_last_started)              + "'");
        sql = sql.append(",'" + convertDate(currentStat.time_statistics_last_reset)                 + "'");
        sql = sql.append(",'" + convertDate(currentStat.time_statistics_obtained)                   + "'");
        sql = sql.append(","  + currentStat.vm__max_memory_used                        + "");
        sql = sql.append(","  + currentStat.vm_free_memory                             + "");
        sql = sql.append(","  + currentStat.vm_maximum_memory                          + "");
        sql = sql.append(","  + currentStat.vm_memory_usage                            + "");
        sql = sql.append(","  + currentStat.vm_total_memory_allocated                  + "");

        sql = sql.append(","  + currentStat.avg_RBA_search_time                                   + "");
        sql = sql.append(","  + currentStat.avg_number_of_bytes_per_record                        + "");
        sql = sql.append(","  + currentStat.avg_number_of_bytes_read_per_s                   + "");
        sql = sql.append(","  + currentStat.avg_nb_of_log_records_per_ckpt              + "");
        sql = sql.append(","  + currentStat.avg_nb_of_s_between_log_r_ckpt  + "");
        sql = sql.append(","  + currentStat.avg_time_per_arch_log_dev_read                  + "");
        sql = sql.append(","  + currentStat.avg_time_per_onln_log_dev_read                   + "");
        sql = sql.append(","  + currentStat.Curr_RASD_article_cache_size                       + "");
        sql = sql.append(","  + currentStat.Cur_RASD_marked_obj_cache_size                 + "");
        sql = sql.append(","  + currentStat.Log_scan_checkpoint_set_size                          + "");
        sql = sql.append(","  + currentStat.Total_archive_log_read_time                                  + "");
        sql = sql.append(","  + currentStat.Total_bytes_read                                             + "");
        sql = sql.append(","  + currentStat.Total_log_records_filtered                                   + "");
        sql = sql.append(","  + currentStat.Total_log_records_queued                                     + "");
        sql = sql.append(","  + currentStat.Total_log_records_read                                       + "");
        sql = sql.append(","  + currentStat.TotNum_of_RASD_art_cache_hits                      + "");
        sql = sql.append(","  + currentStat.TotNum_of_RASD_art_cache_miss                    + "");
        sql = sql.append(","  + currentStat.TotNum_of_RASD_marked_obj_hits                + "");
        sql = sql.append(","  + currentStat.TotNum_of_RASD_marked_obj_miss              + "");
        sql = sql.append(","  + currentStat.Total_online_log_read_time                                   + "");

        sql = sql.append(")");

        //System.out.println (sql);

        // Get an archive connection from the pool
        CnxMgr.ArchCnx aArchCnx = CnxMgr.archCnxPool.getArchCnx(false);
        archCnxWaitTime = aArchCnx.waitedFor;
	    try {
            if (stmtArch == null) stmtArch = aArchCnx.archive_conn.createStatement();
            stmtArch.executeUpdate(sql.toString());
            nbRowsToInsert++;
        }
        catch (SQLException sqle) {
              AsemonSQLException asemonEx = new AsemonSQLException(sqle.getMessage(), sqle.getSQLState(), sqle.getErrorCode(), "ARCH", "CollectorRaoStats");
              throw asemonEx;
        }
        finally {
          // Return archive connection to the pool
          archCnxActiveTime =  CnxMgr.archCnxPool.putArchCnx(aArchCnx);
        }


    
    currentStat=null;
    archRows = nbRowsToInsert;
    oldTime = newTime;


  } // end getMetrics





}