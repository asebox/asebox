<?php
    $filter_clause ="
and (Program   like '".$filterprogram."'   or '".$filterprogram."'  = '')
and (Message   like '".$filtermessage."'   or '".$filtermessage."'  = '')
and (LogType   like '".$filterlogtype."'   or '".$filterlogtype."'  = '')
and (Username  like '".$filterusername."'  or '".$filteusername."'  = '')
and ( (abs(datediff( ms, LogTime, StartTime )/1000.0)) > convert(numeric(10,2), '".$filtermintime."') or '".$filtermintime."'       = '')";
//    and ('Spid'      =convert(int,'".$filterspid."')  or '".$filterspid."'          = '')";
        
//$orderPrc=$order_by;

$query_rep = "
select 
     StartTime
    ,LogTime  
    ,Time       = abs(datediff( ms, LogTime, StartTime )/1000.0) 
    ,Program 
    ,Message 
    ,LogType  
    ,Username
    ,Spid    
    ,Scope
    ,Rate       = case when LogTime=StartTime then 0 
                  else Scope / abs(datediff( ms, LogTime, StartTime )/1000.0)
                  end
into  #applog                  
from ".$ServerName."_AppLog
where LogTime >=convert(datetime, '".$StartTimestamp."')        
and   LogTime < convert(datetime, '".$EndTimestamp."')
".$filter_clause."
select 
     StartTime  = convert(varchar(10), StartTime, 103) + ' ' + convert(varchar(8), StartTime, 8)  
    ,LogTime    = convert(varchar(10), LogTime,   103) + ' ' + convert(varchar(8), LogTime,   8)  
    ,TimeTxt    = convert(varchar, convert(numeric(10,2), Time ))
    ,Program 
    ,Message 
    ,LogType  
    ,Username
    ,Spid    
    ,Scope
    ,Rate      
from  #applog     
order by ".$orderPrc;

$query = "
select 
     StartTime  = convert(varchar(10), StartTime, 103) + ' ' + convert(varchar(8), StartTime, 8)  
    ,LogTime    = convert(varchar(10), LogTime,   103) + ' ' + convert(varchar(8), LogTime,   8)  
    ,TimeTxt    = convert(varchar, convert(numeric(10,2), Time ))
    ,Program 
    ,Message 
    ,LogType  
    ,Username
    ,Spid    
    ,Scope
    ,Rate      
from  #applog     
order by ".$orderPrc;


//OLD
$query2 = "
select 
     StartTime  = convert(varchar(10), StartTime, 103) + ' ' + convert(varchar(8), StartTime, 8)  
    ,LogTime    = convert(varchar(10), LogTime,   103) + ' ' + convert(varchar(8), LogTime,   8)  
    ,Time       = convert(varchar, convert(numeric(10,2), abs(datediff( ms, LogTime, StartTime )/1000.0)    ))
    ,Program 
    ,Message 
    ,LogType  
    ,Username
    ,Spid    
    ,Scope
    ,Rate       = 0
from ".$ServerName."_AppLog
where LogTime >=convert(datetime, '".$StartTimestamp."')        
and   LogTime < convert(datetime, '".$EndTimestamp."')
".$filter_clause."
order by ".$orderPrc;



$query_name = "applog_statistics";
?>
