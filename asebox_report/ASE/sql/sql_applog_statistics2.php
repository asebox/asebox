<?php
$query="";
        
        $filter_clause ="
	and (Program    like '".$filterprogram."'   or '".$filterprogram."'       = '')
	and (Message    like '".$filtermessage."'   or '".$filtermessage."'       = '')
	and ('Label'    like '".$filterlogtype."'   or '".$filterlogtype."'       = '')
	and ('Username' like '".$filterusername."'  or '".$filteusername."'       = '')
	and ( (abs(datediff( ms, LogTime, StartTime )/1000.0)) > convert(numeric(10,2), '".$filtermintime."') or '".$filtermintime."'       = '')";
//    and ('Spid'      =convert(int,'".$filterspid."')  or '".$filterspid."'          = '')";
        
        //$orderPrc=$order_by;
    

$query = $query . "
select Program,
       cnt      = count(*),
       tot_time = sum( abs( datediff( ss, StartTime, LogTime ))  ),
       avg_time = avg( abs( datediff( ss, StartTime, LogTime ))  ),
       aax_time = max( abs( datediff( ss, StartTime, LogTime ))  )
from ".$ServerName."_AppLog
where LogTime >='".$StartTimestamp."'        
and   LogTime <'".$EndTimestamp."'
and   LogType = 'FIN'
group by Program
order by Program";
//order by ".$orderPrc;
//".$filter_clause."


$query_name = "applog_summary";

?>