<?php
    $query="";
    
    $filter_clause ="
and (name    like '".$filtername   ."' or '".$filtername   ."' = '')
and (appname like '".$filterappname."' or '".$filterappname."' = '')";
        
$query = $query . "
select Name        = name      
      ,Application = appname   
      ,RangeID     = rangeid   
      ,LimitID     = limitid   
      ,Enforced    = enforced  
      ,Action      = action    
      ,Limit       = limitvalue
      ,Scope       = scope     
from ".$ServerName."_SysResLimits
where (1=1)" . $filter_clause."
order by ".$orderRLL;

$query_name = "resourcelimits_limits";
?>
