<?php
	$query = 
	"select dbname,
  ra_log_waits             = sum(case when field_name='ra_log_waits            ' then sumval else 0 end),
  ra_sum_log_wait          = sum(case when field_name='ra_sum_log_wait         ' then sumval else 0 end),
  ra_longest_log_wait      = sum(case when field_name='ra_longest_log_wait     ' then maxval else 0 end),
  ra_truncpt_moved         = sum(case when field_name='ra_truncpt_moved        ' then sumval else 0 end),
  ra_truncpt_gotten        = sum(case when field_name='ra_truncpt_gotten       ' then sumval else 0 end),
  ra_io_wait               = sum(case when field_name='ra_io_wait              ' then sumval else 0 end),
  ra_sum_io_wait           = sum(case when field_name='ra_sum_io_wait          ' then sumval else 0 end),
  ra_longest_io_wait       = sum(case when field_name='ra_longest_io_wait      ' then maxval else 0 end),
  ra_rs_connect            = sum(case when field_name='ra_rs_connect           ' then sumval else 0 end),
  ra_fail_rs_connect       = sum(case when field_name='ra_fail_rs_connect      ' then sumval else 0 end),
  ra_packets_sent          = sum(case when field_name='ra_packets_sent         ' then sumval else 0 end),
  ra_full_packets_sent     = sum(case when field_name='ra_full_packets_sent    ' then sumval else 0 end),
  ra_sum_packet            = sum(case when field_name='ra_sum_packet           ' then sumval else 0 end),
  ra_largest_packet        = sum(case when field_name='ra_largest_packet       ' then maxval else 0 end),
  ra_log_records_scanned   = sum(case when field_name='ra_log_records_scanned  ' then sumval else 0 end),
  ra_log_records_processed = sum(case when field_name='ra_log_records_processed' then sumval else 0 end),
  ra_open_xact             = sum(case when field_name='ra_open_xact            ' then sumval else 0 end),
  ra_maintuser_xact        = sum(case when field_name='ra_maintuser_xact       ' then sumval else 0 end),
  ra_commit_xact           = sum(case when field_name='ra_commit_xact          ' then sumval else 0 end),
  ra_abort_xact            = sum(case when field_name='ra_abort_xact           ' then sumval else 0 end),
  ra_prepare_xact          = sum(case when field_name='ra_prepare_xact         ' then sumval else 0 end),
  ra_xupdate_processed     = sum(case when field_name='ra_xupdate_processed    ' then sumval else 0 end),
  ra_xinsert_processed     = sum(case when field_name='ra_xinsert_processed    ' then sumval else 0 end),
  ra_xdelete_processed     = sum(case when field_name='ra_xdelete_processed    ' then sumval else 0 end),
  ra_xexec_processed       = sum(case when field_name='ra_xexec_processed      ' then sumval else 0 end),
  ra_xcmdtext_processed    = sum(case when field_name='ra_xcmdtext_processed   ' then sumval else 0 end),
  ra_xwrtext_processed     = sum(case when field_name='ra_xwrtext_processed    ' then sumval else 0 end),
  ra_xrowimage_processed   = sum(case when field_name='ra_xrowimage_processed  ' then sumval else 0 end),
  ra_xclr_processed        = sum(case when field_name='ra_xclr_processed       ' then sumval else 0 end),
  ra_bckward_schema        = sum(case when field_name='ra_bckward_schema       ' then sumval else 0 end),
  ra_sum_bckward_wait      = sum(case when field_name='ra_sum_bckward_wait     ' then sumval else 0 end),
  ra_longest_bckward_wait  = sum(case when field_name='ra_longest_bckward_wait ' then maxval else 0 end),
  ra_forward_schema        = sum(case when field_name='ra_forward_schema       ' then sumval else 0 end),
  ra_sum_forward_wait      = sum(case when field_name='ra_sum_forward_wait     ' then sumval else 0 end),
  ra_longest_forward_wait  = sum(case when field_name='ra_longest_forward_wait ' then maxval else 0 end)
  
  from (
  select DBs.dbname, FLDs.field_name, sumval, maxval
                                                                                                                              
  from                                                                                                                        
  (                                                                                                                           
  select dbid=convert(int,substring(grpname, 2, datalength(grpname)))".$add1.",field_id, sumval=sum(1.*d_value), maxval=max(1.*d_value)
  from ".$ServerName."_SysMon                                                                                                          
  where Timestamp between '".$StartTimestamp."' and '".$EndTimestamp."'                                                               
  and grpname like 'A%'                                                                                                       
  group by convert(int,substring(grpname, 2, datalength(grpname))),field_id                                                   
  ) RAStats,                                                                                                                  
  (select distinct dbid,dbname from ".$ServerName."_AseDbSpce where  Timestamp between '".$StartTimestamp."' and '".$EndTimestamp."') DBs,      
  (select field_id, field_name from ".$ServerName."_SysMonFld where group_name LIKE 'repagent_0') FLDs                                 
                                                                                                                              
  where RAStats.dbid  = DBs.dbid                                                                                              
  and RAStats.field_id= FLDs.field_id                                                                                         
  ) results
  group by dbname";

  $query_name = "ra_from_sysmon_statistics";
	
?>
