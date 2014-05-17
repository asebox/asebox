<?php
$query = "select starttm=convert(varchar,starttime,109),elapsed=datediff(ss, starttime, max(Timestamp)),dbname,dbid,spid,page,name,xloid, endtime=convert(varchar,max(Timestamp),109)
from ".$ServerName."_LogsHold
where Timestamp >='".$StartTimestamp."'        
and Timestamp <'".$EndTimestamp."'
and (dbname like '".$filterdbname."' or '".$filterdbname."' = '')
and (dbid = convert(int,'".$filterdbid."') or '".$filterdbid."' = '')
and (spid = convert(int,'".$filterspid."') or '".$filterspid."' = '')
and (page = convert(int,'".$filterpage."') or '".$filterpage."' = '')
and (name like '".$filtername."' or '".$filtername."' = '')
and (xloid = convert(int,'".$filterxloid."') or '".$filterxloid."' = '')
and starttime != ''
group by dbname,dbid,spid,page,starttime,name,xloid
order by ".$orderSysLogsHold;

$query_name = "SysLogsHold";

?>
