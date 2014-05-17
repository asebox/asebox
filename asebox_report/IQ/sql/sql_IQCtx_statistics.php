<?php
$query = "
        select C.Timestamp, C.Interval,  C.TxnID,
        LastReqTime, ReqType, IQCmdType, LastIQCmdTime, IQCursors, LowestIQCursorState,
        C.IQthreads, TempTableSpaceKB, TempWorkSpaceKB, satoiq_count, iqtosa_count,
        ConnOrCursor, numIQCursors, ConnOrCurCreateTime, IQGovernPriority
        , CmdLine
        from ".$ServerName."_IQCnx C,".$ServerName."_IQCtx X
        where dateadd(ss,-12, C.Timestamp ) <= X.Timestamp
        and dateadd(ss, +12, C.Timestamp ) >= X.Timestamp
        and C.ConnHandle = X.ConnHandle
        and C.IQconnID = X.IQconnID
        and C.TxnID=X.TxnID
        
        and C.Timestamp >='".$StartTimestamp."'        
	and C.Timestamp <'".$EndTimestamp."'
	
	and C.IQconnID       =  convert(int,'".$IQconnID."')
	and C.ConnHandle       =  convert(int,'".$ConnHandle."')
	
	order by C.Timestamp                                     
";

  $query_name = "IQCtx_statistics";

// Global SQL variable
$_SESSION['SQLCODE'] = "IQCtx_statistics";
$_SESSION['SQLTEXT_IQCTX_STATISTICS'] = $query;
//
?>
