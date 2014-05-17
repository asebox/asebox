<?php
if (($SQLtableExists==1) && ($PLNtableExists==1)) { 
	$query = "set rowcount ".$rowcnt."
	select 
    STMStats.bootcount                                                     ,
    STMStats.SSQLID                                                        ,
    STMStats.Hashkey                                                       ,
    UserID                                                        ,
    DBID                                                          ,
    UseCount                                    ,
    StatementSize                              ,
    MaxPlanSizeKB                             ,
    MaxCurrentUsageCount                   ,
    MaxUsageCount                              ,
    NumRecompilesSchemaChanges,
    NumRecompilesPlanFlushes    ,
    MetricsCount                           ,
    MaxPIO                                         ,
    AvgPIO                                         ,
    MaxLIO                                         ,
    AvgLIO                                         ,
    MaxCpuTime                                   ,
    AvgCpuTime                                   ,
    MaxElapsedTime                           ,
    AvgElapsedTime                           ,
    DBName                                                        ,
    CachedDate                                                    ,
    LastUsedDate                                ,
    planOK,
    SQLText
from (
	select 
    STM.bootcount                                                     ,
    STM.SSQLID                                                        ,
    STM.Hashkey                                                       ,
    UserID                                                        ,
    DBID                                                          ,
    UseCount=sum(UseCount_smp)                                    ,
    StatementSize=max(StatementSize)                              ,
    MaxPlanSizeKB=max(MaxPlanSizeKB)                              ,
    MaxCurrentUsageCount=max(CurrentUsageCount)                   ,
    MaxUsageCount=max(MaxUsageCount)                              ,
    NumRecompilesSchemaChanges=sum(NumRecompilesSchemaChanges_smp),
    NumRecompilesPlanFlushes=sum(NumRecompilesPlanFlushes_smp)    ,
    MetricsCount=sum(MetricsCount_smp )                           ,
    MaxPIO=str(max(1.*MaxPIO) ,14,0)                                           ,
    AvgPIO=str(avg(1.*AvgPIO) ,14,0)                                           ,
    MaxLIO=str(max(1.*MaxLIO) ,14,0)                                           ,
    AvgLIO=str(avg(1.*AvgLIO) ,14,0)                                           ,
    MaxCpuTime=str(max(1.*MaxCpuTime),10,0)                                    ,
    AvgCpuTime=str(avg(1.*AvgCpuTime),10,0)                                    ,
    MaxElapsedTime=str(max(1.*MaxElapsedTime),10,0)                            ,
    AvgElapsedTime=str(avg(1.*AvgElapsedTime),10,0)                            ,
    DBName                                                        ,
    CachedDate                                                    ,
    LastUsedDate=max(LastUsedDate)                                ,
    planOK=case when PLN.bootcount is not null then 'OK' else '' end
	from (".$ServerName."_CachedSTM STM  left outer join ".$ServerName."_CachedPLN PLN on STM.bootcount=PLN.bootcount and STM.SSQLID=PLN.SSQLID and STM.Hashkey=PLN.Hashkey and PLN.Sequence=0
	                                     left outer join ".$ServerName."_CachedSQL SQL on STM.bootcount=SQL.bootcount and STM.SSQLID=SQL.SSQLID and STM.Hashkey=SQL.Hashkey)
	where STM.Timestamp >='".$StartTimestamp."'        
	and STM.Timestamp <'".$EndTimestamp."' 
	and (STM.bootcount = convert(int,'".$filterbootcount."') or '".$filterbootcount."'='')
	and (STM.SSQLID = convert(int,'".$filterSSQLID."') or '".$filterSSQLID."'='')
	and (STM.UserID = convert(int,'".$filterUserID."') or '".$filterUserID."'='')
	and (STM.SUserID = convert(int,'".$filterSUserID."') or '".$filterSUserID."'='')
	and (STM.DBName like '".$filterDBName."' or '".$filterDBName."'='')
	and (SQLText like '".$filterSQLText."' or '".$filterSQLText."'='')
	group by STM.bootcount,STM.SSQLID,STM.Hashkey,UserID,SUserID,DBID,DBName,CachedDate, case when PLN.bootcount is not null then 'OK' else '' end
  ) STMStats left outer join ".$ServerName."_CachedSQL CSQL
         on  STMStats.bootcount=CSQL.bootcount and STMStats.SSQLID=CSQL.SSQLID and STMStats.Hashkey=CSQL.Hashkey ";
	
}
else 
    if ($SQLtableExists==1) { 
	$query = "set rowcount ".$rowcnt."
	select 
    STMStats.bootcount                                                     ,
    STMStats.SSQLID                                                        ,
    STMStats.Hashkey                                                       ,
    UserID                                                        ,
    DBID                                                          ,
    UseCount                                    ,
    StatementSize                              ,
    MaxPlanSizeKB                             ,
    MaxCurrentUsageCount                   ,
    MaxUsageCount                              ,
    NumRecompilesSchemaChanges,
    NumRecompilesPlanFlushes    ,
    MetricsCount                           ,
    MaxPIO                                         ,
    AvgPIO                                         ,
    MaxLIO                                         ,
    AvgLIO                                         ,
    MaxCpuTime                                   ,
    AvgCpuTime                                   ,
    MaxElapsedTime                           ,
    AvgElapsedTime                           ,
    DBName                                                        ,
    CachedDate                                                    ,
    LastUsedDate                                ,
    planOK,
    SQLText
from (
	select 
    STM.bootcount                                                     ,
    STM.SSQLID                                                        ,
    STM.Hashkey                                                       ,
    UserID                                                        ,
    DBID                                                          ,
    UseCount=sum(UseCount_smp)                                    ,
    StatementSize=max(StatementSize)                              ,
    MaxPlanSizeKB=max(MaxPlanSizeKB)                              ,
    MaxCurrentUsageCount=max(CurrentUsageCount)                   ,
    MaxUsageCount=max(MaxUsageCount)                              ,
    NumRecompilesSchemaChanges=sum(NumRecompilesSchemaChanges_smp),
    NumRecompilesPlanFlushes=sum(NumRecompilesPlanFlushes_smp)    ,
    MetricsCount=sum(MetricsCount_smp )                           ,
    MaxPIO=str(max(1.*MaxPIO) ,14,0)                                           ,
    AvgPIO=str(avg(1.*AvgPIO) ,14,0)                                           ,
    MaxLIO=str(max(1.*MaxLIO) ,14,0)                                           ,
    AvgLIO=str(avg(1.*AvgLIO) ,14,0)                                           ,
    MaxCpuTime=str(max(1.*MaxCpuTime),10,0)                                    ,
    AvgCpuTime=str(avg(1.*AvgCpuTime),10,0)                                    ,
    MaxElapsedTime=str(max(1.*MaxElapsedTime),10,0)                            ,
    AvgElapsedTime=str(avg(1.*AvgElapsedTime),10,0)                            ,
    DBName                                                        ,
    CachedDate                                                    ,
    LastUsedDate=max(LastUsedDate)                                ,
    planOK=''
	from (".$ServerName."_CachedSTM STM  
	                                     left outer join ".$ServerName."_CachedSQL SQL on STM.bootcount=SQL.bootcount and STM.SSQLID=SQL.SSQLID and STM.Hashkey=SQL.Hashkey)
	where STM.Timestamp >='".$StartTimestamp."'        
	and STM.Timestamp <'".$EndTimestamp."' 
	and (STM.bootcount = convert(int,'".$filterbootcount."') or '".$filterbootcount."'='')
	and (STM.SSQLID = convert(int,'".$filterSSQLID."') or '".$filterSSQLID."'='')
	and (STM.UserID = convert(int,'".$filterUserID."') or '".$filterUserID."'='')
	and (STM.SUserID = convert(int,'".$filterSUserID."') or '".$filterSUserID."'='')
	and (STM.DBName like '".$filterDBName."' or '".$filterDBName."'='')
	and (SQLText like '".$filterSQLText."' or '".$filterSQLText."'='')
	group by STM.bootcount,STM.SSQLID,STM.Hashkey,UserID,SUserID,DBID,DBName,CachedDate
  ) STMStatsleft outer join  ".$ServerName."_CachedSQL CSQL
         on  STMStats.bootcount=CSQL.bootcount and STMStats.SSQLID=CSQL.SSQLID and STMStats.Hashkey=CSQL.Hashkey ";
}
else	
{
	$query = "set rowcount ".$rowcnt."
	select 
    STMStats.bootcount                                                     ,
    STMStats.SSQLID                                                        ,
    STMStats.Hashkey                                                       ,
    UserID                                                        ,
    DBID                                                          ,
    UseCount                                    ,
    StatementSize                              ,
    MaxPlanSizeKB                             ,
    MaxCurrentUsageCount                   ,
    MaxUsageCount                              ,
    NumRecompilesSchemaChanges,
    NumRecompilesPlanFlushes    ,
    MetricsCount                           ,
    MaxPIO                                         ,
    AvgPIO                                         ,
    MaxLIO                                         ,
    AvgLIO                                         ,
    MaxCpuTime                                   ,
    AvgCpuTime                                   ,
    MaxElapsedTime                           ,
    AvgElapsedTime                           ,
    DBName                                                        ,
    CachedDate                                                    ,
    LastUsedDate                                ,
    planOK='',
    SQLText=''
from (
	select 
    STM.bootcount                                                     ,
    STM.SSQLID                                                        ,
    STM.Hashkey                                                       ,
    UserID                                                        ,
    DBID                                                          ,
    UseCount=sum(UseCount_smp)                                    ,
    StatementSize=max(StatementSize)                              ,
    MaxPlanSizeKB=max(MaxPlanSizeKB)                              ,
    MaxCurrentUsageCount=max(CurrentUsageCount)                   ,
    MaxUsageCount=max(MaxUsageCount)                              ,
    NumRecompilesSchemaChanges=sum(NumRecompilesSchemaChanges_smp),
    NumRecompilesPlanFlushes=sum(NumRecompilesPlanFlushes_smp)    ,
    MetricsCount=sum(MetricsCount_smp )                           ,
    MaxPIO=str(max(1.*MaxPIO) ,14,0)                                           ,
    AvgPIO=str(avg(1.*AvgPIO) ,14,0)                                           ,
    MaxLIO=str(max(1.*MaxLIO) ,14,0)                                           ,
    AvgLIO=str(avg(1.*AvgLIO) ,14,0)                                           ,
    MaxCpuTime=str(max(1.*MaxCpuTime),10,0)                                    ,
    AvgCpuTime=str(avg(1.*AvgCpuTime),10,0)                                    ,
    MaxElapsedTime=str(max(1.*MaxElapsedTime),10,0)                            ,
    AvgElapsedTime=str(avg(1.*AvgElapsedTime),10,0)                            ,
    DBName                                                        ,
    CachedDate                                                    ,
    LastUsedDate=max(LastUsedDate)                                
	from ".$ServerName."_CachedSTM STM
	where STM.Timestamp >='".$StartTimestamp."'        
	and STM.Timestamp <'".$EndTimestamp."' 
	and (STM.bootcount = convert(int,'".$filterbootcount."') or '".$filterbootcount."'='')
	and (STM.SSQLID = convert(int,'".$filterSSQLID."') or '".$filterSSQLID."'='')
	and (STM.UserID = convert(int,'".$filterUserID."') or '".$filterUserID."'='')
	and (STM.SUserID = convert(int,'".$filterSUserID."') or '".$filterSUserID."'='')
	and (STM.DBName like '".$filterDBName."' or '".$filterDBName."'='')
	group by STM.bootcount,STM.SSQLID,STM.Hashkey,UserID,SUserID,DBID,DBName,CachedDate
  ) STMStats";
}


  $query = $query ." order by ".$orderCachedStmt." 
  set rowcount 0";
	
  $query_name = "cachedstatement_statistics";

?>
