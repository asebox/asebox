<?php
 
	$query = "set rowcount ".$rowcnt."
	select 
    Q.dbname     ,
    Q.uid    ,
    Q.id    ,
    Q.hashkey    ,
    usecount=sum(cnt) ,
    lio_avg = sum(1.*cnt*lio_avg )/sum(cnt)   ,
    totlio = sum(1.*cnt*lio_avg )   ,
    pio_avg = sum(1.*cnt*pio_avg )/sum(cnt)   ,
    totpio = sum(1.*cnt*pio_avg )   ,
    exec_avg=sum(1.*cnt*exec_avg)/sum(cnt)   ,
    elap_avg=sum(1.*cnt*elap_avg)/sum(cnt)   ,
    qtext	     
	from ".$ServerName."_QPMetrics Q, ".$ServerName."_QPMSQL S
	where Q.Timestamp >='".$StartTimestamp."'        
	and   Q.Timestamp <'".$EndTimestamp."' 
	and Q.dbname=S.dbname
	and Q.uid=S.uid
	and Q.id=S.id
	and Q.hashkey=S.hashkey
	and S.sequence=0
	and (Q.dbname    like '".$filterdbname   ."' or '".$filterdbname  ."'='')	
	and (Q.id        = convert(int,'".$filterid       ."') or '".$filterid      ."'='')	
	and (Q.hashkey   = convert(int,'".$filterhashkey  ."') or '".$filterhashkey ."'='')	
	and (qtext	   like '".$filterqtext	  ."' or '".$filterqtext	  ."'='')"	
	;
	

  $query = $query ." group by Q.dbname,Q.uid,Q.id,Q.hashkey,qtext";                   

	$query = $query ." order by ".$orderQPMetrics." 
	set rowcount 0";
	
  $query_name = "QPMetrics_statistics";

?>
