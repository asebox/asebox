<?php
    $filter_clause ="
and (Name    like '".$filtername."'    or '".$filtername."'   = '')
and (Value   like '".$filtervalue."'   or '".$filtervalue."'  = '')
and (Descr   like '".$filterdescr."'   or '".$filterdescr."'  = '')
and (Type    like '".$filtertype."'    or '".$filtertype."'   = '')
and (Updated = '".   $filterupdated."' or '".$filterupdated."'= '')";

//$orderPrc=$order_by;

$query = "
select Name    
      ,Value
      ,Descr   
      ,Type    
      ,Updated 
from ".$ServerName."_AppConfig
where (1=1)
".$filter_clause."
order by ".$orderPrc;

$query = "
select Name    
      ,Value                  
      ,Descr   
      ,Type    
      ,Updated 
from sybsystemprocs..boxappconfig
where (1=1)
".$filter_clause."
order by ".$orderPrc;

$query_name = "appconfig";
?>

