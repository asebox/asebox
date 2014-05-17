<?php
    $filter_clause ="
and (name    like '".$filterdevice ."' or '".$filterdevice ."' = '')";

$query = "
declare @NbPgParMeg int                                                                                                   
select @NbPgParMeg = 1048576 / low                                                                                        
from ".$ServerName."_SptValues                                                                                            
where  number = 1                                                                                                         
and    type = 'E'                                                                                                         
--select 'Nombre de Pages par Mega: ', @NbPgParMeg                                                                        
declare @vsize int                                                                                                        
select @vsize = low                                                                                                       
from ".$ServerName."_SptValues                                                                                            
where type = 'E'                                                                                                          
    and number = 3                                                                                                        
                                                                                                                          
select Device   = name,                                                                                                 
       vdevno   = convert(tinyint, substring(convert(binary(4), low), @vsize, 1)),                                      
       defdsk   = substring('NY', (status & 1) + 1, 1),                                                                 
       Total    = convert(numeric(11,2), round((high - low + 1) / 512, 2)  ),                                          
       Used     = convert(numeric(11,2), round(isnull(sum(size), 0) / @NbPgParMeg, 2)),                                
       Free     = convert(numeric(11,2), abs(((high - low + 1)/512 - isnull( sum(size)/@NbPgParMeg, 0)) )),
       Location = phyname                                                                                               
from ".$ServerName."_SysUsages usg,                                                                                       
     ".$ServerName."_SysDevices dev                                                                                       
where  usg.vdevno = dev.vdevno                                                                                            
and    cntrltype = 0                                                                                                      
group by all name                                                                                                         
having cntrltype = 0
".$filter_clause."
order by ".$order_by;

  $query_name = "devspace";

?>






