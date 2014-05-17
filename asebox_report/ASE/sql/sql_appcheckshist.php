<?php
    $query="";
    
    $filter_clause ="
and (ChkName    like '".$filtername   ."' or '".$filtername   ."' = '')
and (ChkMessage like '".$filtermessage."' or '".$filtermessage."' = '')";

$sts=-1;
if ($filterstatus == "OK")       { $sts=0 ; };
if ($filterstatus == "WARNING")  { $sts=1 ; };
if ($filterstatus == "CRITICAL") { $sts=2 ; };
if ($filterstatus == "UNKNOWN")  { $sts=3 ; };

if ($sts > 0) {
   $filter_clause = $filter_clause." and (ChkStatus = ".$sts.")";
};


//    $filter_clause ="";
        
//$orderChk=$order_by;  

$query = $query . "
select 
     ChkName   
    ,ChkTime   
    ,ChkStatMsg = case when ChkStatus=0 then 'OK' when ChkStatus=1 then 'WARNING' when ChkStatus=2 then 'CRITICAL' else 'UNKNOWN' end
    ,ChkStatus
    ,ChkMessage
from ".$ServerName."_ChecksHist
where (1=1)" . $filter_clause."
order by ".$orderChk;

$query_name = "appchecks_status";
?>
