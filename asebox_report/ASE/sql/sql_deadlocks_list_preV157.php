<?php
$query = "set rowcount ".$rowcnt."
          select *
		from ".$ServerName."_DeadLock
	        where ResolveTime >='".$StartTimestamp."'        
	        and ResolveTime <'".$EndTimestamp."'        

                and (DeadlockID     = convert(int,'".$filterDeadlockID     ."') or '".$filterDeadlockID     ."' ='')
                and (VictimKPID     = convert(int,'".$filterVictimKPID     ."') or '".$filterVictimKPID     ."' ='')
                and (ResolveTime    = convert(datetime,'".$filterResolveTime    ."') or '".$filterResolveTime    ."' ='')
                and (ObjectDBID     = convert(int,'".$filterObjectDBID     ."') or '".$filterObjectDBID     ."' ='')
                and (PageNumber     = convert(int,'".$filterPageNumber     ."') or '".$filterPageNumber     ."' ='')
                and (RowNumber      = convert(int,'".$filterRowNumber      ."') or '".$filterRowNumber      ."' ='')
                and (HeldFamilyID   = convert(int,'".$filterHeldFamilyID   ."') or '".$filterHeldFamilyID   ."' ='')
                and (HeldSPID       = convert(int,'".$filterHeldSPID       ."') or '".$filterHeldSPID       ."' ='')
                and (HeldKPID       = convert(int,'".$filterHeldKPID       ."') or '".$filterHeldKPID       ."' ='')
                and (HeldProcDBID   = convert(int,'".$filterHeldProcDBID   ."') or '".$filterHeldProcDBID   ."' ='')
                and (HeldProcedureID= convert(int,'".$filterHeldProcedureID."') or '".$filterHeldProcedureID."' ='')
                and (HeldBatchID    = convert(int,'".$filterHeldBatchID    ."') or '".$filterHeldBatchID    ."' ='')
                and (HeldContextID  = convert(int,'".$filterHeldContextID  ."') or '".$filterHeldContextID  ."' ='')
                and (HeldLineNumber = convert(int,'".$filterHeldLineNumber ."') or '".$filterHeldLineNumber ."' ='')
                and (WaitFamilyID   = convert(int,'".$filterWaitFamilyID   ."') or '".$filterWaitFamilyID   ."' ='')
                and (WaitSPID       = convert(int,'".$filterWaitSPID       ."') or '".$filterWaitSPID       ."' ='')
                and (WaitKPID       = convert(int,'".$filterWaitKPID       ."') or '".$filterWaitKPID       ."' ='')
                and (WaitTime       = convert(int,'".$filterWaitTime       ."') or '".$filterWaitTime       ."' ='')
                and (ObjectName   like '".$filterObjectName  ."' or '".$filterObjectName."' ='')
                and (HeldUserName like '".$filterHeldUserName."' or '".$filterHeldUserName."' ='')
                and (HeldApplName like '".$filterHeldApplName."' or '".$filterHeldApplName."' ='')
                and (HeldTranName like '".$filterHeldTranName."' or '".$filterHeldTranName."' ='')
                and (HeldLockType like '".$filterHeldLockType."' or '".$filterHeldLockType."' ='')
                and (HeldCommand  like '".$filterHeldCommand ."' or '".$filterHeldCommand."' ='')
                and (WaitUserName like '".$filterWaitUserName."' or '".$filterWaitUserName."' ='')
                and (WaitLockType like '".$filterWaitLockType."' or '".$filterWaitLockType."' ='')
                and (HeldProcName like '".$filterHeldProcName."' or '".$filterHeldProcName."' ='')
		order by ResolveTime
		set rowcount 0";

  $query_name = "deadlocks_list";

?>
