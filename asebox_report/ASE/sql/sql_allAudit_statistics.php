<?php
	$query = "set rowcount ".$rowcnt."
    select 
    event, 
    event_desc = case
        when event=1 and extrainfo like 'LOGIN%' then  'LOGIN'
        when event=1 and extrainfo not like 'LOGIN%' then  'ADHOC'
         		when event=45 then 'LOGIN'
         		when event=46 then 'LOGOUT'
         		when event=92 then 'SQL'
         		else convert( char(5), event)
         	end,
    evtime = 	convert( varchar, eventtime, 109) , 
    logout= case when event=1 then convert( varchar, (select min(eventtime) from ".$ServerName."_audit_table O where O.event =46 and O.spid=I.spid and O.eventtime >= I.eventtime) , 109) else null end,
    loginname,
    spid,
    sequence,
    extrainfo =
     	case
     		when event=1 then convert( char(100), extrainfo) 
     		when event=45 then ''
     		when event=46 then ''
     		when event=92 then isnull ( convert( varchar(255), extrainfo) , '' )
     		else  
     		convert( char(100), extrainfo) 
     	end
    from ".$ServerName."_audit_table I
    where eventtime between '".$StartTimestamp."' and '".$EndTimestamp."'";

	if (isset($filterevent) && $filterevent <> '')
	{
		$query = $query . " and event = ".$filterevent; 
	}
  if (isset($filterspid) && $filterspid <> '')
	{
		$query = $query . " and spid = ".$filterspid; 
	}
	if (isset($filterloginname) && $filterloginname <> '')
	{
		$query = $query . " and loginname like '".$filterloginname."' "; 
	}
	if (isset($filtersequence) && $filtersequence <> '')
	{
		$query = $query . " and sequence = ".$filtersequence; 
	}

	$query = $query ." order by ".$orderallStats." 
	  set rowcount 0";

  $query_name = "allAudit_statistics";
	
//
?>
