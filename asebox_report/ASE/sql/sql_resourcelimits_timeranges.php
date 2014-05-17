<?php
    $query="";
    
    $filter_clause ="
and (name    like '".$filtername   ."' or '".$filtername   ."' = '')";

$query = $query . "
select Name      = name       
      ,Id        = id         
      ,Startday  = startday   
      ,Endday    = endday     
      ,Starttime = starttime  
      ,Endtime   = endtime    
from ".$ServerName."_SysTimeRanges
where (1=1)" . $filter_clause."
order by ".$orderRLL;

$query_name = "resourcelimits_timeranges";
?>


 
 
 
 
   
 
 