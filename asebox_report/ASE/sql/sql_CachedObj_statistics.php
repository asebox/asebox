<?php
$query = "
	select 
          CacheName, 
          DBName, 
          OwnerN=isnull(OwnerName,'dbo'), 
          ObjectName, 
          IndexID, 
          ObjectType, 
          minCachedKB=min(CachedKB),
          avgCachedKB=avg(CachedKB), 
          maxCachedKB=max(CachedKB),  
          avgProcessesAccessing=avg(ProcessesAccessing),
          CacheID, DBID, ObjectID, OwnerUserID
	from ".$ServerName."_CachedObj
	where Timestamp >='".$StartTimestamp."'        
	and Timestamp <'".$EndTimestamp."'        
	and ( CacheName        like '".$filterCacheName."'    or '".$filterCacheName."'='')
	and ( DBName           like '".$filterDBName."'       or '".$filterDBName."'='')   
	and ( isnull(OwnerName,'dbo')          like '".$filterOwnerName."'    or '".$filterOwnerName."'='')
	and ( ObjectName       like '".$filterObjectName."'   or '".$filterObjectName."'='')      
	and ( ObjectType       like '".$filterObjectType."'   or '".$filterObjectType."'='')      
	and ( IndexID = convert(int,'".$filterIndexID."')           or '".$filterIndexID."'='')
	group by CacheID, DBID, ObjectID, IndexID, OwnerUserID, CacheName, DBName, isnull(OwnerName,'dbo'),  ObjectName, ObjectType
	order by ".$orderCachedObj."                                     
";

  $query_name = "CachedObj_statistics";
?>
