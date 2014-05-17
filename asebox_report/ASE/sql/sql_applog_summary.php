<?php
$query="";
        
$filter_clause ="
and (Program    like '".$filterprogram."'   or '".$filterprogram."'       = '')
and ( (abs(datediff( ms, LogTime, StartTime )/1000.0)) > convert(numeric(10,2), '".$filtermintime."') or '".$filtermintime."'       = '')";
//    and ('Spid'      =convert(int,'".$filterspid."')  or '".$filterspid."'          = '')";
        
//$orderPrc=$order_by;    

$query = $query . "
if object_id('#applogsum') is not null drop table #applogsum

select Program,
       cnt      = count(*),
       tot_time = sum( abs( datediff( ss, StartTime, LogTime ))  ),
       avg_time = avg( abs( datediff( ss, StartTime, LogTime ))  ),
       max_time = max( abs( datediff( ss, StartTime, LogTime ))  )
into   #applogsum
from ".$ServerName."_AppLog
where LogTime >='".$StartTimestamp."'        
and   LogTime <'".$EndTimestamp."'
--and   LogType = 'FIN'
".$filter_clause."
group by Program

select * 
from   #applogsum
order by ".$orderPrc;

$query_name = "applog_summary";
?>