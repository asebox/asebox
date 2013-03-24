<?php
$query = "set rowcount ".$rowcnt."
select
    dbname           ,
    owner            ,
    tabname          ,
    Counter=sum(counter)          ,
    c1               ,
    c2               ,
    c3               ,
    c4               ,
    c5               ,
    c6               ,
    c7               ,
    c8               ,
    c9               ,
    c10              ,
    c11              ,
    c12              ,
    c13              ,
    c14              ,
    c15              ,
    c16              ,
    c17              ,
    c18              ,
    c19              ,
    c20              ,
    c21              ,
    c22              ,
    c23              ,
    c24              ,
    c25              ,
    c26              ,
    c27              ,
    c28              ,
    c29              ,
    c30              ,
    c31              
from ".$ServerName."_MissStats
where Timestamp >='".$StartTimestamp."'        
	and Timestamp <'".$EndTimestamp."'        
	and (dbname       like '".$filterDbName."' or '".$filterDbName."' = '')
	and (owner        like '".$filterOwner."' or '".$filterOwner."' = '')
	and (tabname       like '".$filterTabName."' or '".$filterTabName."' = '')
group by
    dbname           ,
    owner            ,
    tabname          ,
    c1               ,
    c2               ,
    c3               ,
    c4               ,
    c5               ,
    c6               ,
    c7               ,
    c8               ,
    c9               ,
    c10
order by ".$orderMissStats.
" set rowcount 0";

  $query_name = "missing_statistics";

?>







