<?php
  $blocksize = 16; // This is currently fixed but should be based on config

$query = "select 
statqueues.Info,

statqueues.AVG_active_queue_sz_Mb,
statqueues.MAX_active_queue_sz_Mb,
LAST_active_queue_sz_Mb=(
(
convert(numeric(20,0), substring(Last_Seg_Block, 1, patindex('%.%',Last_Seg_Block)))*64.+
(convert(numeric(20,0), substring(Last_Seg_Block, patindex('%.%',Last_Seg_Block)+1, datalength(Last_Seg_Block))))*1.)
-
(
convert(numeric(20,0), substring(Next_Read, 1, patindex('%.%',Next_Read)))*64.+
(convert(numeric(20,0), substring(Next_Read, patindex('%.%',Next_Read)+1, datalength(Next_Read)))-1)*1.)
)*".$blocksize."/1024,

statqueues.sav_int_mn,

statqueues.AVG_saved_queue_sz_Mb,
statqueues.MAX_saved_queue_sz_Mb,
LAST_saved_queue_sz_Mb=(
(convert(numeric(20,0), substring(Next_Read, 1, patindex('%.%',Next_Read))))*64. -
convert(numeric(20,0), substring(Save_Int_Seg, patindex('%:%',Save_Int_Seg)+1, datalength(Save_Int_Seg)))*64.
)*".$blocksize."/1024

from (
select Info,
sav_int_mn=max(case when lower(Save_Int_Seg) like 'strict%' then 'strict' else substring(Save_Int_Seg, 1, patindex('%:%',Save_Int_Seg)-1)  end),

AVG_saved_queue_sz_Mb=avg(
(convert(numeric(20,0), substring(Next_Read, 1, patindex('%.%',Next_Read))))*64. -
convert(numeric(20,0), substring(Save_Int_Seg, patindex('%:%',Save_Int_Seg)+1, datalength(Save_Int_Seg)))*64.
)*".$blocksize."/1024,

AVG_active_queue_sz_Mb=avg(
(
convert(numeric(20,0), substring(Last_Seg_Block, 1, patindex('%.%',Last_Seg_Block)))*64.+
(convert(numeric(20,0), substring(Last_Seg_Block, patindex('%.%',Last_Seg_Block)+1, datalength(Last_Seg_Block))))*1.)
-
(
convert(numeric(20,0), substring(Next_Read, 1, patindex('%.%',Next_Read)))*64.+
(convert(numeric(20,0), substring(Next_Read, patindex('%.%',Next_Read)+1, datalength(Next_Read)))-1)*1.)
)*".$blocksize."/1024,

MAX_saved_queue_sz_Mb=max(
(convert(numeric(20,0), substring(Next_Read, 1, patindex('%.%',Next_Read))))*64. -
convert(numeric(20,0), substring(Save_Int_Seg, patindex('%:%',Save_Int_Seg)+1, datalength(Save_Int_Seg)))*64.
)*".$blocksize."/1024,

MAX_active_queue_sz_Mb=max(
(
convert(numeric(20,0), substring(Last_Seg_Block, 1, patindex('%.%',Last_Seg_Block)))*64.+
(convert(numeric(20,0), substring(Last_Seg_Block, patindex('%.%',Last_Seg_Block)+1, datalength(Last_Seg_Block))))*1.)
-
(
convert(numeric(20,0), substring(Next_Read, 1, patindex('%.%',Next_Read)))*64.+
(convert(numeric(20,0), substring(Next_Read, patindex('%.%',Next_Read)+1, datalength(Next_Read)))-1)*1.)
)*".$blocksize."/1024



         from ".$ServerName."_RSWhoSQM
         where  
              Timestamp >='".$StartTimestamp."'
          and Timestamp <='".$EndTimestamp."'
group by Info
) statqueues, ".$ServerName."_RSWhoSQM lastqueues
where statqueues.Info = lastqueues.Info
and lastqueues.Timestamp=(select max(Timestamp) from ".$ServerName."_RSWhoSQM where Timestamp <='".$EndTimestamp."')
order by ".$orderqueues;

  $query_name = "rsqueues_statistics";

?>
