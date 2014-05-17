<?php

$result = sybase_query("if object_id('#sysconf') is not null drop table #sysconf",$pid);
$result = sybase_query("if object_id('#sysconf_1') is not null drop table #sysconf_1",$pid);
$result = sybase_query("if object_id('#sysconf_2') is not null drop table #sysconf_2",$pid);
//$result = sybase_query("if object_id('#max_sysconf') is not null drop table #max_sysconf",$pid);

$query="";
if ($ArchSrvType=="Adaptive Server Enterprise") {
        $query = "set forceplan on ";
}

$filter_clause ="
and (a.name like '%".$filtername."%'   or '".$filtername."'   = '')
and (a.name like '%".$filterparent."%'   or '".$filterparent."'   = '')";


//if ($show_diff=="2") {
	
	
if ($filtercomment!="") {
$filter_clause2 ="
where isnull(value_1, ' ') != isnull(value_2, ' ')";
} else {
$filter_clause2 =" ";
}


//and (a.parent  like '%".$filterparent."%' or '".$filterparent."' = '')";
  
 
$query = "
set rowcount 0

create table #sysconf
( name       varchar(60) null,
  value_1    varchar(30) null,
  value_2    varchar(30) null,
  parent     varchar(30) null,
  difference varchar(6)  null
)
create table #sysconf_1
( name      varchar(60),
  valuetext varchar(30) null,
  parent    varchar(30) null
)
create table #sysconf_2
( name      varchar(60),
  valuetext varchar(30) null,
  parent    varchar(30) null
)
set forceplan on
insert into #sysconf_1
select a.comment,
       valuetext = case when a.value2 is not null then convert(varchar(30),a.value2) else convert(varchar(30), a.value) end,
       parent=b.comment
from   ".$ServerName."_SysConf a, 
       ".$ServerName."_SysConf b , 
       (select maxTimestamp=max(SL.Timestamp) from ".$ServerName."_SysConf SL
         where Timestamp <= '".$EndTimestamp."') dt
where datediff(hh,a.Timestamp,dt.maxTimestamp) = 0
and   datediff(hh,b.Timestamp,dt.maxTimestamp) = 0
and   a.parent = b.config
and   a.parent not in (0,1,10,19)
".$filter_clause."

insert into #sysconf_2
select a.comment,
       valuetext = case when a.value2 is not null then convert(varchar(30),a.value2) else convert(varchar(30), a.value) end,
       parent=b.comment
from   ".$ServerName2."_SysConf a, 
       ".$ServerName2."_SysConf b , 
       (select maxTimestamp=max(SL.Timestamp) from ".$ServerName2."_SysConf SL
         where Timestamp <= '".$EndTimestamp2."') dt
where datediff(hh,a.Timestamp,dt.maxTimestamp) = 0
and   datediff(hh,b.Timestamp,dt.maxTimestamp) = 0
and   a.parent = b.config
and   a.parent = b.config
and   a.parent not in (0,1,10,19)
".$filter_clause."

insert #sysconf
select name,null,null,null,null from #sysconf_1
union
select name,null,null,null,null from #sysconf_2

update #sysconf
   set value_1 = a.valuetext,
       parent  = a.parent
from   #sysconf_1 a
where  #sysconf.name = a.name

update #sysconf
   set value_2 = valuetext
from   #sysconf_2 a
where  #sysconf.name = a.name

update #sysconf
   set difference = case when isnull(value_1, ' ') = isnull(value_2, ' ') then '      ' else '<<<<<<' end

select name,
       value_1,
       value_2,
       difference,
       parent  
from #sysconf
".$filter_clause2."
order by ".$orderPrc." 

drop table #sysconf
drop table #sysconf_1
drop table #sysconf_2
";


//if ($ArchSrvType=="Adaptive Server Enterprise") {
//        $query = $query . "
//  set forceplan off";
//}

$query_name = "sysconf_list";

?>
