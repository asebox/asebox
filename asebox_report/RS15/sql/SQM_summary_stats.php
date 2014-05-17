<?php
// summary stats for all SQM's
$query_SQM_summary_stats =
"select A.ID, A.instance_id, A.instance,

CmdsWritten         = sum (CmdsWritten        ),
AvgCmdSize             = str(avg (AvgCmdSize), 12,2),
MaxCmdSize             = max (MaxCmdSize            ),
BlocksWritten       = sum (BlocksWritten      ),
BlocksFullWrite     = sum (BlocksFullWrite    ),
BytesWritten        = sum (BytesWritten       ),
MaxSegsActive          = max (SegsActive         ),
UpdsRsoqid          = sum (UpdsRsoqid         ),
AvgSQMWriteTime_ms        = str(sum (1.*SQMWriteTime  )/sum(SQMWrites),12,2)

from (
    select S.ID, I.instance_id, I.instance,

    CmdsWritten         = case when counter_id=6000 then sum(convert(numeric(14,0),counter_obs)) else null end,    -- status= 652
    AvgCmdSize             = case when counter_id=6049 then sum(convert(numeric(14,0),avg_ttl_obs) ) else null end,    -- status=1028
    MaxCmdSize             = case when counter_id=6049 then sum(convert(numeric(14,0),counter_max)) else null end,    -- status=1028
    BlocksWritten       = case when counter_id=6002 then sum(convert(numeric(14,0),counter_obs)) else null end,    -- status= 652
    BlocksFullWrite     = case when counter_id=6041 then sum(convert(numeric(14,0),counter_obs)) else null end,    -- status= 652
    BytesWritten        = case when counter_id=6004 then sum(convert(numeric(14,0),counter_total)) else null end,    -- status=1164
    SegsActive          = case when counter_id=6020 then sum(convert(numeric(14,0),counter_last)) else null end,    -- status=1052
    UpdsRsoqid          = case when counter_id=6036 then sum(convert(numeric(14,0),counter_obs)) else null end,    -- status= 516
    SQMWrites           = case when counter_id=6057 then sum(counter_obs) else null end,    -- status=  32
    SQMWriteTime        = case when counter_id=6057 then sum(counter_total) else null end    -- status=  32

    from ".$ServerName."_Instances I, ".$ServerName."_RSStats S
    where I.ID=S.ID
    and instance like '".$SQM_type."%'
    and S.Timestamp >='".$StartTimestamp."'
    and S.Timestamp <='".$EndTimestamp."'
    ".$ID_search_clause."
    ".$ID_clause."
    group by S.Timestamp, S.ID, I.instance_id, I.instance, counter_id
) A
group by A.ID, A.instance_id, A.instance
order by A.instance_id, A.instance";

?>