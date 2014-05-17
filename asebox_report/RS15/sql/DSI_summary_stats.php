<?php
// summary stats for DSI AND DSI EXEC of a same instance_id
$query_DSI_summary_stats =
"select A.instance_id, instance=(select instance from ".$ServerName."_Instances where instance_id=A.instance_id and instance like 'DSI,%' and Timestamp=
                                           (select max(Timestamp) from ".$ServerName."_Instances where instance_id=A.instance_id and instance like 'DSI,%'
                                            and Timestamp <= '".$StartTimestamp."')
                                      ),
DSICmdsSucceed                       = sum(DSICmdsSucceed                       ),           
DSITranGroupsSucceeded               = sum(DSITranGroupsSucceeded               ),           
DSIReadTransUngrouped                = sum(DSIReadTransUngrouped                ),           
InsertsRead                          = sum(InsertsRead                          ),           
UpdatesRead                          = sum(UpdatesRead                          ),           
DeletesRead                          = sum(DeletesRead                          ),           
SysTransRead                         = sum(SysTransRead                         ),           
CmdsSQLDDLRead                       = sum(CmdsSQLDDLRead                       ),           
CommitsRead                          = sum(CommitsRead                          )
   
from (
    select S.ID, instance_id, 
    DSICmdsSucceed                        = case when counter_id= 5028 then sum(convert(numeric(14,0),counter_total) ) else null end,   -- status=1164
    DSITranGroupsSucceeded                = case when counter_id= 5007 then sum(convert(numeric(14,0),counter_obs) ) else null end,   -- status= 652
    DSIReadTransUngrouped                 = case when counter_id= 5002 then sum(convert(numeric(14,0),counter_total) ) else null end,   -- status=1036
    InsertsRead                           = case when counter_id=57010 then sum(convert(numeric(14,0),counter_obs) ) else null end,   -- status= 516
    UpdatesRead                           = case when counter_id=57011 then sum(convert(numeric(14,0),counter_obs) ) else null end,   -- status= 516
    DeletesRead                           = case when counter_id=57012 then sum(convert(numeric(14,0),counter_obs) ) else null end,   -- status= 516
    SysTransRead                          = case when counter_id=57009 then sum(convert(numeric(14,0),counter_obs) ) else null end,   -- status= 516
    CmdsSQLDDLRead                        = case when counter_id=57120 then sum(convert(numeric(14,0),counter_obs) ) else null end,   -- status= 512
    CommitsRead                           = case when counter_id=57008 then sum(convert(numeric(14,0),counter_obs) ) else null end   -- status= 512


    from ".$ServerName."_Instances I, ".$ServerName."_RSStats S
    where I.ID=S.ID
    and instance like 'DSI%'
    and S.Timestamp >='".$StartTimestamp."'
    and S.Timestamp <='".$EndTimestamp."'
    group by S.Timestamp, S.ID, instance_id,  counter_id
) A
group by A.instance_id
order by A.instance_id";

?>













