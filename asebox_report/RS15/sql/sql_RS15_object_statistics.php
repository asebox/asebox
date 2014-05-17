<?php
$query = "set rowcount ".$rowcnt."
select 
  S.ID,
  instance_id,
  'dbid'= (instance_id /100) ,
  'ObjName'=str_replace (instance , 'AOBJ, ' ,'' ) ,
  total_commands=sum(counter_obs) ,
  'inserts'= sum( case when counter_id in (65000, 65005) then counter_obs else 0 end)  ,
  'updates'= sum( case when counter_id in (65001, 65007) then counter_obs else 0 end)  ,
  'deletes'= sum( case when counter_id in (65002, 65009) then counter_obs else 0 end) ,
  'writetexts'= sum( case when counter_id in (65003, 65011) then counter_obs else 0 end), 
  'execs'= sum( case when counter_id in (65004, 65013) then counter_obs else 0 end) ,
  'AOBJEstRowSize'= sum( case when counter_id =65015 then counter_obs else 0 end) ,
  'AOBJEstLOBSize'= sum( case when counter_id =65017 then counter_obs else 0 end) 

from ".$ServerName."_RSStats S, ".$ServerName."_Instances I
where S.Timestamp >='".$StartTimestamp."'        
and S.Timestamp <'".$EndTimestamp."'        
and (instance like '".$filterobjname."' or '".$filterobjname."'='')
and I.instance like 'AOBJ%'
and S.ID=I.ID
group by S.ID, instance_id, instance
order by ".$orderObj."
set rowcount 0";

$query_name = "RS15_object_statistics";

?>
