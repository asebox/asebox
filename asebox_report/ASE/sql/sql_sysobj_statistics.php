<?php

$query = "set rowcount ".$rowcnt."
select dbname, objtype, objname, crdate=max(crdate)
from ".$ServerName."_SysObj
where Timestamp >='".$StartTimestamp."'        
and Timestamp <'".$EndTimestamp."'        
and (dbname  like '".$filterDbName."'  or '".$filterDbName."'='')
and (objtype like '".$filterObjType."' or '".$filterObjType."'='')
and (objname like '".$filterObjName."' or '".$filterObjName."'='')
and (crdate  like '".$filterCrDate."'  or '".$filterCrDate."'='')
group by dbname, objtype, objname
order by ".$orderObj."
set rowcount 0";

$query = "
select type, name, crdate
from ".$ServerName."_SysObjects
where type not in ('S')";


  $query_name = "sysobj_statistics";

?>
