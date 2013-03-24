<?php

if ($support157==1) {
  $sel157cols     = ",ExecutionCount,CPUTime,ExecutionTime,PhysicalReads,LogicalReads,PhysicalWrites,PagesWritten,TempdbRemapCnt,AvgTempdbRemapTime,RequestCnt ";
  $sel157cols_agg = ",sumExecutionCount=sum(ExecutionCount),
  avgCPUTime       = case when sum(ExecutionCount) = 0 then 0 else sum(1.*CPUTime)/sum(ExecutionCount)        end,
  avgExecutionTime = case when sum(ExecutionCount) = 0 then 0 else sum(1.*ExecutionTime)/sum(ExecutionCount)  end,
  avgPhysicalReads = case when sum(ExecutionCount) = 0 then 0 else sum(1.*PhysicalReads)/sum(ExecutionCount)  end,
  avgLogicalReads  = case when sum(ExecutionCount) = 0 then 0 else sum(1.*LogicalReads)/sum(ExecutionCount)   end,
  avgPhysicalWrites= case when sum(ExecutionCount) = 0 then 0 else sum(1.*PhysicalWrites)/sum(ExecutionCount) end,
  avgPagesWritten  = case when sum(ExecutionCount) = 0 then 0 else sum(1.*PagesWritten)/sum(ExecutionCount)   end,
  sumTempdbRemapCnt=sum(TempdbRemapCnt),
  avgTempdbRemapTime=avg(AvgTempdbRemapTime),
  sumRequestCnt=sum(RequestCnt) ";
}
else {
  $sel157cols     = " ";
  $sel157cols_agg = " ";
}




if ($group=='N') {
    if (
      ($orderCachedProc=="NumPlans DESC")||
      ($orderCachedProc=="MaxMemUsageKB DESC")||
      ($orderCachedProc=="LastCompileDate DESC")
     )
        $orderCachedProc = "CompileDate DESC";

   
$query = "set rowcount ".$rowcnt." select
  Timestamp,
  ObjectID   ,
  OwnerUID   ,
  DBID       ,
  PlanID     ,
  MemUsageKB ,
  CDate=convert(varchar,CompileDate,109),
  ObjectName ,
  ObjectType ,
  OwnerName  ,
  DBName     
".$sel157cols."
from ".$ServerName."_CachedPrc
where Timestamp >='".$StartTimestamp."'        
and Timestamp <'".$EndTimestamp."'        

and (ObjectID = convert(int,'".$filterObjectID."')  or '".$filterObjectID."'='')
and (OwnerUID = convert(int,'".$filterOwnerUID."')  or '".$filterOwnerUID."'='')
and (DBID = convert(int,'".$filterDBID."')  or '".$filterDBID."'='')
and (PlanID = convert(int,'".$filterPlanID."')  or '".$filterPlanID."'='')
and ( ObjectName       like '".$filterObjectName."'   or '".$filterObjectName."'='')      
and ( ObjectType       like '".$filterObjectType."'   or '".$filterObjectType."'='')      
and ( OwnerName       like '".$filterOwnerName."'   or '".$filterOwnerName."'='')      
and ( DBName       like '".$filterDBName."'   or '".$filterDBName."'='')      

order by ".$orderCachedProc." set rowcount 0";
}
else {
    if (
      ($orderCachedProc=="PlanID")||
      ($orderCachedProc=="MemUsageKB DESC")||
      ($orderCachedProc=="CompileDate DESC")
     )
        $orderCachedProc = "NumPlans DESC";
     if ($orderCachedProc=="Timestamp")
        $orderCachedProc = "LastCompileDate DESC";

$query = "set rowcount ".$rowcnt." select
  1, /* fake column to have same column numbers as with th non group by select */
  ObjectID   ,
  OwnerUID   ,
  DBID       ,
  NumPlans     =count(distinct PlanID),
  MaxMemUsageKB = max(MemUsageKB),
  LastCompileDate = convert(varchar,max(CompileDate),109),
  ObjectName ,
  ObjectType ,
  OwnerName  ,
  DBName     
".$sel157cols_agg."
from ".$ServerName."_CachedPrc
where Timestamp >='".$StartTimestamp."'        
and Timestamp <'".$EndTimestamp."'        

and (ObjectID = convert(int,'".$filterObjectID."')  or '".$filterObjectID."'='')
and (OwnerUID = convert(int,'".$filterOwnerUID."')  or '".$filterOwnerUID."'='')
and (DBID = convert(int,'".$filterDBID."')  or '".$filterDBID."'='')
and ( ObjectName       like '".$filterObjectName."'   or '".$filterObjectName."'='')      
and ( ObjectType       like '".$filterObjectType."'   or '".$filterObjectType."'='')      
and ( OwnerName       like '".$filterOwnerName."'   or '".$filterOwnerName."'='')      
and ( DBName       like '".$filterDBName."'   or '".$filterDBName."'='')      

group by ObjectID   ,
  OwnerUID   ,
  DBID       ,
  ObjectName ,
  ObjectType ,
  OwnerName  ,
  DBName     

order by ".$orderCachedProc." set rowcount 0";
}


$query_name = "CachedProc_statistics";

?>
