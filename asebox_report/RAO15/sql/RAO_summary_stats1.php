<?php
// summary stats for RAO
$query_RAO_summary_stats1 =
"select 
last_processed_oper_locator    ,
log_reposition_point_locator   ,
last_qid_sent                  ,
last_transaction_id_sent       ,
time_replication_last_started  ,
time_statistics_last_reset     ,
time_statistics_obtained       
    from ".$ServerName."_RAOSTATS
    where Timestamp= (select max(Timestamp) from ".$ServerName."_RAOSTATS 
                      where Timestamp >='".$StartTimestamp."'
                      and Timestamp <='".$EndTimestamp."')

";

?>
