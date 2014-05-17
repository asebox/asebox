<?php
	$query = "set rowcount ".$rowcnt."
    select
    	LOGIN=convert( varchar, I.eventtime, 109), 
    	LOGOUT=convert( varchar, O.eventtime, 109),
      CnxTime_ms=datediff(ms,  I.eventtime, O.eventtime),
    	I.loginname , 
      I.spid, 
    	IP=dbo.getIPaddress( I.extrainfo ), 
    	machine=dbo.getMachine( I.extrainfo ),
    	application=dbo.getApplication( I.extrainfo )
    from ".$ServerName."_audit_table I, ".$ServerName."_audit_table O
    where I.event in (1)
    and I.eventtime between '".$StartTimestamp."' and '".$EndTimestamp."'
    and O.eventtime = (select min(eventtime) from ".$ServerName."_audit_table O2 where O2.event =46 and O2.spid=O.spid and O2.eventtime >= I.eventtime)
    and O.event=46
    and I.spid=O.spid";
  if (isset($filterspid) && $filterspid <> '')
	{
		$query = $query . " and I.spid = ".$filterspid; 
	}
	if (isset($filterloginname) && $filterloginname <> '')
	{
		$query = $query . " and I.loginname like '".$filterloginname."' "; 
	}
	if (isset($filterip) && $filterip <> '')
	{
		$query = $query . " and dbo.getIPaddress( I.extrainfo ) like '".$filterip."' "; 
	}
	if (isset($filtermachine) && $filtermachine <> '')
	{
		$query = $query . " and dbo.getMachine( I.extrainfo ) like '".$filtermachine."' "; 
	}
	if (isset($filterappli) && $filterappli <> '')
	{
		$query = $query . " and dbo.getApplication( I.extrainfo ) like '".$filterappli."' "; 
	}

  //$query = $query . " order by I.id";
	$query = $query ." order by ".$ordercnxStats." 
	  set rowcount 0";

  $query_name = "cnxAudit_statistics";
	
//
?>
