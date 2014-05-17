<?php
$query = "select 
Device=substring(LogicalName,1,30),
PReads= sum (convert(numeric(20,0),Reads)), 
AReads = sum (convert(numeric(20,0),APFReads )), 
PWrites= sum (convert(numeric(20,0),Writes)), 
avgserv_ms=str(sum(IOTime*1.) / sum(1.*Reads+APFReads+Writes),8,2),
DevSemaphoreContentionPCT=case when sum(1.*DevSemaphoreRequests)<=0. then '-1.' else str(sum(DevSemaphoreWaits*100.)/sum(DevSemaphoreRequests*1.),8,2) end
from ".$ServerName."_DevIO
where Timestamp >='".$StartTimestamp."'        
and Timestamp <'".$EndTimestamp."' 
and (LogicalName like '".$filterdevice."' or '".$filterdevice."'='')
and LogicalName not like 'SYSDEV$[_][_]%' /* ignore crazy values for devices of archive db's */
group by LogicalName
having sum(convert(numeric(20,0),1.*Reads+APFReads+Writes)) >0
order by ".$order_by;

  $query_name = "device_statistics";

?>
