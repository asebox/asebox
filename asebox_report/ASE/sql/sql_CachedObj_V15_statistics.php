<?php
$query = "
	select 
          CacheName, 
          DBName, 
          OwnerN=isnull(OwnerName,'dbo'), 
          ObjectName, 
          IndexID, 
          ObjectType, 
          PartitionName, 
<<<<<<< HEAD
          minCachedKB=min(CachedKB),
          avgCachedKB=avg(CachedKB), 
          maxCachedKB=max(CachedKB),  
          avgProcessesAccessing=avg(ProcessesAccessing), 
          avgTotalSizeKB=avg(TotalSizeKB), 
=======
          minCachedKB=min(convert( numeric(19,0), CachedKB)),
          avgCachedKB=avg(convert( numeric(19,0), CachedKB)), 
          maxCachedKB=max(convert( numeric(19,0), CachedKB)),  
          avgProcessesAccessing=avg(convert( numeric(19,0), ProcessesAccessing)), 
          avgTotalSizeKB=avg(convert( numeric(19,0), TotalSizeKB)), 
>>>>>>> 3.1.0
          CacheID, DBID, ObjectID, OwnerUserID, PartitionID

	from ".$ServerName."_CachedObj
	where Timestamp >='".$StartTimestamp."'        
	and Timestamp <'".$EndTimestamp."'        
	and ( CacheName        like '".$filterCacheName."'    or '".$filterCacheName."'='')
	and ( DBName           like '".$filterDBName."'       or '".$filterDBName."'='')   
	and ( isnull(OwnerName,'dbo')          like '".$filterOwnerName."'    or '".$filterOwnerName."'='')
	and ( ObjectName       like '".$filterObjectName."'   or '".$filterObjectName."'='')      
	and ( ObjectType       like '".$filterObjectType."'   or '".$filterObjectType."'='')      
	and ( IndexID = convert(int,'".$filterIndexID."')           or '".$filterIndexID."'='')
	group by CacheID, DBID, ObjectID, IndexID, OwnerUserID, PartitionID, CacheName, DBName, OwnerName, ObjectName, ObjectType, PartitionName
	order by ".$orderCachedObj."                                     
";

  $query_name = "CachedObj_statistics";
?>
