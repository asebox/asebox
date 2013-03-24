<?php
// summary stats for all RSIUSER's
$query_RSIUSER_summary_stats =
"select A.ID, A.instance_id, A.instance,

RSIUBytsRcvd         = sum (RSIUBytsRcvd        ),
RSIUBuffsRcvd         = sum (RSIUBuffsRcvd        ),
RSIUMsgRecv         = sum (RSIUMsgRecv        )

from (
    select S.ID, I.instance_id, I.instance,

    RSIUBytsRcvd         = case when counter_id=59016 then 1.*counter_total else null end,
    RSIUBuffsRcvd         = case when counter_id=59013 then 1.*counter_obs else null end,
    RSIUMsgRecv         = case when counter_id=59001 then 1.*counter_obs else null end

    from ".$ServerName."_Instances I, ".$ServerName."_RSStats S
    where I.ID=S.ID
    and instance like 'RSI USER,%'
    and S.Timestamp >='".$StartTimestamp."'
    and S.Timestamp <='".$EndTimestamp."'
    ".$ID_search_clause."
    ".$ID_clause."
    group by S.Timestamp, S.ID, I.instance_id, I.instance, counter_id
) A
group by A.ID, A.instance_id, A.instance
order by A.instance_id, A.instance";

?>