<?php
if ( isset($_POST['showsys'     ]) ) $showsys=$_POST['showsys'];      else $showsys="NO";

$query = "select cnt=count(*)
          from ".$ServerName."_Engines   
          where Timestamp >='".$StartTimestamp."' 
          and Timestamp <'".$EndTimestamp."'";


$result = sybase_query($query,$pid);
$row = sybase_fetch_array($result);



$cnt = $row["cnt"];

if ( $cnt == 0 ) {
        ?>
        <p align="center"><font size="4"  STYLE="font-weight: 900" COLOR="red">Error or no data available for this period</font></p>
        <?php   
   }
else {	
?>

<?php
//====================================================================================================
// GET DATA 
$result = sybase_query("if object_id('#linedet')   is not null drop table #linedet",  $pid);
$result = sybase_query("if object_id('#linedet2')  is not null drop table #linedet2", $pid);
$result = sybase_query("if object_id('#procexecs') is not null drop table #procexecs",$pid);
$result = sybase_query("if object_id('#procsumm')  is not null drop table #procsumm", $pid);
 
$query = "
select distinct  
    StmtID = convert(integer, stat.StmtID), 
    StartTime,   
    --Debut      =convert(varchar,StartTime,109), 
    Elapsed_s  =datediff(ms,StartTime,EndTime)/1000.0,   
    stat.SPID,   
    stat.DBID,   
    App        = convert( varchar(5), stat.Application ), 
    ClientHost = convert( varchar(8), stat.ClientHost ), 
    ProcName   = case when stat.ProcName is null then stat.ClientHost else stat.ProcName end,  
    Line       = stat.LineNumber,  
    CpuTime    = stat.CpuTime,  
    WaitTime   = stat.WaitTime, 
    MemKB      = stat.MemUsageKB,  
    PReads     = stat.PhysicalReads,   
    LReads     = stat.LogicalReads, 
    PagesMod   = stat.PagesModified,   
    PktsSent   = stat.PacketsSent, 
    PktsRec    = stat.PacketsReceived, 
    BatchID    = stat.BatchID,  
    ContextID  = stat.ContextID,   
    PlanID     = stat.PlanID,   
    planOK     =case when pln.StmtID is not null then 'OK' else '' end  
into #linedet 
from  (".$ServerName."_StmtStat stat  left outer join ".$ServerName."_StmtPlan pln on stat.StmtID=pln.StmtID and Sequence=1) 
where StartTime >='".$StartTimestamp."' 
  and StartTime <'".$EndTimestamp."'
    and isnull(Application,'') <> 'asemon_logger' and isnull(ProcName,'') not like 'sp_asemon_%' and isnull(ProcName,'') <> 'sp_dba_upstats_thread'
order by datediff(ss,StartTime,EndTime) desc";
 
$result = sybase_query($query,$pid);

$query = "
select ProcName   
      ,Line 
      ,'Elapsed_s' = convert(int, sum( Elapsed_s ))   
      ,'ExecCount'   = count(*)  
      ,CpuTime   = convert(int, sum( CpuTime   /1000) )  
      ,WaitTime  = sum( WaitTime  /1000)  
      ,MemKB     = sum( MemKB     /1000)  
      ,PReads    = sum( PReads    /1000)  
      ,LReads    = sum( LReads    /1000)  
      ,PagesMod  = sum( PagesMod  /1000)  
      ,PktsSent  = sum( PktsSent  /1000)  
      ,PktsRec   = sum( PktsRec   /1000)  
into  #linedet2   
from  #linedet 
group by ProcName, Line 
--having sum( Elapsed_s ) >60";

$result = sybase_query($query,$pid);

$query = "   
----------------------------------------------------------------------------------------------------  
--PROC EXECS   
select ProcName, cnt=count(*) 
into   #procexecs 
from (   
    select ProcName, DBID, SPID, KPID, BatchID, PlanID, ContextID 
    from ".$ServerName."_StmtStat   
    where (StartTime >= '$DEB'   
      and  StartTime <= '$FIN')  
      and  ProcName is not null  
      and  Application <> 'asemon_logger' 
      and  Application not like 'sp_sysmon%' 
      and  LogicalReads < 2147000000   
      and  ProcName not like '*%'   
      and   ('$PROC' = '' or ProcName = '$PROC')   
      group by ProcName, DBID, SPID, KPID,BatchID, PlanID, ContextID 
    ) ProcExec 
group by ProcName"; 

$result = sybase_query($query,$pid);

$query = "   
--set rowcount 50 
--print 'SUMMARY BY PROCEDURE'  
select ProcName   
      --,Line  
      ,'Elapsed_s' = convert(int, sum( Elapsed_s ))   
      ,'Elapsed_m' = convert(numeric(10,2), sum( Elapsed_s )/60.) 
      --,'ExecCount'   = count(*)   
      ,ExecCount = convert(int, 0)  
      ,CpuTime   = sum( CpuTime   ) 
      ,WaitTime  = sum( WaitTime  ) 
      --,MemKB     = sum( MemKB     )  
      ,PReads    = sum( PReads    ) 
      ,LReads    = sum( LReads    ) 
      ,PagesMod  = sum( PagesMod  ) 
      ,PktsSent  = sum( PktsSent  ) 
      ,PktsRec   = sum( PktsRec   ) 
into  #procsumm   
from  #linedet2   
--having sum( Elapsed_sec ) >1   
group by ProcName 
   
update #procsumm  
   set ExecCount = cnt  
from   #procexecs 
where  #procsumm.ProcName = #procexecs.ProcName"; 

$result = sybase_query($query,$pid);

$query = "   
set rowcount 20   
select * 
from   #procsumm  
order by 1 -- todo xxx
   
select 'TOTAL' = convert(varchar(30), 'TOTAL $SRV')   
      ,'Elapsed_min' = sum( Elapsed_s )/60   
      ,CpuTime   = sum( CpuTime   ) 
      ,WaitTime  = sum( WaitTime  ) 
    --,MemKB     = sum( MemKB     ) 
      ,PReads    = sum( PReads    ) 
      ,LReads    = sum( LReads    ) 
from  #procsumm"; 

$result = sybase_query($query,$pid);

$query = "   
set rowcount 20   
--print '' 
--print 'SUMMARY BY LINE' 
select * from #linedet2 
order by Elapsed_s desc 
   
select   'ELAPSED                         '  
        ,'          '   
        ,'Elapsed(min)' = convert(int, sum( Elapsed_s )/60) 
        ,'Elapsed(hrs)' = convert( numeric(6,2), sum( Elapsed_s )/3600. )  
        --,CpuTime   = convert( numeric(12,2), sum( CpuTime/1000.   )  )   
        --,WaitTime  = convert( numeric(12,2), sum( WaitTime /1000. )  )   
      --,MemKB     = convert( numeric(12,2), sum( MemKB /1000.    )  )  
        --,PReads    = convert( numeric(12,2), sum( PReads /1000.   )  )   
        --,LReads    = convert( numeric(12,2), sum( LReads /1000.   )  )   
      --,PagesMod  = sum( PagesMod  )  
      --,PktsSent  = sum( PktsSent  )  
      --,PktsRec   = sum( PktsRec   )  
from  #linedet"; 
   
   
$query = "   
--print '' 
--print 'DETAIL BY LINE'  
set rowcount 20   
   
select   
    StmtID = convert( char(8), stat.StmtID)  
   ,StartTime  = convert(varchar(10), StartTime, 103) + ' ' + convert(varchar(5), StartTime, 8) 
    --Debut      =convert(varchar,StartTime,109),  
   --,'Elapsed(sec)'  = convert( numeric(8,1), Elapsed_s)   
   ,'Elapsed(min)'  = convert( numeric(8,1), Elapsed_s/60.)  -- = datediff(ss,StartTime,EndTime),  
   --,SPID     = convert( numeric(4,0), stat.SPID) 
   --,stat.DBID   
   --,App        -- = convert( varchar(5), stat.Application ), 
   --,ClientHost -- = convert( varchar(8), stat.ClientHost ),  
   ,ProcName   -- = stat.ProcName,  
   ,Line       -- = stat.LineNumber,   
   ,CpuTime    -- = stat.CpuTime,   
   ,WaitTime   -- = stat.WaitTime,  
   --,MemKB      -- = stat.MemUsageKB, 
   ,PReads     -- = stat.PhysicalReads,   
   ,LReads     -- = stat.LogicalReads, 
   --,PagesMod   -- = stat.PagesModified, 
   --,PktsSent   -- = stat.PacketsSent,   
   --,PktsRec    -- = stat.PacketsReceived,  
   --,BatchID    -- = stat.BatchID, 
   --,ContextID  -- = stat.ContextID,  
   ,PlanID     -- = stat.PlanID, 
   ,planOK     -- =case when pln.StmtID is not null then 'OK' else '' end  
from   #linedet stat 
where  1=1  
--and    datepart(caldayofweek, StartTime) < 6  
--and    (PReads + LReads/1000) > 10000   
--and Elapsed_s >30  
order by $ORDER   ";
?> 
 


 
    <?php
    //====================================================================================================
    // RUN QUERY INTO TEMP TABLES
    echo "<P>";
              $Title = "Procedure Summary";
              include ("procSummary_detail.php");
    echo "</P>";
    ?>

    <?php
    //====================================================================================================
    // PROC 1
    echo "<P>";
              $Title = "Procedure Summary";
              include ("procSummary_detail_1.php");
    echo "</P>";
    ?>

    <?php
    //====================================================================================================
    // PROC 2
    echo "<P>";
              $Title = "Procedure Summary";
              include ("procSummary_detail_1.php");
    echo "</P>";
    ?>

    <?php
    //====================================================================================================
    // PROC 3
    echo "<P>";
              $Title = "Procedure Summary";
              include ("procSummary_detail_2.php");
    echo "</P>";
    ?>


    <?php
      } // End if ASEMON_LOGGER data available
?>

