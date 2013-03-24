<?php
// summary stats for all SQM's
$query_SQM_summary_stats =
"select A.ID, A.instance_id, A.instance,


CmdsRead            = sum(CmdsRead            ),
BlocksRead          = sum(BlocksRead          ),
BlocksReadCached    = sum(BlocksReadCached    ),
AvgSQMRReadTime_ms        = sum(SQMRReadTime)/sum(SQMRReads),
Reads = sum(SQMRReads)

from (
    select S.ID, I.instance_id, I.instance,

    CmdsRead               =case when counter_id=62000 then sum(convert(numeric(14,0), counter_obs)) else null end,
    BlocksRead             =case when counter_id=62002 then sum(convert(numeric(14,0), counter_obs)) else null end,
    BlocksReadCached       =case when counter_id=62004 then sum(convert(numeric(14,0), counter_obs)) else null end,
    SQMRReads              =case when counter_id=62011 then sum(counter_obs) else null end,
    SQMRReadTime           =case when counter_id=62011 then sum(counter_total) else null end

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