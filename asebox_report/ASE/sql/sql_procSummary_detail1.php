<?php
$result = sybase_query("if object_id('#sumplanperproc') is not null drop table #sumplanperproc",$pid);


$query = 
"select DBID, ProcName,Application, nbplans=count(distinct PlanID)
into #sumplanperproc
from ".$ServerName."_StmtStat S
where (S.StartTime >= '".$StartTimestamp."'        
  and S.StartTime <= '".$EndTimestamp."')
  and ProcName is not null
  and (DBID=convert(int,'".$filterdbid."') or '".$filterdbid."'='')
  and (ProcName like '".$filterProcname."' or '".$filterProcname."' = '')
  and (Application like '".$filterAppname."' or '".$filterAppname."' = '')
group by DBID, ProcName, Application
create clustered index ic on #sumplanperproc(DBID, ProcName, Application)

set rowcount 0".$rowcnt."

select DBID, Application,ProcName,
    Executions=count(*),
    CpuTime=sum(CpuTime),
    WaitTime=sum(WaitTime),
    MemUsageKB=max(MemUsageKB),
    PhysicalReads=sum(PhysicalReads),
    LogicalReads=sum(LogicalReads),
    PagesModified=sum(PagesModified),
    PacketsSent=sum(PacketsSent),
    PacketsReceived=sum(PacketsReceived),
    SumPlans=(select nbplans from #sumplanperproc S
              where ProcExec.DBID=S.DBID              
                and isnull(ProcExec.Application,'')=isnull(S.Application,'')
                and ProcExec.ProcName=S.ProcName
             )
from (
    select Application,ProcName, DBID, SPID, KPID, BatchID, ContextID , 
           CpuTime=sum(1.*CpuTime),
           WaitTime=sum(1.*WaitTime), 
           MemUsageKB=max(MemUsageKB), 
           PhysicalReads=sum(1.*PhysicalReads),
           LogicalReads=sum(1.*LogicalReads), 
           PagesModified=sum(1.*PagesModified),
           PacketsSent=sum(1.*PacketsSent), 
           PacketsReceived=sum(1.*PacketsReceived)
    from ".$ServerName."_StmtStat
    where (StartTime >= '".$StartTimestamp."'        
      and StartTime <= '".$EndTimestamp."')
      and ProcName is not null
      and (DBID=convert(int,'".$filterdbid."') or '".$filterdbid."'='')
      and (ProcName like '".$filterProcname."' or '".$filterProcname."' = '')
      and (Application like '".$filterAppname."' or '".$filterAppname."' = '')
    group by Application,ProcName, DBID, SPID, KPID,BatchID, ContextID 
    ) ProcExec
group by DBID, Application,ProcName
order by ".$orderProc." 
set rowcount 0";

$query_name = "procMDA_statistics";
?>
