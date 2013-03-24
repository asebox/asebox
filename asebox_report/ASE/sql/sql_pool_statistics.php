<?php
if (!isset($CacheName)) $CacheName="";

$sel157cols="";
if ($support157==1) {
 $sel157cols = ",sumLogicalReads=sum(1.*LogicalReads), sumPhysicalWrites=sum(1.*PhysicalWrites), sumAPFReads=sum(1.*APFReads), avgAPFPercentage=avg(APFPercentage), avgWashSize=avg(WashSize)";
}

$query = "select
CacheName,
IOBufferSize ,
AllocatedKB ,
sumPagesReads=sum(convert(numeric(15,0),PagesRead)),
sumPhysicalReads=sum(convert(numeric(15,0),PhysicalReads)),
sumStalls=sum(convert(numeric(15,0),Stalls)),
--         sumPagesTouched=sum(PagesTouched),
sumBuffersToMRU=sum(convert(numeric(15,0),BuffersToMRU)),
sumBuffersToLRU=sum(convert(numeric(15,0),BuffersToLRU)),
PhysReads_s=case when datediff(ss,min(Timestamp), max(Timestamp))=0 then '' else
  str(sum(convert(numeric(15,0),PhysicalReads))/datediff(ss,min(Timestamp), max(Timestamp)),15,2)
  end,
Turnover_s=case when sum(convert(numeric(15,0),BuffersToMRU)) = 0 then 'infinite' 
                when datediff(ss,min(Timestamp), max(Timestamp))=0 then '' 
                else
                         str((AllocatedKB/2) /(sum(convert(numeric(15,0),BuffersToMRU))/datediff(ss,min(Timestamp), max(Timestamp)) ), 20,2)
              end
".$sel157cols."
from ".$ServerName."_CachePool
where Timestamp >='".$StartTimestamp."'
and Timestamp <'".$EndTimestamp."'
and (CacheName='".$CacheName."' or ''='".$CacheName."')
group by CacheName, IOBufferSize, AllocatedKB
order by CacheName, IOBufferSize";

  $query_name = "pool_statistics";

?>
