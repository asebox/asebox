<?php
$query = "
select  Ts, M.IQconnID, MinKBRelease, MaxKBRelease,Timeblocking_s, ConnCreateTime=convert(varchar,C.ConnCreateTime,109)
from 
(
select Ts=min(Timestamp), IQconnID, MinKBRelease, MaxKBRelease,Timeblocking_s=datediff(ss, min(Timestamp), max(Timestamp)), MaxTs=max(Timestamp)
from ".$ServerName."_IQVersUse 
where Timestamp >='".$StartTimestamp."'        
and Timestamp <'".$EndTimestamp."'
/*and MinKBRelease=MaxKBRelease
and MinKBRelease>0*/
group by IQconnID,MinKBRelease,MaxKBRelease
) M,
".$ServerName."_IQCnx C
where M.IQconnID = C.IQconnID
and C.Timestamp between M.Ts and M.MaxTs
and C.Timestamp = (select min(Timestamp) from ".$ServerName."_IQCnx where M.IQconnID = IQconnID and Timestamp between M.Ts and M.MaxTs)
order by Ts,M.IQconnID,MinKBRelease,MaxKBRelease
";

  $query_name = "IQVersions_statistics";

// Global SQL variable
$_SESSION['SQLCODE'] = "IQVersions_statistics";
$_SESSION['SQLTEXT_IQVersions_STATISTICS'] = $query;
//
?>
