<?php

        $query="";

        $filter_clause ="
    and (ErrorMessage   like '".$filtererrormessage."'    or '".$filtererrormessage."'   = '')";
	
$query = $query . "
select  Timestamp
        ,Interval
        ,SPID
        ,KPID
        ,FamilyID
        ,EngineNumber
        ,ErrorNumber
        ,Severity
        ,State
        ,ErrorMessage
from ".$ServerName."_ErrLog
where Timestamp >='".$StartTimestamp."'        
and   Timestamp <='".$EndTimestamp."'        
and   ErrorMessage not like 'DBCC TRACEO%'
and   ErrorMessage not like 'Begin%'
and   ErrorMessage not like 'REORG%'
and   ErrorMessage not like 'Cannot read%'
 ".$filter_clause;

  $query_name = "errorlog_statistics";

?>


