<?php
$query="";
        
$filter_clause ="
and (Program    like '".$filterprogram."'   or '".$filterprogram."'       = '')
and ( (abs(datediff( ms, LogTime, StartTime )/1000.0)) > convert(numeric(10,2), '".$filtermintime."') or '".$filtermintime."'       = '')";
//    and ('Spid'      =convert(int,'".$filterspid."')  or '".$filterspid."'          = '')";
        
//$orderPrc=$order_by;    

$query = $query . "
if object_id('#applogsum') is not null drop table #applogsum".$EnvNum."
create table #applogsum".$ENV."
( Program   varchar(30) null,
  cnt       integer,
  tot_time  numeric(16),
  avg_time  integer,
  max_time  integer
)

insert into #applogsum".$ENV."
select
       Program,
       cnt      = count(*),
       tot_time = sum( abs( datediff( ss, StartTime, LogTime ))  ),
       avg_time = avg( abs( datediff( ss, StartTime, LogTime ))  ),
       aax_time = max( abs( datediff( ss, StartTime, LogTime ))  )
from ".$ServerName."_AppLog
where StartTime >='".$StartTimestamp."'        
and   StartTime <'".$EndTimestamp."'
and   LogType = 'FIN'
group by Program";

$query_name = "applog_summary";
?>






















