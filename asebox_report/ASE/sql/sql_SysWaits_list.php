<?php
$query = "select ClassDesc=C.Description,EventDesc=I.Description, SumWaitTime=sum(1.*WaitTime), Sumwaits = sum(1.*Waits),
AvgWaitTime_ms=sum(1000.*WaitTime) / sum(1.*Waits)
from ".$ServerName."_SysWaits W, ".$ServerName."_WEvInf I, ".$ServerName."_WClassInf C
where W.Timestamp >='".$StartTimestamp."'
and W.Timestamp <'".$EndTimestamp."'
and convert(smallint,W.WaitEventID) = I.WaitEventID
and I.WaitClassID=C.WaitClassID
and lower(C.Description) != 'waiting for internal system event'
group by C.Description,I.Description
order by ".$ordersyswaits;

$query_name = "SysWaits_list";

?>
