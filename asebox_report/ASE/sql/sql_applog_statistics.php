<?php

        $query="";
        
        $filter_clause ="
	and (Program   like '".$filterprogram."'        or '".$filterprogram."'       = '')
	and (Message   like '".$filtermessage."'        or '".$filtermessage."'       = '')
	and ('Label'     like '".$filterlogtype."'          or '".$filterlogtype."'         = '')
	and ('Username'  like '".$filterusername."'       or '".$filteusername."'       = '')
	and ( (abs(datediff( ms, LogTime, StartTime )/1000.0)) > convert(numeric(10,2), '".$filtermintime."') or '".$filtermintime."'       = '')";
//    and ('Spid'      =convert(int,'".$filterspid."')  or '".$filterspid."'          = '')";
        
        //$orderPrc=$order_by;
    

        $query = $query . "
	select 
      LogTime    = convert(varchar(10), LogTime, 103) + ' ' + convert(varchar(8), LogTime, 8)  
     ,StartTime  = convert(varchar(10), StartTime, 103) + ' ' + convert(varchar(8), StartTime, 8)  
     ,Time       = convert(varchar,  convert(numeric(10,2), abs(datediff( ms, LogTime, StartTime )/1000.0)    ))
     ,Program 
     ,Message 
     ,LogType 
     ,Username
     ,Spid    
     ,Scope   
	from ".$ServerName."_AppLog
	where LogTime >='".$StartTimestamp."'        
	and   LogTime <'".$EndTimestamp."'        	
	".$filter_clause."
	order by ".$orderPrc;

  $query_name = "applog_statistics";

?>
