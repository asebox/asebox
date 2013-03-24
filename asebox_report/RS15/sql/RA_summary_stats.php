<?php
$query_RA_summary_stats =
"select A.ID,
instance_id, instance,
Commands_received               = sum(Commands_received            ), 
Applied_commands                = sum(Applied_commands             ), 
Request_commands                = sum(Request_commands             ), 
System_commands                 = sum(System_commands              ), 
Mini_Abort_commands             = sum(Mini_Abort_commands          ), 
Dump_Load_commands              = sum(Dump_Load_commands           ), 
Purge_Open_commands             = sum(Purge_Open_commands          ), 
Route_RCL_commands              = sum(Route_RCL_commands           ), 
Enable_Replication_rs_markers   = sum(Enable_Replication_rs_markers), 
Updates_to_rs_locater           = sum(Updates_to_rs_locater        ), 
Bytes_received                  = sum(Bytes_received               ), 
Connection_packet_size          = sum(Connection_packet_size       ), 
Buffers_received                = sum(Buffers_received             ), 
Empty_packets_received          = sum(Empty_packets_received       ), 
RepAgent_yield_time             = sum(RepAgent_yield_time          ), 
AvgRepAgent_yield_time_ms       = str(avg(AvgRepAgent_yield_time_ms    ),10,2), 
AvgRepAgent_write_wait_time_ms        = str(avg(AvgRepAgent_write_wait_time_ms     ),10,2), 
SQLDDL_commands                 = sum(SQLDDL_commands              ), 
rs_tickets_processed            = sum(rs_tickets_processed         ), 
AvgRepAgentRecvTime_ms          = str(avg(AvgRepAgentRecvTime_ms  ),10,2), 
AvgExcution_time_ms                = str(avg(AvgExcution_time_ms ),12,2), 
SQLDML_update_commands          = sum(SQLDML_update_commands       ), 
SQLDML_delete_commands          = sum(SQLDML_delete_commands       ), 
SQLDML_select_into_commands     = sum(SQLDML_select_into_commands  ), 
SQLDML_insert_select_commands   = sum(SQLDML_insert_select_commands),

RepAgentParseTime    = sum(RepAgentParseTime ),
RepAgentNrmTime      = sum(RepAgentNrmTime   ),
RepAgentPackTime     = sum(RepAgentPackTime  ),
TotalBytesReceived   = sum(TotalBytesReceived),
RAWaitNRMTime        = sum(RAWaitNRMTime     )

from (
    select S.ID, instance_id, instance,
    Commands_received               = case when counter_id=58000 then sum(convert(numeric(14,0),counter_obs)  ) else null end, 
    Applied_commands                = case when counter_id=58001 then sum(convert(numeric(14,0),counter_obs)  ) else null end, 
    Request_commands                = case when counter_id=58002 then sum(convert(numeric(14,0),counter_obs)  ) else null end, 
    System_commands                 = case when counter_id=58003 then sum(convert(numeric(14,0),counter_obs)  ) else null end, 
    Mini_Abort_commands             = case when counter_id=58004 then sum(convert(numeric(14,0),counter_obs)  ) else null end, 
    Dump_Load_commands              = case when counter_id=58005 then sum(convert(numeric(14,0),counter_obs)  ) else null end, 
    Purge_Open_commands             = case when counter_id=58006 then sum(convert(numeric(14,0),counter_obs)  ) else null end, 
    Route_RCL_commands              = case when counter_id=58007 then sum(convert(numeric(14,0),counter_obs)  ) else null end, 
    Enable_Replication_rs_markers   = case when counter_id=58008 then sum(convert(numeric(14,0),counter_obs)  ) else null end, 
    Updates_to_rs_locater           = case when counter_id=58009 then sum(convert(numeric(14,0),counter_obs)  ) else null end, 
    Bytes_received                  = case when counter_id=58011 then sum(convert(numeric(14,0),counter_total)) else null end, 
    Connection_packet_size          = case when counter_id=58012 then avg(convert(numeric(14,0),counter_total)) else null end, 
    Buffers_received                = case when counter_id=58013 then sum(convert(numeric(14,0),counter_obs)  ) else null end, 
    Empty_packets_received          = case when counter_id=58014 then sum(convert(numeric(14,0),counter_obs)  ) else null end, 
    RepAgent_yield_time             = case when counter_id=58016 then sum(convert(numeric(14,0),counter_obs)  ) else null end, 
    AvgRepAgent_yield_time_ms             = case when counter_id=58016 then sum(1.*counter_total/counter_obs) else null end, 
    AvgRepAgent_write_wait_time_ms        = case when counter_id=58019 then sum(1.*counter_total/counter_obs) else null end, 
    SQLDDL_commands                 = case when counter_id=58021 then sum(convert(numeric(14,0),counter_obs)  ) else null end, 
    rs_tickets_processed            = case when counter_id=58022 then sum(convert(numeric(14,0),counter_obs)  ) else null end, 
    AvgRepAgentRecvTime_ms             = case when counter_id=58023 then sum(1.*counter_total/counter_obs) else null end, 
    AvgExcution_time_ms                = case when counter_id=58025 then sum(1.*counter_total/counter_obs) else null end, 
    SQLDML_update_commands          = case when counter_id=58027 then sum(convert(numeric(14,0),counter_obs)  ) else null end, 
    SQLDML_delete_commands          = case when counter_id=58028 then sum(convert(numeric(14,0),counter_obs)  ) else null end, 
    SQLDML_select_into_commands     = case when counter_id=58029 then sum(convert(numeric(14,0),counter_obs)  ) else null end, 
    SQLDML_insert_select_commands   = case when counter_id=58030 then sum(convert(numeric(14,0),counter_obs)  ) else null end,

    RepAgentParseTime               = case when counter_id=58031 then sum(convert(numeric(14,0),counter_total)  ) else null end,
    RepAgentNrmTime                 = case when counter_id=58033 then sum(convert(numeric(14,0),counter_total)  ) else null end,
    RepAgentPackTime                = case when counter_id=58035 then sum(convert(numeric(14,0),counter_total)  ) else null end,
    TotalBytesReceived              = case when counter_id=58037 then sum(convert(numeric(14,0),counter_total)  ) else null end,
    RAWaitNRMTime                   = case when counter_id=58038 then sum(convert(numeric(14,0),counter_total)  ) else null end

    from ".$ServerName."_Instances I, ".$ServerName."_RSStats S
    where I.ID=S.ID
    and instance like 'REP AGENT, %'
    and S.Timestamp >='".$StartTimestamp."'
    and S.Timestamp <='".$EndTimestamp."'
    ".$ID_search_clause."
    group by S.Timestamp, S.ID, instance_id, instance, counter_id
) A
group by A.ID, instance_id, instance
order by instance_id";

?>