<?php
if (!isset($IQconnID)) $IQconnID=-1;
if (!isset($ConnHandle)) $ConnHandle=-1;
$query = "

	select 

     A.TxnCreateTime,
     A.TxnID,
     status=max(case when open_transactions.status='OPEN' then 'OPEN' else 'CLOSED' end),
     CmtID = max (CmtID),
     MaxMainTableKBCr=  max(MainTableKBCr    ),
     MaxMainTableKBDr=  max(MainTableKBDr    ),
     MaxTempTableKBCr=  max(TempTableKBCr    ),
     MaxTempTableKBDr=  max(TempTableKBDr    ),
     MaxTempWorkSpaceKB=max(TempWorkSpaceKB  ),
     IQconnID, ConnHandle, Name, Userid

	from ".$ServerName."_IQXacts A left outer join
	
	     (select TxnCreateTime, TxnID, status='OPEN' from ".$ServerName."_IQXacts
              where Timestamp = (select max(Timestamp) from ".$ServerName."_IQXacts
                               where Timestamp >='".$StartTimestamp."'        
                               and Timestamp <'".$EndTimestamp."')
               and CmtID = 0
	     
	     ) open_transactions
	     on A.TxnCreateTime=open_transactions.TxnCreateTime and A.TxnID = open_transactions.TxnID
	where Timestamp >='".$StartTimestamp."'        
	and Timestamp <'".$EndTimestamp."'
	
	and (A.TxnID       =  convert(int,'".$filterTxnID."')   or '".$filterTxnID."'='')
	and (IQconnID       =  convert(int,'".$IQconnID."') or ".$IQconnID."=-1)
	and (ConnHandle       =  convert(int,'".$ConnHandle."') or ".$ConnHandle."=-1)
	
	group by A.TxnCreateTime,A.TxnID, IQconnID, ConnHandle, Name, Userid

	having  max(case when open_transactions.status='OPEN' then 'OPEN' else 'CLOSED' end)
	         like upper( '%".$filterstatus."%') or     '".$filterstatus."'=''
	order by ".$orderTxn."                                     
";

  $query_name = "IQTrans_statistics";

// Global SQL variable
$_SESSION['SQLCODE'] = "IQTrans_statistics";
$_SESSION['SQLTEXT_IQTrans_STATISTICS'] = $query;
//
?>
