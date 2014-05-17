<?php
$query = "set rowcount ".$rowcnt."
          select *
		from ".$ServerName."_DeadLock
	        where ResolveTime >='".$StartTimestamp."'        
	        and ResolveTime <'".$EndTimestamp."'        

                and (DeadlockID      = convert(int,'".$filterDeadlockID        ."') or '".$filterDeadlockID      ."' = '')          
                and (VictimKPID      = convert(int,'".$filterVictimKPID        ."') or '".$filterVictimKPID      ."' = '')          
                and (ResolveTime     = convert(datetime,'".$filterResolveTime  ."') or '".$filterResolveTime     ."' = '')          
                and (ObjectDBID      = convert(int,'".$filterObjectDBID        ."') or '".$filterObjectDBID      ."' = '')          
                and (InstanceID      = convert(int,'".$filterInstanceID        ."') or '".$filterInstanceID      ."' = '')          
                and (ObjectID        = convert(int,'".$filterObjectID          ."') or '".$filterObjectID        ."' = '')          
                and (PageNumber      = convert(int,'".$filterPageNumber        ."') or '".$filterPageNumber      ."' = '')          
                and (RowNumber       = convert(int,'".$filterRowNumber         ."') or '".$filterRowNumber       ."' = '')          
                and (HeldInstanceID  = convert(int,'".$filterHeldInstanceID    ."') or '".$filterHeldInstanceID  ."' = '')          
                and (HeldStmtNumber  = convert(int,'".$filterHeldStmtNumber    ."') or '".$filterHeldStmtNumber  ."' = '')          
                and (HeldNumLocks    = convert(int,'".$filterHeldNumLocks      ."') or '".$filterHeldNumLocks    ."' = '')          
                and (HeldFamilyID    = convert(int,'".$filterHeldFamilyID      ."') or '".$filterHeldFamilyID    ."' = '')          
                and (HeldSPID        = convert(int,'".$filterHeldSPID          ."') or '".$filterHeldSPID        ."' = '')          
                and (HeldKPID        = convert(int,'".$filterHeldKPID          ."') or '".$filterHeldKPID        ."' = '')          
                and (HeldProcDBID    = convert(int,'".$filterHeldProcDBID      ."') or '".$filterHeldProcDBID    ."' = '')          
                and (HeldProcedureID = convert(int,'".$filterHeldProcedureID   ."') or '".$filterHeldProcedureID ."' = '')          
                and (HeldBatchID     = convert(int,'".$filterHeldBatchID       ."') or '".$filterHeldBatchID     ."' = '')          
                and (HeldContextID   = convert(int,'".$filterHeldContextID     ."') or '".$filterHeldContextID   ."' = '')          
                and (HeldLineNumber  = convert(int,'".$filterHeldLineNumber    ."') or '".$filterHeldLineNumber  ."' = '')          
                and (WaitStmtNumber  = convert(int,'".$filterWaitStmtNumber    ."') or '".$filterWaitStmtNumber  ."' = '')          
                and (WaitFamilyID    = convert(int,'".$filterWaitFamilyID      ."') or '".$filterWaitFamilyID    ."' = '')          
                and (WaitSPID        = convert(int,'".$filterWaitSPID          ."') or '".$filterWaitSPID        ."' = '')          
                and (WaitKPID        = convert(int,'".$filterWaitKPID          ."') or '".$filterWaitKPID        ."' = '')          
                and (WaitProcDBID    = convert(int,'".$filterWaitProcDBID      ."') or '".$filterWaitProcDBID    ."' = '')          
                and (WaitProcedureID = convert(int,'".$filterWaitProcedureID   ."') or '".$filterWaitProcedureID ."' = '')                                                                                                                                                       
                and (WaitBatchID     = convert(int,'".$filterWaitBatchID       ."') or '".$filterWaitBatchID     ."' = '')
                and (WaitContextID   = convert(int,'".$filterWaitContextID     ."') or '".$filterWaitContextID   ."' = '')
                and (WaitLineNumber  = convert(int,'".$filterWaitLineNumber    ."') or '".$filterWaitLineNumber  ."' = '')
                and (WaitTime        = convert(int,'".$filterWaitTime          ."') or '".$filterWaitTime        ."' = '')



                and (ObjectName         like '".$filterObjectName         ."' or '".$filterObjectName        ."' ='')
                and (ObjectDBName       like '".$filterObjectDBName       ."' or '".$filterObjectDBName      ."' ='')
                and (HeldApplName       like '".$filterHeldApplName       ."' or '".$filterHeldApplName      ."' ='')
                and (HeldUserName       like '".$filterHeldUserName       ."' or '".$filterHeldUserName      ."' ='')
                and (HeldTranName       like '".$filterHeldTranName       ."' or '".$filterHeldTranName      ."' ='')
                and (HeldLockType       like '".$filterHeldLockType       ."' or '".$filterHeldLockType      ."' ='')
                and (HeldCommand        like '".$filterHeldCommand        ."' or '".$filterHeldCommand       ."' ='')
                and (HeldHostName       like '".$filterHeldHostName       ."' or '".$filterHeldHostName      ."' ='')
                and (HeldClientName     like '".$filterHeldClientName     ."' or '".$filterHeldClientName    ."' ='')
                and (HeldClientHostName like '".$filterHeldClientHostName ."' or '".$filterHeldClientHostName."' ='')
                and (HeldClientApplName like '".$filterHeldClientApplName ."' or '".$filterHeldClientApplName."' ='')
                and (HeldProcDBName     like '".$filterHeldProcDBName     ."' or '".$filterHeldProcDBName    ."' ='')
                and (HeldProcedureName  like '".$filterHeldProcedureName  ."' or '".$filterHeldProcedureName ."' ='')
                and (WaitApplName       like '".$filterWaitApplName       ."' or '".$filterWaitApplName      ."' ='')
                and (WaitUserName       like '".$filterWaitUserName       ."' or '".$filterWaitUserName      ."' ='')
                and (WaitTranName       like '".$filterWaitTranName       ."' or '".$filterWaitTranName      ."' ='')
                and (WaitLockType       like '".$filterWaitLockType       ."' or '".$filterWaitLockType      ."' ='')
                and (WaitCommand        like '".$filterWaitCommand        ."' or '".$filterWaitCommand       ."' ='')
                and (WaitHostName       like '".$filterWaitHostName       ."' or '".$filterWaitHostName      ."' ='')
                and (WaitClientName     like '".$filterWaitClientName     ."' or '".$filterWaitClientName    ."' ='')
                and (WaitClientHostName like '".$filterWaitClientHostName ."' or '".$filterWaitClientHostName."' ='')
                and (WaitClientApplName like '".$filterWaitClientApplName ."' or '".$filterWaitClientApplName."' ='')
                and (WaitProcDBName     like '".$filterWaitProcDBName     ."' or '".$filterWaitProcDBName    ."' ='')
                and (WaitProcedureName  like '".$filterWaitProcedureName  ."' or '".$filterWaitProcedureName ."' ='')
                and (HeldSourceCodeID   like '".$filterHeldSourceCodeID   ."' or '".$filterHeldSourceCodeID  ."' ='')
                and (WaitSourceCodeID   like '".$filterWaitSourceCodeID   ."' or '".$filterWaitSourceCodeID  ."' ='')

		order by ResolveTime
		set rowcount 0";

  $query_name = "deadlocks_list";

?>
