<?php
$query = "
	select 

     CCTime=convert(varchar,ConnCreateTime,109),
     A.IQconnID,
     ConnHandle,
     Name,
     Userid,
     Clk=substring(CommLink,1,30),
     MaxIQCursors =max(IQCursors),
     MaxIQthreads = max(IQthreads),
     MaxTempTableSpaceKB= max(TempTableSpaceKB),
     MaxTempWorkSpaceKB = max(TempWorkSpaceKB),
     Sum_satoiq_count =sum(1.*satoiq_count),
     Sum_iqtosa_count =sum(1.*iqtosa_count),
     NAddr = max(NodeAddr),
     MxKBRelease = max(MaxKBRelease)
     ".$host_selclause."

	from ".$ServerName."_IQCnx A left outer join  ".$ServerName."_IQVersUse B on A.IQconnID = B.IQconnID and B.Timestamp between dateadd(ss,-10,A.Timestamp) and dateadd(ss,10,A.Timestamp)
	where A.Timestamp >='".$StartTimestamp."'        
	and A.Timestamp <'".$EndTimestamp."'        
	and (A.IQconnID       =  convert(int,'".$filterIQconnID."')   or '".$filterIQconnID."'='')
	and (ConnHandle     =  convert(int,'".$filterConnHandle."') or '".$filterConnHandle."'='')
	and (Name         like '%".$filterName."%'        or Name is null)
	and (Userid       like '%".$filterUserid."%'     or Userid is null)  
	and (CommLink       like '%".$filterCommLink."%'  or CommLink is null)
	and ( NodeAddr      like '%".$filterNodeAddr."%'      or NodeAddr is null)
	";
	
	if ($host_selclause != "") 
	   $query = $query .
	"and ( Hostname      like '%".$filterHost."%'      or Hostname is null)
	";
	
	$query= $query . "
	group by ConnCreateTime, A.IQconnID, ConnHandle, Name, Userid, substring(CommLink,1,30)" . $host_selclause."
	order by ".$orderPrc."                                     
    ";

  $query_name = "IQCnx_statistics";
?>
