<?php

	$param_list=array(
		'orderPrc',
		'rowcnt',
        'filterDeadlockID',
        'filterVictimKPID',
        'filterResolveTime',
        'filterInstanceID',
        'filterObjectID',
        'filterObjectDBID',
        'filterObjectDBName',
        'filterObjectName',
        'filterPageNumber',
        'filterRowNumber',
        'filterHeldApplName',
        'filterHeldUserName',
        'filterHeldTranName',
        'filterHeldLockType',
        'filterHeldCommand',
        'filterHeldInstanceID',
        'filterHeldStmtNumber',
        'filterHeldNumLocks',
        'filterHeldHostName',
        'filterHeldClientName',
        'filterHeldClientHostName',
        'filterHeldClientApplName',
        'filterHeldProcDBName',
        'filterHeldProcedureName',
        'filterHeldSourceCodeID',
        'filterHeldFamilyID',
        'filterHeldSPID',
        'filterHeldKPID',
        'filterHeldProcDBID',
        'filterHeldProcedureID',
        'filterHeldBatchID',
        'filterHeldContextID',
        'filterHeldLineNumber',
        'filterWaitApplName',
        'filterWaitUserName',
        'filterWaitTranName',
        'filterWaitLockType',
        'filterWaitCommand',
        'filterWaitStmtNumber',
        'filterWaitHostName',
        'filterWaitClientName',
        'filterWaitClientHostName',
        'filterWaitClientApplName',
        'filterWaitProcDBName',
        'filterWaitProcedureName',
        'filterWaitSourceCodeID',
        'filterWaitFamilyID',
        'filterWaitSPID',
        'filterWaitKPID',
        'filterWaitProcDBID',
        'filterWaitProcedureID',
        'filterWaitBatchID',
        'filterWaitContextID',
        'filterWaitLineNumber',
        'filterWaitTime'
	);
	foreach ($param_list as $param)
		@$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];
 

        if ( !isset($orderPrc) ) $orderPrc=$order_by;
        if ( !isset($rowcnt) ) $rowcnt=200;
        if ( !isset($filterDeadlockID        ) ) $filterDeadlockID         = "";
        if ( !isset($filterVictimKPID        ) ) $filterVictimKPID         = "";
        if ( !isset($filterResolveTime       ) ) $filterResolveTime        = "";
        if ( !isset($filterInstanceID        ) ) $filterInstanceID         = "";
        if ( !isset($filterObjectID          ) ) $filterObjectID           = "";
        if ( !isset($filterObjectDBID        ) ) $filterObjectDBID         = "";
        if ( !isset($filterObjectDBName      ) ) $filterObjectDBName       = "";
        if ( !isset($filterObjectName        ) ) $filterObjectName         = "";
        if ( !isset($filterPageNumber        ) ) $filterPageNumber         = "";
        if ( !isset($filterRowNumber         ) ) $filterRowNumber          = "";
        if ( !isset($filterHeldApplName      ) ) $filterHeldApplName       = "";
        if ( !isset($filterHeldUserName      ) ) $filterHeldUserName       = "";
        if ( !isset($filterHeldTranName      ) ) $filterHeldTranName       = "";
        if ( !isset($filterHeldLockType      ) ) $filterHeldLockType       = "";
        if ( !isset($filterHeldCommand       ) ) $filterHeldCommand        = "";
        if ( !isset($filterHeldInstanceID    ) ) $filterHeldInstanceID     = "";
        if ( !isset($filterHeldStmtNumber    ) ) $filterHeldStmtNumber     = "";
        if ( !isset($filterHeldNumLocks      ) ) $filterHeldNumLocks       = "";
        if ( !isset($filterHeldHostName      ) ) $filterHeldHostName       = "";
        if ( !isset($filterHeldClientName    ) ) $filterHeldClientName     = "";
        if ( !isset($filterHeldClientHostName) ) $filterHeldClientHostName = "";
        if ( !isset($filterHeldClientApplName) ) $filterHeldClientApplName = "";
        if ( !isset($filterHeldProcDBName    ) ) $filterHeldProcDBName     = "";
        if ( !isset($filterHeldProcedureName ) ) $filterHeldProcedureName  = "";
        if ( !isset($filterHeldSourceCodeID  ) ) $filterHeldSourceCodeID   = "";
        if ( !isset($filterHeldFamilyID      ) ) $filterHeldFamilyID       = "";
        if ( !isset($filterHeldSPID          ) ) $filterHeldSPID           = "";
        if ( !isset($filterHeldKPID          ) ) $filterHeldKPID           = "";
        if ( !isset($filterHeldProcDBID      ) ) $filterHeldProcDBID       = "";
        if ( !isset($filterHeldProcedureID   ) ) $filterHeldProcedureID    = "";
        if ( !isset($filterHeldBatchID       ) ) $filterHeldBatchID        = "";
        if ( !isset($filterHeldContextID     ) ) $filterHeldContextID      = "";
        if ( !isset($filterHeldLineNumber    ) ) $filterHeldLineNumber     = "";
        if ( !isset($filterWaitApplName      ) ) $filterWaitApplName       = "";
        if ( !isset($filterWaitUserName      ) ) $filterWaitUserName       = "";
        if ( !isset($filterWaitTranName      ) ) $filterWaitTranName       = "";
        if ( !isset($filterWaitLockType      ) ) $filterWaitLockType       = "";
        if ( !isset($filterWaitCommand       ) ) $filterWaitCommand        = "";
        if ( !isset($filterWaitStmtNumber    ) ) $filterWaitStmtNumber     = "";
        if ( !isset($filterWaitHostName      ) ) $filterWaitHostName       = "";
        if ( !isset($filterWaitClientName    ) ) $filterWaitClientName     = "";
        if ( !isset($filterWaitClientHostName) ) $filterWaitClientHostName = "";
        if ( !isset($filterWaitClientApplName) ) $filterWaitClientApplName = "";
        if ( !isset($filterWaitProcDBName    ) ) $filterWaitProcDBName     = "";
        if ( !isset($filterWaitProcedureName ) ) $filterWaitProcedureName  = "";
        if ( !isset($filterWaitSourceCodeID  ) ) $filterWaitSourceCodeID   = "";
        if ( !isset($filterWaitFamilyID      ) ) $filterWaitFamilyID       = "";
        if ( !isset($filterWaitSPID          ) ) $filterWaitSPID           = "";
        if ( !isset($filterWaitKPID          ) ) $filterWaitKPID           = "";
        if ( !isset($filterWaitProcDBID      ) ) $filterWaitProcDBID       = "";
        if ( !isset($filterWaitProcedureID   ) ) $filterWaitProcedureID    = "";
        if ( !isset($filterWaitBatchID       ) ) $filterWaitBatchID        = "";
        if ( !isset($filterWaitContextID     ) ) $filterWaitContextID      = "";
        if ( !isset($filterWaitLineNumber    ) ) $filterWaitLineNumber     = "";
        if ( !isset($filterWaitTime          ) ) $filterWaitTime           = "";

        include ("sql/sql_deadlocks_list_V157.php");
?>      
        
<script type="text/javascript">
setStatMainTableSize(0);
</script>
        
        
<center>
        
<div class="boxinmain" style="min-width:800px">
<div class="boxtop">
<div style="float:left; position: relative; top: 3px; left: 6px"><?php include './export/export-table.php' ?></div>
<div class="title"><?php echo  $Title ?></div>
<a   href="http://github.com/asebox/asebox?title=AseRep_DeadLocks" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="Deadlocks help" TITLE="Deadlocks help"  /> </a>
</div>

<div class="boxcontent">

<div class="boxbtns" >
<table align="left" cellspacing="2px" ><tr>
<td>Max rows (0 = unlimited) :</td>
<td>
	<input type="text" name="rowcnt" value="<?php if( isset($rowcnt) ){ echo $rowcnt ; } ?>">
</td>
<td>
	<img src="images/button_sideLt.gif"  class="btn" height="20px" >
    <INPUT style="height:20px; " class="btn" type="submit" value="Refresh" name="RefreshStmt" >
    <img src="images/button_sideRt.gif"  class="btn" height="20px">
</td>
</tr></table>
</div>



<div class="statMainTable">
        
        

<table cellspacing=2 cellpadding=4>
    <tr> 
      <td class="statTabletitle" > DeadlockID         </td>
      <td class="statTabletitle" > VictimKPID         </td>
      <td class="statTabletitle" > ResolveTime        </td>
      <td class="statTabletitle" > InstanceID         </td>
      <td class="statTabletitle" > ObjectID           </td>
      <td class="statTabletitle" > ObjectDBID         </td>
      <td class="statTabletitle" > ObjectDBName       </td>
      <td class="statTabletitle" > ObjectName         </td>
      <td class="statTabletitle" > PageNumber         </td>
      <td class="statTabletitle" > RowNumber          </td>
      <td class="statTabletitle" > HeldApplName       </td>
      <td class="statTabletitle" > HeldUserName       </td>
      <td class="statTabletitle" > HeldTranName       </td>
      <td class="statTabletitle" > HeldLockType       </td>
      <td class="statTabletitle" > HeldCommand        </td>
      <td class="statTabletitle" > HeldInstanceID     </td>
      <td class="statTabletitle" > HeldStmtNumber     </td>
      <td class="statTabletitle" > HeldNumLocks       </td>
      <td class="statTabletitle" > HeldHostName       </td>
      <td class="statTabletitle" > HeldClientName     </td>
      <td class="statTabletitle" > HeldClientHostName </td>
      <td class="statTabletitle" > HeldClientApplName </td>
      <td class="statTabletitle" > HeldProcDBName     </td>
      <td class="statTabletitle" > HeldProcedureName  </td>
      <td class="statTabletitle" > HeldSourceCodeID   </td>
      <td class="statTabletitle" > HeldFamilyID       </td>
      <td class="statTabletitle" > HeldSPID           </td>
      <td class="statTabletitle" > HeldKPID           </td>
      <td class="statTabletitle" > HeldProcDBID       </td>
      <td class="statTabletitle" > HeldProcedureID    </td>
      <td class="statTabletitle" > HeldBatchID        </td>
      <td class="statTabletitle" > HeldContextID      </td>
      <td class="statTabletitle" > HeldLineNumber     </td>
      <td class="statTabletitle" > WaitApplName       </td>
      <td class="statTabletitle" > WaitUserName       </td>
      <td class="statTabletitle" > WaitTranName       </td>
      <td class="statTabletitle" > WaitLockType       </td>
      <td class="statTabletitle" > WaitCommand        </td>
      <td class="statTabletitle" > WaitStmtNumber     </td>
      <td class="statTabletitle" > WaitHostName       </td>
      <td class="statTabletitle" > WaitClientName     </td>
      <td class="statTabletitle" > WaitClientHostName </td>
      <td class="statTabletitle" > WaitClientApplName </td>
      <td class="statTabletitle" > WaitProcDBName     </td>
      <td class="statTabletitle" > WaitProcedureName  </td>
      <td class="statTabletitle" > WaitSourceCodeID   </td>
      <td class="statTabletitle" > WaitFamilyID       </td>
      <td class="statTabletitle" > WaitSPID           </td>
      <td class="statTabletitle" > WaitKPID           </td>
      <td class="statTabletitle" > WaitProcDBID       </td>
      <td class="statTabletitle" > WaitProcedureID    </td>
      <td class="statTabletitle" > WaitBatchID        </td>
      <td class="statTabletitle" > WaitContextID      </td>
      <td class="statTabletitle" > WaitLineNumber     </td>
      <td class="statTabletitle" > WaitTime           </td>
    </tr>
    
    <tr class=statTableTitle> 
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterDeadlockID" value="<?php if(isset($filterDeadlockID        )) { echo $filterDeadlockID        ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterVictimKPID" value="<?php if(isset($filterVictimKPID        )) { echo $filterVictimKPID        ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterResolveTime" value="<?php if(isset($filterResolveTime       )) { echo $filterResolveTime       ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterInstanceID" value="<?php if(isset($filterInstanceID        )) { echo $filterInstanceID        ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterObjectID" value="<?php if(isset($filterObjectID          )) { echo $filterObjectID          ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterObjectDBID" value="<?php if(isset($filterObjectDBID        )) { echo $filterObjectDBID        ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterObjectDBName" value="<?php if(isset($filterObjectDBName      )) { echo $filterObjectDBName      ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterObjectName" value="<?php if(isset($filterObjectName        )) { echo $filterObjectName        ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterPageNumber" value="<?php if(isset($filterPageNumber        )) { echo $filterPageNumber        ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterRowNumber" value="<?php if(isset($filterRowNumber         )) { echo $filterRowNumber         ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterHeldApplName" value="<?php if(isset($filterHeldApplName      )) { echo $filterHeldApplName      ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterHeldUserName" value="<?php if(isset($filterHeldUserName      )) { echo $filterHeldUserName      ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterHeldTranName" value="<?php if(isset($filterHeldTranName      )) { echo $filterHeldTranName      ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterHeldLockType" value="<?php if(isset($filterHeldLockType      )) { echo $filterHeldLockType      ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterHeldCommand" value="<?php if(isset($filterHeldCommand       )) { echo $filterHeldCommand       ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterHeldInstanceID" value="<?php if(isset($filterHeldInstanceID    )) { echo $filterHeldInstanceID    ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterHeldStmtNumber" value="<?php if(isset($filterHeldStmtNumber    )) { echo $filterHeldStmtNumber    ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterHeldNumLocks" value="<?php if(isset($filterHeldNumLocks      )) { echo $filterHeldNumLocks      ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterHeldHostName" value="<?php if(isset($filterHeldHostName      )) { echo $filterHeldHostName      ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterHeldClientName" value="<?php if(isset($filterHeldClientName    )) { echo $filterHeldClientName    ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterHeldClientHostName" value="<?php if(isset($filterHeldClientHostName)) { echo $filterHeldClientHostName; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterHeldClientApplName" value="<?php if(isset($filterHeldClientApplName)) { echo $filterHeldClientApplName; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterHeldProcDBName" value="<?php if(isset($filterHeldProcDBName    )) { echo $filterHeldProcDBName    ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterHeldProcedureName" value="<?php if(isset($filterHeldProcedureName )) { echo $filterHeldProcedureName ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterHeldSourceCodeID" value="<?php if(isset($filterHeldSourceCodeID  )) { echo $filterHeldSourceCodeID  ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterHeldFamilyID" value="<?php if(isset($filterHeldFamilyID      )) { echo $filterHeldFamilyID      ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterHeldSPID" value="<?php if(isset($filterHeldSPID          )) { echo $filterHeldSPID          ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterHeldKPID" value="<?php if(isset($filterHeldKPID          )) { echo $filterHeldKPID          ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterHeldProcDBID" value="<?php if(isset($filterHeldProcDBID      )) { echo $filterHeldProcDBID      ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterHeldProcedureID" value="<?php if(isset($filterHeldProcedureID   )) { echo $filterHeldProcedureID   ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterHeldBatchID" value="<?php if(isset($filterHeldBatchID       )) { echo $filterHeldBatchID       ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterHeldContextID" value="<?php if(isset($filterHeldContextID     )) { echo $filterHeldContextID     ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterHeldLineNumber" value="<?php if(isset($filterHeldLineNumber    )) { echo $filterHeldLineNumber    ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterWaitApplName" value="<?php if(isset($filterWaitApplName      )) { echo $filterWaitApplName      ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterWaitUserName" value="<?php if(isset($filterWaitUserName      )) { echo $filterWaitUserName      ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterWaitTranName" value="<?php if(isset($filterWaitTranName      )) { echo $filterWaitTranName      ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterWaitLockType" value="<?php if(isset($filterWaitLockType      )) { echo $filterWaitLockType      ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterWaitCommand" value="<?php if(isset($filterWaitCommand       )) { echo $filterWaitCommand       ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterWaitStmtNumber" value="<?php if(isset($filterWaitStmtNumber    )) { echo $filterWaitStmtNumber    ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterWaitHostName" value="<?php if(isset($filterWaitHostName      )) { echo $filterWaitHostName      ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterWaitClientName" value="<?php if(isset($filterWaitClientName    )) { echo $filterWaitClientName    ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterWaitClientHostName" value="<?php if(isset($filterWaitClientHostName)) { echo $filterWaitClientHostName; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterWaitClientApplName" value="<?php if(isset($filterWaitClientApplName)) { echo $filterWaitClientApplName; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterWaitProcDBName" value="<?php if(isset($filterWaitProcDBName    )) { echo $filterWaitProcDBName    ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterWaitProcedureName" value="<?php if(isset($filterWaitProcedureName )) { echo $filterWaitProcedureName ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterWaitSourceCodeID" value="<?php if(isset($filterWaitSourceCodeID  )) { echo $filterWaitSourceCodeID  ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterWaitFamilyID" value="<?php if(isset($filterWaitFamilyID      )) { echo $filterWaitFamilyID      ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterWaitSPID" value="<?php if(isset($filterWaitSPID          )) { echo $filterWaitSPID          ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterWaitKPID" value="<?php if(isset($filterWaitKPID          )) { echo $filterWaitKPID          ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterWaitProcDBID" value="<?php if(isset($filterWaitProcDBID      )) { echo $filterWaitProcDBID      ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterWaitProcedureID" value="<?php if(isset($filterWaitProcedureID   )) { echo $filterWaitProcedureID   ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterWaitBatchID" value="<?php if(isset($filterWaitBatchID       )) { echo $filterWaitBatchID       ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterWaitContextID" value="<?php if(isset($filterWaitContextID     )) { echo $filterWaitContextID     ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterWaitLineNumber" value="<?php if(isset($filterWaitLineNumber    )) { echo $filterWaitLineNumber    ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterWaitTime" value="<?php if(isset($filterWaitTime          )) { echo $filterWaitTime          ; } ?>" > </td>
    </tr>
    
    
    <?php
	$result = sybase_query($query,$pid) ;
	$rw=0;
	$cpt=1;
        if ($result != FALSE ) {   
          while( $row = sybase_fetch_array($result))
          {
			$rw++;
			if($cpt==0)
			     $parite="impair";
			else
			     $parite="pair";
			?>
			<tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';"  >
			<?php
			$cpt=1-$cpt;
    ?>
      <td nowrap class="statTable" > <?php echo $row["DeadlockID"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["VictimKPID"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["ResolveTime"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["InstanceID"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["ObjectID"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["ObjectDBID"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["ObjectDBName"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["ObjectName"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["PageNumber"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["RowNumber"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["HeldApplName"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["HeldUserName"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["HeldTranName"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["HeldLockType"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["HeldCommand"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["HeldInstanceID"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["HeldStmtNumber"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["HeldNumLocks"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["HeldHostName"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["HeldClientName"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["HeldClientHostName"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["HeldClientApplName"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["HeldProcDBName"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["HeldProcedureName"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["HeldSourceCodeID"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["HeldFamilyID"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["HeldSPID"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["HeldKPID"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["HeldProcDBID"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["HeldProcedureID"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["HeldBatchID"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["HeldContextID"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["HeldLineNumber"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["WaitApplName"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["WaitUserName"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["WaitTranName"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["WaitLockType"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["WaitCommand"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["WaitStmtNumber"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["WaitHostName"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["WaitClientName"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["WaitClientHostName"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["WaitClientApplName"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["WaitProcDBName"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["WaitProcedureName"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["WaitSourceCodeID"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["WaitFamilyID"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["WaitSPID"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["WaitKPID"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["WaitProcDBID"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["WaitProcedureID"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["WaitBatchID"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["WaitContextID"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["WaitLineNumber"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["WaitTime"] ?> </td>
      
     </tr> 
    <?php
          } // end while
	} // end if $result...
	if ($rw == 0 )  {
    ?>
    <tr>
       <td colspan="19" align="center" > <font  STYLE="font-weight: 900"> No deadlock   </font> </td>
    </tr>
    <?php
        } // end if $result
    ?>
    

</table>

</DIV>
</DIV>
</DIV>

</center>
