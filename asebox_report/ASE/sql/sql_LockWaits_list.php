<?php
$query = "set rowcount ".$rowcnt."
select DbName, TabName, LockScheme, Pagetype, StatName,
WaitTime_ms=sum(1.*WaitTime),
sumWaits=sum(Waits),
AvgWaitTime_ms = str(sum(1.*WaitTime)/sum(Waits),14,0)
from ".$ServerName."_LockWaits
where Timestamp >='".$StartTimestamp."'        
and Timestamp <'".$EndTimestamp."'        
and (DbName like '".$filterDbName."' or '".$filterDbName."' ='')
and (TabName like '".$filterTabName."' or '".$filterTabName."' ='')
group by DbName, TabName, LockScheme, Pagetype, StatName
order by WaitTime_ms desc
set rowcount 0";

  $query_name = "LockWaits_list";
?>
