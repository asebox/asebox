<?php
$query_SQT_summary_stats =
"select A.ID,
instance_id, instance,
CmdsRead         = sum(CmdsRead             ),
OpenTransAdd     = sum(OpenTransAdd         ),
AvgCmdsTran         = str(avg(CmdsTran             ),12,2),
AvgCacheMemUsed     = str(avg(CacheMemUsed         ),12,2),
AvgMemUsedTran      = str(avg(MemUsedTran          ),12,2),
TransRemoved     = sum(TransRemoved         ),
TruncTransAdd    = sum(TruncTransAdd        ),
ClosedTransAdd   = sum(ClosedTransAdd       ),
ReadTransAdd     = sum(ReadTransAdd         ),
OpenTransRm      = sum(OpenTransRm          ),
TruncTransRm     = sum(TruncTransRm         ),
ClosedTransRm    = sum(ClosedTransRm        ),
ReadTransRm      = sum(ReadTransRm          ),
EmptyTransRm     = sum(EmptyTransRm         ),
AvgSQTCacheLowBnd   = str(avg(SQTCacheLowBnd       ),12,2),
SQTWakeupRead    = sum(SQTWakeupRead        ),
SQTReadSQMTime   = sum(SQTReadSQMTime       ),
SQTAddCacheTime  = sum(SQTAddCacheTime      ),
SQTDelCacheTime  = sum(SQTDelCacheTime      ),
AvgSQTOpenTrans     = str(avg(SQTOpenTrans         ),12,2),
AvgSQTClosedTrans   = str(avg(SQTClosedTrans       ),12,2),
AvgSQTReadTrans     = str(avg(SQTReadTrans         ),12,2),
AvgSQTTruncTrans    = str(avg(SQTTruncTrans        ),12,2),

CacheLow                = sum(CacheLow              ),
SQTResyncPurgedTrans    = sum(SQTResyncPurgedTrans  )

from (
    select S.ID, instance_id, instance,
    CmdsRead                        = case when counter_id=24000  then sum(convert(numeric(14,0), counter_obs) ) else null end,   --  524 
    OpenTransAdd                    = case when counter_id=24001  then sum(convert(numeric(14,0), counter_obs) ) else null end,   --  516 
    CmdsTran                        = case when counter_id=24002  then sum(convert(numeric(14,0), avg_ttl_obs) ) else null end,   -- 1028 
    CacheMemUsed                    = case when counter_id=24005  then sum(convert(numeric(14,0), avg_ttl_obs) ) else null end,   -- 1036 
    MemUsedTran                     = case when counter_id=24006  then sum(convert(numeric(14,0), avg_ttl_obs) ) else null end,   -- 1024 
    TransRemoved                    = case when counter_id=24009  then sum(convert(numeric(14,0), counter_obs) ) else null end,   --  524 
    TruncTransAdd                   = case when counter_id=24011  then sum(convert(numeric(14,0), counter_obs) ) else null end,   --  516 
    ClosedTransAdd                  = case when counter_id=24012  then sum(convert(numeric(14,0), counter_obs) ) else null end,   --  516 
    ReadTransAdd                    = case when counter_id=24013  then sum(convert(numeric(14,0), counter_obs) ) else null end,   --  516 
    OpenTransRm                     = case when counter_id=24014  then sum(convert(numeric(14,0), counter_obs) ) else null end,   --  516 
    TruncTransRm                    = case when counter_id=24015  then sum(convert(numeric(14,0), counter_obs) ) else null end,   --  524 
    ClosedTransRm                   = case when counter_id=24016  then sum(convert(numeric(14,0), counter_obs) ) else null end,   --  516 
    ReadTransRm                     = case when counter_id=24017  then sum(convert(numeric(14,0), counter_obs) ) else null end,   --  516 
    EmptyTransRm                    = case when counter_id=24018  then sum(convert(numeric(14,0), counter_obs) ) else null end,   --  512 
    SQTCacheLowBnd                  = case when counter_id=24019  then sum(convert(numeric(14,0), avg_ttl_obs) ) else null end,   -- 1036 
    SQTWakeupRead                   = case when counter_id=24020  then sum(convert(numeric(14,0), counter_obs) ) else null end,   --  512 
    SQTReadSQMTime                  = case when counter_id=24021  then sum(convert(numeric(14,0), counter_obs) ) else null end,   --   33 
    SQTAddCacheTime                 = case when counter_id=24023  then sum(convert(numeric(14,0), counter_obs) ) else null end,   --   33 
    SQTDelCacheTime                 = case when counter_id=24025  then sum(convert(numeric(14,0), counter_obs) ) else null end,   --   33 
    SQTOpenTrans                    = case when counter_id=24027  then sum(convert(numeric(14,0), avg_ttl_obs) ) else null end,   -- 1036 
    SQTClosedTrans                  = case when counter_id=24028  then sum(convert(numeric(14,0), avg_ttl_obs) ) else null end,   -- 1036 
    SQTReadTrans                    = case when counter_id=24029  then sum(convert(numeric(14,0), avg_ttl_obs) ) else null end,   -- 1036 
    SQTTruncTrans                   = case when counter_id=24030  then sum(convert(numeric(14,0), avg_ttl_obs) ) else null end,    -- 1036 

    CacheLow                        = case when counter_id=24031  then sum(convert(numeric(14,0), counter_obs) ) else null end,    -- 1036 
    SQTResyncPurgedTrans            = case when counter_id=24032  then sum(convert(numeric(14,0), counter_obs) ) else null end     -- 1036 

    from ".$ServerName."_Instances I, ".$ServerName."_RSStats S
    where I.ID=S.ID
    and instance like 'SQT%'
    and S.Timestamp >='".$StartTimestamp."'
    and S.Timestamp <='".$EndTimestamp."'
    ".$ID_search_clause."
    group by S.Timestamp, S.ID, instance_id, instance, counter_id
) A
group by A.ID, instance_id, instance
order by instance_id";

?>