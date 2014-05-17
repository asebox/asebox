<?php
  if (isset($ObjectName) && $ObjectName <> '' ) {
  	$fromStmtObjClause = " left outer join ".$ServerName."_StmtObj obj on stat.StmtID=obj.StmtID";
  	$whereStmtObjClause = " and ObjectName like '".$ObjectName."'";
  	if (isset($IndexID) && $IndexID<>"")  $whereStmtObjClause = $whereStmtObjClause." and IndexID=".$IndexID;
  	if (isset($ObjectDbName) && $ObjectDbName<>"")  $whereStmtObjClause = $whereStmtObjClause." and DBName='".$ObjectDbName."'";
  }
  else {
  	$fromStmtObjClause="";
  	$whereStmtObjClause="";
  }
  
  if ($support157 ==1) {
    $selClientInfo = ", ClientName, ClientHostName, ClientApplName";
    $filter_clause127 = "
	and (ClientName       like '".$filterClientName."'        or '".$filterClientName."'       = '')
	and (ClientHostName       like '".$filterClientHostName."'        or '".$filterClientHostName."'       = '')
	and (ClientApplName       like '".$filterClientApplName."'        or '".$filterClientApplName."'       = '')
    ";

  }
  else {
    $selClientInfo = "";
    $filter_clause127 = "";
  }


	$query = "set rowcount ".$rowcnt."
	select distinct
	    stat.StmtID,
        Debut=convert(varchar,StartTime,109),
        Elapsed_s=datediff(ss,StartTime,EndTime),
        stat.SPID,
        stat.DBID,
	      stat.Application,
	      stat.ClientHost,
        stat.ProcName,
        stat.LineNumber,
        stat.CpuTime,
        stat.WaitTime,
        stat.MemUsageKB,
        stat.PhysicalReads,
        stat.LogicalReads,
        stat.PagesModified,
        stat.PacketsSent,
        stat.PacketsReceived,
        stat.BatchID,
        stat.ContextID,
        stat.PlanID,
        planOK=case when pln.StmtID is not null then 'OK' else '' end
        ".$selClientInfo."
	from (".$ServerName."_StmtStat stat  left outer join ".$ServerName."_StmtPlan pln on stat.StmtID=pln.StmtID and Sequence=1)
	".$fromStmtObjClause."
	where StartTime >='".$StartTimestamp."'        
	and StartTime <'".$EndTimestamp."' ".$whereStmtObjClause."

    and (stat.StmtID=convert(int,'".$filterStmtID."') or '".$filterStmtID."'='')
    and (stat.SPID=convert(int,'".$filterSPID."') or '".$filterSPID."'='')
    and (stat.DBID=convert(int,'".$filterDBID."') or '".$filterDBID."'='')

	and (stat.Application       like '".$filterApplication."'        or '".$filterApplication."'       = '')
	and (stat.ClientHost       like '".$filterClientHost."'        or '".$filterClientHost."'       = '')
	and (stat.ProcName       like '".$filterProcName."'        or '".$filterProcName."'       = '')

    and (stat.LineNumber=convert(int,'".$filterLineNumber."') or '".$filterLineNumber."'='')
    and (stat.PlanID=convert(int,'".$filterPlanID."') or '".$filterPlanID."'='')
    and (stat.BatchID=convert(int,'".$filterBatchID."') or '".$filterBatchID."'='')
    and (stat.ContextID=convert(int,'".$filterContextID."') or '".$filterContextID."'='')

    ";


	
	if (isset($ExclApp) && $ExclApp <> '')
	{
		$query = $query . "and Application not like '".$ExclApp."' "; 
	}
	if (isset($StmtID) && $StmtID<> '')
	{
		$query = $query . "and stat.StmtID = ".$StmtID; 
	}
	if (isset($LineNumber) && $LineNumber<> '')
	{
		$query = $query . "and LineNumber = ".$LineNumber; 
	}
	if (isset($DBID) && $DBID<> '')
	{
		$query = $query . "and DBID = ".$DBID; 
	}
	if (isset($ClientHost) && $ClientHost<> '')
	{
                $query = $query . " and ClientHost like '".$ClientHost."' "; 
        }
	if (isset($ExclHost) && $ExclHost<> '')
	{
                $query = $query . " and ClientHost not like '".$ExclHost."' "; 
        }
    
    
    $query = $query . $filter_clause127;
    
	$query = $query ." order by ".$orderStmt." 
	set rowcount 0";

    $query_name = "statement_statistics";
	
?>
