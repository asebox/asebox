<?php

        $query="";
        
        $filter_clause ="
    and (event   =convert(int,'".$filterevent.   "') or '".$filterevent."'='')
    and (eventmod=convert(int,'".$filtereventmod."') or '".$filtereventmod."'='')
    and (spid    =convert(int,'".$filterspid.    "') or '".$filterspid."'='')
	and (loginname like '".$filterloginname."'      or '".$filteuologinname."'     = '')
	and (dbname    like '".$filterdbname   ."'      or '".$filterddbname   ."'     = '')
	and (objname   like '".$filterobjname  ."'      or '".$filteroobjname  ."'     = '')
	and (objowner  like '".$filterobjowner ."'      or '".$filteuoobjowner ."'     = '')
	and (extrainfo like '".$filterextrainfo."'      or '".$filterextrainfo."'     = '')";

        $query = $query . "
	select 
       event
      ,eventmod
      ,eventname = audit_event_name(event)
      ,eventtime  = convert(varchar(10), eventtime, 103) + ' ' + convert(varchar(8), eventtime, 8)  
      ,spid
      ,sequence 
      --,suid     
      --,dbid     
      --,objid    
      --,xactid   
      ,loginname
      ,dbname   
      ,objname  
      ,objowner 
      ,extrainfo
      ,nodeid   
	from ".$ServerName."_Audit
	where eventtime >='".$StartTimestamp."'        
	and eventtime <'".$EndTimestamp."'        
	and event not in (37)
	and loginname not in ('dbmon')
	".$filter_clause."
	order by ".$orderPrc;

  $query_name = "audit_statistics";

?>
