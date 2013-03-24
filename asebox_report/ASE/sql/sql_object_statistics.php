<?php

if ($support150)
   $sel150 = ", sumHkgcRequests=sum(HkgcRequests), maxHkgcPending=max(HkgcPending), sumHkgcOverflows=sum(HkgcOverflows)";
else
   $sel150 = "";

if ($support157)
   $sel157 = ", sumSharedLockWaitTime=sum(SharedLockWaitTime), sumExclusiveLockWaitTime=sum(ExclusiveLockWaitTime), sumUpdateLockWaitTime=sum(UpdateLockWaitTime), maxObjectCacheDate=max(ObjectCacheDate)";
else
   $sel157 = "";


if ($show_index_not_used==0)

$query = "set rowcount ".$rowcnt."
select dbname, objname, IndID".$indname_clause.", 
LReads=sum(convert(numeric(20,0),LogicalReads)), 
PReads=sum(convert(numeric(20,0),PhysicalReads)), 
AReads=sum(convert(numeric(20,0),APFReads)), 
PgReads=sum(convert(numeric(20,0),PagesRead)), 
CacheHitPct=str(100 * (sum(1.*LogicalReads)-(sum(1.*PagesRead) )) / sum(1.*LogicalReads),7,2) ,
PWrites=sum(PhysicalWrites), 
PgWrites=sum(PagesWritten), 
RowIns=sum(RowsInserted), 
RowDel=sum(RowsDeleted), 
RowUpd=sum(RowsUpdated),
Opers=sum(convert(numeric(20,0),Operations)), 
LockR=sum(convert(numeric(20,0),LockRequests)), 
LockW=sum(LockWaits),
UsedCnt= sum(UsedCount),
LReads_per_UsedCnt= str(case when sum(UsedCount)>0 then sum(1.*LogicalReads)/sum(UsedCount) else 0 end,15,2)
".$sel150."
".$sel157."
from ".$ServerName."_OpObjAct   
where Timestamp >='".$StartTimestamp."'        
and Timestamp <'".$EndTimestamp."'        
and (objname like '".$filterobjname."' or '".$filterobjname."'='')
and (dbname like '".$filterdbname."' or '".$filterdbname."'='')
and (IndID = convert(int,'".$filterindid."') or '".$filterindid."'='')
".$indname_filterclause."
".$sc_show_table_scans."
group by dbname, objname, IndID".$indname_clause."
order by ".$orderObj."
set rowcount 0";


else

$query = "set rowcount ".$rowcnt."
select dbname, objname, IndID".$indname_clause.", 
LReads=sum(convert(numeric(20,0),LogicalReads)), 
PReads=sum(convert(numeric(20,0),PhysicalReads)), 
AReads=sum(convert(numeric(20,0),APFReads)), 
PgReads=sum(convert(numeric(20,0),PagesRead)), 
CacheHitPct=str(100 * (sum(1.*LogicalReads)-(sum(1.*PhysicalReads))) / sum(1.*LogicalReads),7,2) ,
PWrites=sum(PhysicalWrites), 
PgWrites=sum(PagesWritten), 
RowIns=sum(RowsInserted), 
RowDel=sum(RowsDeleted), 
RowUpd=sum(RowsUpdated),
Opers=sum(convert(numeric(20,0),Operations)), 
LockR=sum(convert(numeric(20,0),LockRequests)), 
LockW=sum(LockWaits),
UsedCnt= sum(UsedCount),
LReads_per_UsedCnt= str(case when sum(UsedCount)>0 then sum(1.*LogicalReads)/sum(UsedCount) else 0 end,15,2)
".$sel150."
".$sel157."
from ".$ServerName."_OpObjAct,
    (select dbn=dbname, objn=objname, iid=IndID, ucount=sum(UsedCount)
     from ".$ServerName."_OpObjAct
     where Timestamp >='".$StartTimestamp."'        
       and Timestamp <'".$EndTimestamp."'
       and IndID between 1 and 254
       and (objname like '".$filterobjname."' or '".$filterobjname."'='')
       and (dbname like '".$filterdbname."' or '".$filterdbname."'='')
       and (IndID = convert(int,'".$filterindid."') or '".$filterindid."'='')
       ".$indname_filterclause."
     group by dbname, objname, IndID
     ) B
where Timestamp >='".$StartTimestamp."'        
and Timestamp <'".$EndTimestamp."'        
and IndID between 1 and 254
and dbname=B.dbn
and objname=B.objn
and IndID=B.iid
and B.ucount = 0
and (objname like '".$filterobjname."' or '".$filterobjname."'='')
and (dbname like '".$filterdbname."' or '".$filterdbname."'='')
and (IndID = convert(int,'".$filterindid."') or '".$filterindid."'='')
".$indname_filterclause."
group by dbname, objname, IndID".$indname_clause."
order by ".$orderObj."
set rowcount 0";





  $query_name = "object_statistics";

?>
