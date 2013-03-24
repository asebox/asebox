<?php

if ( ($filterIndid != "") && ($filterIndid[0]=='>'))
  $filterIndid_clause = " and (F.indid".$filterIndid.")";
else
	$filterIndid_clause = " and (F.indid=convert(int,'".$filterIndid."') or '".$filterIndid."'='')";

$query = "set rowcount ".$rowcnt."
select
    F.dbname           ,
    F.owner            ,
    F.tabname          ,
    F.indname          ,
    F.indid            ,
    F.lockmode         ,
    F.clu              ,
    avg_Rowcnt            = str(avg(Rowcnt           ),14,0),
    avg_pagecnt           = str(avg(convert(float,pagecnt   )       ),14,0),
    avg_leafcnt           = str(avg(convert(float,leafcnt   )       ),14,0),
    avg_emptypgcnt        = str(avg(convert(float,emptypgcnt)       ),14,0),
    avg_Forwardrowcnt     = str(avg(Forwardrowcnt    ),14,0),
    avg_Delrowcnt         = str(avg(Delrowcnt        ),14,0),
    avg_dpageCR           = str(avg(dpageCR          ),10,2),
    avg_ipageCR           = str(avg(ipageCR          ),10,2),
    avg_drowCR            = str(avg(drowCR           ),10,2),
    avg_space_utilization = str(avg(space_utilization),10,2),
    avg_largeIO_eff       = str(avg(largeIO_eff      ),10,2)
    ".$Dpage_utilization_selclause."
    , object_Mb  = convert(numeric(12,2),str(avg(2.*pagecnt+2.*leafcnt)/1024 ,12,2))
    , delta_Mb   = convert(numeric(12,2),str(avg(data_delta_Mb+leaf_delta_Mb),12,2))
from ".$ServerName."_Fragment F,


(
    select
        dbname,
        owner ,
        tabname,
        indid  ,
        data_delta_Mb=2.*(max(pagecnt) - min(pagecnt))/1024,
        leaf_delta_Mb=2.*(max(leafcnt) - min(leafcnt))/1024
    from ".$ServerName."_Fragment
    where Timestamp >='".$StartTimestamp."' and Timestamp <'".$EndTimestamp."'
    group by dbname, owner,tabname,indid
) delta_size


where Timestamp ='".$selectedTimestamp."'        
	and (F.dbname       like '".$filterDbName."' or '".$filterDbName."' = '')
	and (F.owner        like '".$filterOwner."' or '".$filterOwner."' = '')
	and (F.tabname       like '".$filterTabName."' or '".$filterTabName."' = '')
	and (F.indname       like '".$filterIndName."' or '".$filterIndName."' = '')
	and (F.lockmode       like '".$filterLckMode."' or '".$filterLckMode."' = '')
	and (F.clu       like '".$filterClu."' or '".$filterClu."' = '')
  ".$filterIndid_clause."
  and F.dbname=delta_size.dbname
  and F.owner=delta_size.owner
  and F.tabname=delta_size.tabname
  and F.indid=delta_size.indid
  
group by F.dbname, F.owner, F.tabname, F.indname, F.indid, F.lockmode, F.clu
having (   avg(pagecnt          ) > ".$pagenum."
	     or avg(leafcnt          ) > ".$pagenum.")
order by ".$orderFragment.
" set rowcount 0";

  $query_name = "fragment_statistics";
?>







