<?php
$query = "
declare @max_ts       datetime
select @max_ts = '".$selectedTimestamp."'

select starttm=convert(varchar,starttime,109),elapsed=datediff(ss, starttime, Timestamp),dbname,dbid,spid,page,name,xloid, endtime=convert(varchar,Timestamp,109)
from ".$ServerName."_LogsHold
--where  Timestamp = @max_ts
--where Timestamp >='".$StartTimestamp."'        
--and Timestamp <'".$EndTimestamp."'
where  Timestamp > dateadd(mi, -1, @max_ts)
and    Timestamp < dateadd(mi,  1, @max_ts)
and (dbname like '".$filterdbname."' or '".$filterdbname."' = '')
and (dbid = convert(int,'".$filterdbid."') or '".$filterdbid."' = '')
and (spid = convert(int,'".$filterspid."') or '".$filterspid."' = '')
and (page = convert(int,'".$filterpage."') or '".$filterpage."' = '')
and (name like '".$filtername."' or '".$filtername."' = '')
and (xloid = convert(int,'".$filterxloid."') or '".$filterxloid."' = '')
and starttime != ''
order by ".$orderSysLogsHold;

$query_name = "SysLogsHold";

?>
