<?php
$query = "select CacheName,
sumSearch=sum(convert(numeric(16,0),CacheSearches)),
sumLReads=sum(convert(numeric(16,0),LogicalReads)), 
sumReads=sum(convert(numeric(16,0),PhysicalReads)), 
sumWrites=sum(convert(numeric(16,0),PhysicalWrites)), 
sumStalls=sum(convert(numeric(16,0),Stalls)), 
Hit_pct= str(100*convert(float, sum(convert(numeric(16,0),CacheSearches)) - sum(convert(numeric(16,0),PhysicalReads) )) / sum(convert(numeric(16,0),CacheSearches)),5,2)
from ".$ServerName."_DataCache          
where Timestamp >='".$StartTimestamp."'        
and Timestamp <'".$EndTimestamp."'        
group by CacheName
having sum(convert(numeric(16,0),CacheSearches))>0";

  $query_name = "cache_statistics";

?>
