<?php

        $query="";
        if ($ArchSrvType=="Adaptive Server Enterprise") {
        	$query = "set forceplan on ";   
        }

        if ($dbid_tmp_pages_exists == 1 ) {
        	$dbid_tmp_pages_clause=", Tempdb_id=max(tempdbid), Maxtmp_pages=max(A.tmp_pages)";
        	$dbid_tmp_page_filter_clause="and (tempdbid =convert(int,'".$filterTempdbid."') or '".$filterTempdbid."'='')";
        }
        else {
                $dbid_tmp_pages_clause="";
        	$dbid_tmp_page_filter_clause="";
        }

        $filter_clause ="
    and (A.Spid=convert(int,'".$filterspid."') or '".$filterspid."'='')
	and (UserName       like '".$filterusername."'        or '".$filterusername."'       = '')
	and (program_name   like '".$filterprogname."'        or '".$filterprogname."'       = '')
	and (ipaddr         like '".$filteripaddr."'          or '".$filteripaddr."'         = '')
	and (hostname       like '".$filterhostname."'        or '".$filterhostname."'       = '')
	and (hostprocess    like '".$filterhostprocess."'     or '".$filterhostprocess."'    = '')
	and (clientname     like '".$filterclientname."'      or '".$filterclientname."'     = '')
	and (clienthostname like '".$filterclienthostname."'  or '".$filterclienthostname."' = '')
	and (clientapplname like '".$filterclientapplname."'  or '".$filterclientapplname."' = '')";
	
if ($showsys!="yes") {
	$filter_clause=$filter_clause." and rtrim(program_name) is not null";
}
        $query = $query . "
        declare @max_ts       datetime
        select @max_ts = max(A.Timestamp) from ".$ServerName."_CnxActiv A
	select 
	  Loggedindt=convert(varchar,A.Loggedindatetime,109),
	  A.Spid,
	  UserName,
	  program_name, 
	  CPUTm=sum(convert(numeric(16,0),case when CPUTime <1500000000  then CPUTime else 0 end)), 
	  LReads=sum(convert(numeric(16,0),LogicalReads)), 
	  PReads=sum(convert(numeric(16,0),PhysicalReads)), 
	  PgRead=sum(convert(numeric(16,0),PagesRead)), 
	  PWrites=sum(convert(numeric(16,0),PhysicalWrites)), 
	  PgWritten=sum(convert(numeric(16,0),PagesWritten)), 
	  ScPgs=sum(convert(numeric(16,0),ScanPgs)), 
	  IPgs=sum(convert(numeric(16,0),IdxPgs)), 
	  TTbl=sum(convert(numeric(16,0),TmpTbl)),
	  UBytWrite=sum(convert(numeric(20,0),UlcBytWrite)), 
	  UFlush=sum(convert(numeric(16,0),UlcFlush)), 
	  UFlushFull=sum(convert(numeric(16,0),ULCFlushFull)),
	  avgUlcSize= case when sum(convert(numeric(16,0),UlcFlush))=0 then str(0,5,0) else str(convert(float,sum(convert(numeric(30,0),UlcBytWrite))) / sum(convert(numeric(16,0),UlcFlush)),5,0) end,
	  Trans=sum(Transactions),
	  Cmits=sum(Commits),
	  Rlbacks=sum(Rollbacks), 
	  PktReceived=sum(PacketsReceived),
	  PktSent=sum(PacketsSent),
	  BReceived=sum(convert(numeric(16,0),BytesReceived)),
	  BSent=sum(convert(numeric(16,0),BytesSent)),
	  avgPktRcv=case when sum(PacketsReceived)=0 then str(0,5,0) else str(convert(float,sum(convert(numeric(16,0),BytesReceived)))/sum(PacketsReceived),5,0) end,
	  avgPktSent=case when sum(PacketsSent)=0 then str(0,5,0) else str(convert(float,sum(convert(numeric(16,0),BytesSent)))/sum(PacketsSent),5,0) end,
	  MaxMemusageKB=max(memusage)*2,
	  MaxLocksHeld=max(LocksHeld),
	  ipaddr,
	  hostname,
	  hostprocess,
          execlass,
	  clientname,
	  clienthostname,
	  clientapplname".
	  $dbid_tmp_pages_clause."
	from ".$ServerName."_CnxActiv A, ".$ServerName."_Cnx C
	where A.Timestamp >='".$StartTimestamp."'        
	and A.Timestamp <'".$EndTimestamp."'        
    and A.Timestamp = @max_ts
	and A.Loggedindatetime = C.Loggedindatetime    
	--and rtrim(program_name) is not null
	and A.Spid=C.Spid
	and A.Kpid=C.Kpid
  ".$filter_clause."
	$dbid_tmp_page_filter_clause
	group by A.Loggedindatetime, A.Spid, UserName, program_name, ipaddr, hostname, hostprocess, clientname, clienthostname, clientapplname, execlass
	order by A.Loggedindatetime, A.Spid, UserName, program_name, ipaddr, hostname, hostprocess, clientname, clienthostname, clientapplname, execlass ";
	/* .$orderPrc;   */

        if ($ArchSrvType=="Adaptive Server Enterprise") {
        	$query = $query . "set forceplan off ";
        }

  $query_name = "process_statistics";

?>
