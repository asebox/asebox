<?php
$query = "set rowcount ".$rowcnt." select
  DBID,
  ObjectID,
  PartitionID,
  CompRowInserted=sum(delta_CompRowInserted),
  CompRowUpdated=sum(delta_CompRowUpdated),
  CompRowForward=sum(delta_CompRowForward),
  CompRowScan=sum(delta_CompRowScan),
  RowDecompressed=sum(delta_RowDecompressed),
  RowPageDecompressed=sum(delta_RowPageDecompressed),
  ColDecompressed=sum(delta_ColDecompressed),
  RowCompNoneed=sum(delta_RowCompNoneed),
  PageCompNoneed=sum(delta_PageCompNoneed),
  PagesCompressed=sum(delta_PagesCompressed),
  AvgBytesSavedPageLevel=avg(AvgBytesSavedPageLevel),
  TableName
from ".$ServerName."_Compress
where Timestamp >='".$StartTimestamp."'        
and Timestamp <'".$EndTimestamp."'        
	and ( DBID = convert(int,'".$filterDBID."')           or '".$filterDBID."'='')
	and ( ObjectID = convert(int,'".$filterObjectID."')           or '".$filterObjectID."'='')
	and ( PartitionID = convert(int,'".$filterPartitionID."')           or '".$filterPartitionID."'='')
	and ( TableName       like '".$filterTableName."'   or '".$filterTableName."'='')      
group by DBID, ObjectID, PartitionID, TableName
order by ".$orderCompressObj." set rowcount 0";

$query_name = "compress_statistics";

//
?>
