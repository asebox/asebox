<?php
        $filter_clause ="
	and (name     like '".$filtername."'     or '".$filtername."'     = '')
	and (fullname like '".$filterfullname."' or '".$filterfullname."' = '')";

$query = "set rowcount ".$rowcnt."
if object_id('#syslogins') is not null drop table #syslogins

select name, fullname, 
totcpu=max(totcpu) - min(totcpu), 
totio =max(totio ) - min(totio ),
cpupc =convert(numeric, 0),
iopc  =convert(numeric, 0)
into #syslogins
from ".$ServerName."_SysLogins
where Timestamp >='".$StartTimestamp."'        
and Timestamp <'".$EndTimestamp."'        
".$filter_clause."
group by name, fullname

select sumtotcpu = convert( numeric(20,2), sum(totcpu) ),
       sumtotio  = convert( numeric(20,2), sum(totio)  )
into   #tot
from   #syslogins

declare @sumtotcpu numeric(20,2)
declare @sumtotio  numeric(20,2) 
select @sumtotcpu = sumtotcpu,
       @sumtotio  = sumtotio  
from #tot

update #syslogins
   set cpupc = case when @sumtotcpu = 0 then 0 else 100 * totcpu / @sumtotcpu end,
       iopc  = case when @sumtotio  = 0 then 0 else 100 * totio / @sumtotio end

select *
from   #syslogins
order by ".$orderPrc." 
set rowcount 0";

  $query_name = "SysLogin_statistics";
?>

