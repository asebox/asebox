<?php
// summary stats for all RSI's
$query_RSI_summary_stats =
"select A.ID, A.instance_id, A.instance,

BytesSent         = sum (BytesSent        ),
PacketsSent         = sum (PacketsSent        ),
MsgsSent         = sum (MsgsSent        ),
AvgSendPTTime_ms         = sum (SendPTTime        )/sum(PacketsSent)

from (
    select S.ID, I.instance_id, I.instance,

    BytesSent         = case when counter_id=4000 then 1.*counter_total else null end,
    PacketsSent         = case when counter_id=4002 then 1.*counter_obs else null end,
    MsgsSent         = case when counter_id=4004 then 1.*counter_obs else null end,
    SendPTTime         = case when counter_id=4009 then 1.*counter_total else null end

    from ".$ServerName."_Instances I, ".$ServerName."_RSStats S
    where I.ID=S.ID
    and instance like 'RSI,%'
    and S.Timestamp >='".$StartTimestamp."'
    and S.Timestamp <='".$EndTimestamp."'
    ".$ID_search_clause."
    ".$ID_clause."
    group by S.Timestamp, S.ID, I.instance_id, I.instance, counter_id
) A
group by A.ID, A.instance_id, A.instance
order by A.instance_id, A.instance";

?>