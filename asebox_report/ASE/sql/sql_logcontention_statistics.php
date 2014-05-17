<?php        
$query = "select DBName, sumAppendLog=sum(AppendLogRequests) , sumLogWaits=sum(AppendLogWaits ), 
waitPct= str(convert(float,sum(AppendLogWaits) )*100/sum(AppendLogRequests) ,5,2)
from ".$ServerName."_OpenDbs          
where Timestamp >='".$StartTimestamp."'        
and Timestamp <'".$EndTimestamp."'
group by DBName order by waitPct desc";

  $query_name = "logcontention_statistics";
?>
