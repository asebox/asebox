<?php
$query = "select 
 Timestamp, ConnOrCursor,Name,Userid,numIQCursors,IQthreads,ConnOrCurCreateTime,IQGovernPriority,CmdLine
 from  ".$ServerName."_IQCtx 
 where Timestamp >='".$StartTimestamp."'        
	and Timestamp <'".$EndTimestamp."'
  and TxnID= ".$TxnID."
  and IQconnID= ".$IQconnID."
  and ConnHandle= ".$ConnHandle."
	order by Timestamp                                     
";

  $query_name = "IQTrans_detail";

?>


