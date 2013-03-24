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
	$filter_clause=$filter_clause." and rtrim(program_name) is not null and UserName not like 'asemon%'";
}
$query = $query . "
declare @max_ts       datetime
declare @max_ts_block datetime
declare @dt           datetime
declare @rowcount     int
declare @line         varchar(160)
declare @version      varchar(3)

--select @max_ts = max(A.Timestamp) from ".$ServerName."_CnxActiv A
select @max_ts = '".$selectedTimestamp."'

select A.*,
       C.hostname,
       C.UserName,
       C.clientapplname,
       C.clienthostname,
       C.clientname,
       C.execlass,
       C.hostprocess,
       C.ipaddr,
       C.program_name,
       StmtID       = convert(numeric(14,0), null),
       blockingSpid = convert(int, null)
into #cnxA
from ".$ServerName."_CnxActiv A, ".$ServerName."_Cnx C
where  A.Timestamp = @max_ts
and A.Loggedindatetime = C.Loggedindatetime    
and A.Spid=C.Spid
and A.Kpid=C.Kpid
".$filter_clause." 
".$dbid_tmp_page_filter_clause."



select @dt = dateadd( mi, -5, getdate() )

select SPID, StmtID = max(StmtID)
into   #stmt
from ".$ServerName."_StmtStat stat
where  StartTime > @dt
group by SPID

update #cnxA
   set StmtID = stat.StmtID
from   #stmt stat
where  #cnxA.Spid = stat.SPID

------------------------------------------------------------------------------------------------------------------------
--blockages
select @max_ts_block = max(Timestamp) from ".$ServerName."_BlockedP
where  Timestamp > (select dateadd( mi, -1, @max_ts))

select *
into   #block
from ".$ServerName."_BlockedP
where  Timestamp = @max_ts_block
select @rowcount = @@rowcount

update #cnxA
   set blockingSpid = b.blockingSpid
from   #block b
where  #cnxA.Spid = b.blockedSpid

------------------------------------------------------------------------------------------------------------------------
--set rowcount 50
	select 
	  Loggedindt=convert(varchar,A.Loggedindatetime,109),
	  A.Spid,
	  UserName,
	  program_name, 
	  CPUTm=convert(numeric(16,0),case when CPUTime <1500000000  then CPUTime else 0 end), 
	  LReads=convert(numeric(16,0),LogicalReads), 
	  PReads=convert(numeric(16,0),PhysicalReads), 
	  PgRead=convert(numeric(16,0),PagesRead), 
	  PWrites=convert(numeric(16,0),PhysicalWrites), 
	  PgWritten=convert(numeric(16,0),PagesWritten), 
	  ScPgs=convert(numeric(16,0),ScanPgs), 
	  IPgs=convert(numeric(16,0),IdxPgs), 
	  TTbl=convert(numeric(16,0),TmpTbl),
	  UBytWrite=convert(numeric(20,0),UlcBytWrite), 
	  UFlush=convert(numeric(16,0),UlcFlush), 
	  UFlushFull=convert(numeric(16,0),ULCFlushFull),
	  avgUlcSize=0,
	  --avgUlcSize= case when convert(numeric(16,0),UlcFlush))=0 then str(0,5,0) else str(convert(float,convert(numeric(30,0),UlcBytWrite))) / convert(numeric(16,0),UlcFlush),5,0) end,
	  Trans=Transactions,
	  Cmits=Commits,
	  Rlbacks=Rollbacks, 
	  PktReceived=PacketsReceived,
	  PktSent=PacketsSent,
	  BReceived=convert(numeric(16,0),BytesReceived),
	  BSent=convert(numeric(16,0),BytesSent),
	  avgPktRcv=0,
	  avgPktSent=0,
	  --avgPktRcv=case when PacketsReceived=0 then str(0,5,0) else str(convert(float,convert(numeric(16,0),BytesReceived)))/PacketsReceived),5,0)) end,
	  --avgPktSent=case when PacketsSent=0 then str(0,5,0) else str(convert(float,convert(numeric(16,0),BytesSent)))/PacketsSent),5,0) end,
	  MaxMemusageKB=memusage*2,
	  MaxLocksHeld=LocksHeld,
	  ipaddr,
	  hostname,
	  hostprocess,
      execlass,
	  clientname,
	  clienthostname,
	  clientapplname,
	  proc_name,
	  linenum
	  --".
	  $dbid_tmp_pages_clause."
	from #cnxA A
	order by ".$orderPrc."
	drop table #cnxA
	drop table #stmt
	drop table #block
	";

/*  ".$dbid_tmp_page_filter_clause."
	group by A.Loggedindatetime, A.Spid, UserName, program_name, ipaddr, hostname, hostprocess, clientname, clienthostname, clientapplname, execlass
	order by A.Loggedindatetime, A.Spid, UserName, program_name, ipaddr, hostname, hostprocess, clientname, clienthostname, clientapplname, execlass ";
*/	
	/* .$orderPrc;   */



        if ($ArchSrvType=="Adaptive Server Enterprise") {
        	$query = $query . "set forceplan off ";
        }

  $query_name = "process_statistics";

?>
