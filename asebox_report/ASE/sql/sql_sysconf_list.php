<?php

$result = sybase_query("if object_id('#sysconf') is not null drop table #sysconf",$pid);
$result = sybase_query("if object_id('#max_sysconf') is not null drop table #max_sysconf",$pid);

$query="";
if ($ArchSrvType=="Adaptive Server Enterprise") {
        $query = "set forceplan on ";
}

$filter_clause ="
and (a.comment like '%".$filtername."%'   or '".$filtername."'   = '')
and (b.comment  like '%".$filterparent."%' or '".$filterparent."' = '')";
  
 
$query = "";
$query = $query . "
set rowcount 299

declare @dt datetime
select @dt = max(Timestamp) from ".$ServerName."_SysConf

select a.comment, parent=b.comment,
       valuetext = case when a.value2 is not null then convert(varchar(30),a.value2) else convert(varchar(30), a.value) end, 
       Timestamp=@dt, last_change = convert(datetime, null), last_value=convert(varchar(30), '')
into   #sysconf
from   ".$ServerName."_SysConf a, ".$ServerName."_SysConf b
where  a.Timestamp = @dt
and    b.Timestamp = @dt
and    a.parent = b.config
and a.parent <> 0
and a.parent <> 1 -- Parent
and a.parent <> 10
and a.parent <> 19 -- Cache Manager
".$filter_clause."

select comment,
       valuetext = case when value2 is not null then convert(varchar(30),value2) else convert(varchar(30), value) end,
       maxts=max(Timestamp)
into   #max_sysconf
from   ".$ServerName."_SysConf
where  parent not in (0,1,10,19)
group by comment, case when value2 is not null then convert(varchar(30),value2) else convert(varchar(30), value) end

update #sysconf 
   set last_value  = m.valuetext, 
       last_change = m.maxts 
from   #max_sysconf m 
where  #sysconf.comment = m.comment 
and    m.maxts!=@dt 

select 
       Timestamp = convert(varchar(16), convert(varchar(10), Timestamp, 103) + ' ' + convert(varchar(5), Timestamp, 8)), 
       Configname  = convert(varchar(30), comment), 
       Value=valuetext,
       Parent      = convert(varchar(30), parent), 
       last_change = convert(varchar(16), convert(varchar(10), last_change, 103) + ' ' + convert(varchar(5), last_change, 8)), 
       last_value 
from   #sysconf  a
order by ".$orderPrc;





if ($ArchSrvType=="Adaptive Server Enterprise") {
        $query = $query . "
  set forceplan off";
}

$query_name = "sysconf_list";

?>
