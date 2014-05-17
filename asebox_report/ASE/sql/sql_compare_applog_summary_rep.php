<?php
$query="";
        
$filter_clause ="
and (Program    like '".$filterprogram."'   or '".$filterprogram."'       = '')
and ( (abs(datediff( ms, LogTime, StartTime )/1000.0)) > convert(numeric(10,2), '".$filtermintime."') or '".$filtermintime."'       = '')";
//    and ('Spid'      =convert(int,'".$filterspid."')  or '".$filterspid."'          = '')";
        
//$orderPrc=$order_by;    

$query = $query . "
if object_id('#applogsum') is not null drop table #applogsum

select Program, 
       Count_1   = convert( integer, 0), 
       Elapsed_1 = convert( numeric(16,2), 0), 
       Average_1 = convert( numeric(16,2), 0), 
       Count_2   = convert( integer, 0), 
       Elapsed_2 = convert( numeric(16,2), 0), 
       Average_2 = convert( numeric(16,2), 0)
into   #applogsum
from   #applogsum1
union
select Program, 
       Count_1   = convert( integer, 0), 
       Elapsed_1 = convert( numeric(16,2), 0), 
       Average_1 = convert( numeric(16,2), 0), 
       Count_2   = convert( integer, 0), 
       Elapsed_2 = convert( numeric(16,2), 0), 
       Average_2 = convert( numeric(16,2), 0)
from   #applogsum2

update #applogsum
   set Count_1   = cnt,
       Elapsed_1 = tot_time,
       Average_1 = avg_time
from   #applogsum1 a
where  #applogsum.Program = a.Program

update #applogsum
   set Count_2   = cnt,
       Elapsed_2 = tot_time,
       Average_2 = avg_time
from   #applogsum2 a
where  #applogsum.Program = a.Program

select Program,
       Count_1    = convert( integer, Count_1 ),
       Elapsed_1  = convert( numeric(16,2), Elapsed_1/60. ),
       Average_1  = convert( numeric(16,2), Average_1/60. ),
       Count_2    = convert( integer, Count_2 ),
       Elapsed_2  = convert( numeric(16,2), Elapsed_2/60. ),
       Average_2  = convert( numeric(16,2), Average_2/60. ),
       Delta_Time = convert( numeric(16,2), (Elapsed_1 - Elapsed_2) /60. ),
       Delta_Avg  = convert( numeric(16,2), (Average_1 - Average_2) /60. ),
       Message    = case when (Elapsed_1 - Elapsed_2) /60 < -10 then  '<<<<<<'
                        when (Elapsed_1 - Elapsed_2) /60 <  -6 then  '<<<<  '
                        when (Elapsed_1 - Elapsed_2) /60 <  -2 then  '<<    '
                        else '      '
                   end
from #applogsum
--where (Elapsed_1 > 60 OR Elapsed_2 > 60)
--order by Elapsed_1*100000 + Elapsed_2 desc
order by ".$orderPrc;

$query_name = "applog_summary";
?>