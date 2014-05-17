<?php
<<<<<<< HEAD

=======
>>>>>>> 3.1.0
	$param_list=array(
		'orderPrc',
		'rowcnt',
		'filterDeadlockID',
		'filterVictimKPID',
		'filterResolveTime',
		'filterObjectDBID',
		'filterPageNumber',
		'filterRowNumber',
		'filterHeldFamilyID',
		'filterHeldSPID',
		'filterHeldKPID',
		'filterHeldProcDBID',
		'filterHeldProcedureID',
		'filterHeldProcName',
		'filterHeldBatchID',
		'filterHeldContextID',
		'filterHeldLineNumber',
		'filterWaitFamilyID',
		'filterWaitSPID',
		'filterWaitKPID',
		'filterWaitTime',
		'filterObjectName',
		'filterHeldUserName',
		'filterHeldApplName',
		'filterHeldTranName',
		'filterHeldLockType',
		'filterHeldCommand',
		'filterWaitUserName',
		'filterWaitLockType'
	);
	foreach ($param_list as $param)
<<<<<<< HEAD
		@$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];
 
=======
		@$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param]; 
>>>>>>> 3.1.0

        if ( !isset($orderPrc) ) $orderPrc=$order_by;
        if ( !isset($rowcnt) ) $rowcnt=200;
        if ( !isset($filterDeadlockID       ) ) $filterDeadlockID      ="";
        if ( !isset($filterVictimKPID       ) ) $filterVictimKPID      ="";
        if ( !isset($filterResolveTime      ) ) $filterResolveTime     ="";
        if ( !isset($filterObjectDBID       ) ) $filterObjectDBID      ="";
        if ( !isset($filterPageNumber       ) ) $filterPageNumber      ="";
        if ( !isset($filterRowNumber        ) ) $filterRowNumber       ="";
        if ( !isset($filterHeldFamilyID     ) ) $filterHeldFamilyID    ="";
        if ( !isset($filterHeldSPID         ) ) $filterHeldSPID        ="";
        if ( !isset($filterHeldKPID         ) ) $filterHeldKPID        ="";
        if ( !isset($filterHeldProcDBID     ) ) $filterHeldProcDBID    ="";
        if ( !isset($filterHeldProcedureID  ) ) $filterHeldProcedureID ="";
        if ( !isset($filterHeldProcName     ) ) $filterHeldProcName    ="";
        if ( !isset($filterHeldBatchID      ) ) $filterHeldBatchID     ="";
        if ( !isset($filterHeldContextID    ) ) $filterHeldContextID   ="";
        if ( !isset($filterHeldLineNumber   ) ) $filterHeldLineNumber  ="";
        if ( !isset($filterWaitFamilyID     ) ) $filterWaitFamilyID    ="";
        if ( !isset($filterWaitSPID         ) ) $filterWaitSPID        ="";
        if ( !isset($filterWaitKPID         ) ) $filterWaitKPID        ="";
        if ( !isset($filterWaitTime         ) ) $filterWaitTime        ="";
        if ( !isset($filterObjectName       ) ) $filterObjectName      ="";
        if ( !isset($filterHeldUserName     ) ) $filterHeldUserName    ="";
        if ( !isset($filterHeldApplName     ) ) $filterHeldApplName    ="";
        if ( !isset($filterHeldTranName     ) ) $filterHeldTranName    ="";
        if ( !isset($filterHeldLockType     ) ) $filterHeldLockType    ="";
        if ( !isset($filterHeldCommand      ) ) $filterHeldCommand     ="";
        if ( !isset($filterWaitUserName     ) ) $filterWaitUserName    ="";
        if ( !isset($filterWaitLockType     ) ) $filterWaitLockType    ="";

        include ("sql/sql_deadlocks_list_preV157.php");
?>      
        
<script type="text/javascript">
setStatMainTableSize(0);
</script>
        
        
<center>
        
<div class="boxinmain" style="min-width:800px">
<div class="boxtop">
<div style="float:left; position: relative; top: 3px; left: 6px"><?php include './export/export-table.php' ?></div>
<<<<<<< HEAD
<div class="title"><?php echo  $Title ?></div>
<a   href="http://github.com/asebox/asebox?title=AseRep_DeadLocks" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="Deadlocks help" TITLE="Deadlocks help"  /> </a>
=======
<div class="title"><?php echo $Title ?></div>
<a   href="http://github.com/asebox/asebox/ASE-Deadlocks" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="Deadlocks help" TITLE="Deadlocks help"  /> </a>
>>>>>>> 3.1.0
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


<<<<<<< HEAD

<div class="statMainTable">
        
        

<table cellspacing=2 cellpadding=4>
    <tr> 
      <td class="statTabletitle" > DeadlockID     </td>
      <td class="statTabletitle" > VictimKPID     </td>
      <td class="statTabletitle" > ResolveTime    </td>
      <td class="statTabletitle" > ObjectDBID     </td>
      <td class="statTabletitle" > ObjectName     </td>
      <td class="statTabletitle" > PageNumber     </td>
      <td class="statTabletitle" > RowNumber      </td>
=======
<div class="statMainTable">
        
<table cellspacing=2 cellpadding=4>
    <tr> 
      <td class="statTabletitle" > ID             </td>
      <td class="statTabletitle" > VictimKPID     </td>
      <td class="statTabletitle" > ResolveTime    </td>
      <td class="statTabletitle" > DBID           </td>
      <td class="statTabletitle" > Object         </td>
      <td class="statTabletitle" > Page           </td>
      <td class="statTabletitle" > Row            </td>
>>>>>>> 3.1.0
      <td class="statTabletitle" > HeldFamilyID   </td>
      <td class="statTabletitle" > HeldSPID       </td>
      <td class="statTabletitle" > HeldKPID       </td>
      <td class="statTabletitle" > HeldUserName   </td>
      <td class="statTabletitle" > HeldProcDBID   </td>
      <td class="statTabletitle" > HeldProcedureID</td>
      <td class="statTabletitle" > HeldProcName   </td>
      <td class="statTabletitle" > HeldBatchID    </td>
      <td class="statTabletitle" > HeldContextID  </td>
      <td class="statTabletitle" > HeldLineNumber </td>
      <td class="statTabletitle" > HeldApplName   </td>
      <td class="statTabletitle" > HeldTranName   </td>
      <td class="statTabletitle" > HeldLockType   </td>
      <td class="statTabletitle" > HeldCommand    </td>
      <td class="statTabletitle" > WaitFamilyID   </td>
      <td class="statTabletitle" > WaitSPID       </td>
      <td class="statTabletitle" > WaitKPID       </td>
      <td class="statTabletitle" > WaitUserName   </td>
      <td class="statTabletitle" > WaitLockType   </td>
      <td class="statTabletitle" > WaitTime       </td>
    </tr>
    
    <tr class=statTableTitle> 
<<<<<<< HEAD
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterDeadlockID" value="<?php if(isset($filterDeadlockID          ) ) { echo $filterDeadlockID     ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterVictimKPID" value="<?php if(isset($filterVictimKPID          ) ) { echo $filterVictimKPID     ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterResolveTime" value="<?php if(isset($filterResolveTime        ) ) { echo $filterResolveTime    ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterObjectDBID" value="<?php if(isset($filterObjectDBID          ) ) { echo $filterObjectDBID     ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterObjectName" value="<?php if(isset($filterObjectName          ) ) { echo $filterObjectName     ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterPageNumber" value="<?php if(isset($filterPageNumber          ) ) { echo $filterPageNumber     ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterRowNumber" value="<?php if(isset($filterRowNumber            ) ) { echo $filterRowNumber      ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterHeldFamilyID" value="<?php if(isset($filterHeldFamilyID      ) ) { echo $filterHeldFamilyID   ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterHeldSPID" value="<?php if(isset($filterHeldSPID              ) ) { echo $filterHeldSPID       ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterHeldKPID" value="<?php if(isset($filterHeldKPID              ) ) { echo $filterHeldKPID       ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterHeldUserName" value="<?php if(isset($filterHeldUserName      ) ) { echo $filterHeldUserName   ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterHeldProcDBID" value="<?php if(isset($filterHeldProcDBID      ) ) { echo $filterHeldProcDBID   ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterHeldProcedureID" value="<?php if(isset($filterHeldProcedureID) ) { echo $filterHeldProcedureID; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterHeldProcName" value="<?php if(isset($filterHeldProcName      ) ) { echo $filterHeldProcName   ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterHeldBatchID" value="<?php if(isset($filterHeldBatchID        ) ) { echo $filterHeldBatchID    ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterHeldContextID" value="<?php if(isset($filterHeldContextID    ) ) { echo $filterHeldContextID  ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterHeldLineNumber" value="<?php if(isset($filterHeldLineNumber  ) ) { echo $filterHeldLineNumber ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterHeldApplName" value="<?php if(isset($filterHeldApplName      ) ) { echo $filterHeldApplName   ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterHeldTranName" value="<?php if(isset($filterHeldTranName      ) ) { echo $filterHeldTranName   ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterHeldLockType" value="<?php if(isset($filterHeldLockType      ) ) { echo $filterHeldLockType   ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterHeldCommand" value="<?php if(isset($filterHeldCommand        ) ) { echo $filterHeldCommand    ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterWaitFamilyID" value="<?php if(isset($filterWaitFamilyID      ) ) { echo $filterWaitFamilyID   ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterWaitSPID" value="<?php if(isset($filterWaitSPID              ) ) { echo $filterWaitSPID       ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterWaitKPID" value="<?php if(isset($filterWaitKPID              ) ) { echo $filterWaitKPID       ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterWaitUserName" value="<?php if(isset($filterWaitUserName      ) ) { echo $filterWaitUserName   ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterWaitLockType" value="<?php if(isset($filterWaitLockType      ) ) { echo $filterWaitLockType   ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterWaitTime" value="<?php if(isset($filterWaitTime              ) ) { echo $filterWaitTime       ; } ?>" > </td>
=======
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="4"  Name="filterDeadlockID" value="<?php if(isset($filterDeadlockID          ) ) { echo $filterDeadlockID     ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="10" Name="filterVictimKPID" value="<?php if(isset($filterVictimKPID          ) ) { echo $filterVictimKPID     ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text           Name="filterResolveTime" value="<?php if(isset($filterResolveTime        ) ) { echo $filterResolveTime    ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="4"  Name="filterObjectDBID" value="<?php if(isset($filterObjectDBID          ) ) { echo $filterObjectDBID     ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text           Name="filterObjectName" value="<?php if(isset($filterObjectName          ) ) { echo $filterObjectName     ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="10" Name="filterPageNumber" value="<?php if(isset($filterPageNumber          ) ) { echo $filterPageNumber     ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="6"  Name="filterRowNumber" value="<?php if(isset($filterRowNumber            ) ) { echo $filterRowNumber      ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="10" Name="filterHeldFamilyID" value="<?php if(isset($filterHeldFamilyID      ) ) { echo $filterHeldFamilyID   ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="10" Name="filterHeldSPID" value="<?php if(isset($filterHeldSPID              ) ) { echo $filterHeldSPID       ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="10" Name="filterHeldKPID" value="<?php if(isset($filterHeldKPID              ) ) { echo $filterHeldKPID       ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="10" Name="filterHeldUserName" value="<?php if(isset($filterHeldUserName      ) ) { echo $filterHeldUserName   ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="10" Name="filterHeldProcDBID" value="<?php if(isset($filterHeldProcDBID      ) ) { echo $filterHeldProcDBID   ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="10" Name="filterHeldProcedureID" value="<?php if(isset($filterHeldProcedureID) ) { echo $filterHeldProcedureID; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text           Name="filterHeldProcName" value="<?php if(isset($filterHeldProcName      ) ) { echo $filterHeldProcName   ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="10" Name="filterHeldBatchID" value="<?php if(isset($filterHeldBatchID        ) ) { echo $filterHeldBatchID    ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="10" Name="filterHeldContextID" value="<?php if(isset($filterHeldContextID    ) ) { echo $filterHeldContextID  ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="10" Name="filterHeldLineNumber" value="<?php if(isset($filterHeldLineNumber  ) ) { echo $filterHeldLineNumber ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text           Name="filterHeldApplName" value="<?php if(isset($filterHeldApplName      ) ) { echo $filterHeldApplName   ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text           Name="filterHeldTranName" value="<?php if(isset($filterHeldTranName      ) ) { echo $filterHeldTranName   ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="10" Name="filterHeldLockType" value="<?php if(isset($filterHeldLockType      ) ) { echo $filterHeldLockType   ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="6"  Name="filterHeldCommand" value="<?php if(isset($filterHeldCommand        ) ) { echo $filterHeldCommand    ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="10" Name="filterWaitFamilyID" value="<?php if(isset($filterWaitFamilyID      ) ) { echo $filterWaitFamilyID   ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="10" Name="filterWaitSPID" value="<?php if(isset($filterWaitSPID              ) ) { echo $filterWaitSPID       ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="10" Name="filterWaitKPID" value="<?php if(isset($filterWaitKPID              ) ) { echo $filterWaitKPID       ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="10" Name="filterWaitUserName" value="<?php if(isset($filterWaitUserName      ) ) { echo $filterWaitUserName   ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="10" Name="filterWaitLockType" value="<?php if(isset($filterWaitLockType      ) ) { echo $filterWaitLockType   ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="10" Name="filterWaitTime" value="<?php if(isset($filterWaitTime              ) ) { echo $filterWaitTime       ; } ?>" > </td>
>>>>>>> 3.1.0
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
      <td nowrap class="statTable" > <?php echo $row["ObjectDBID"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["ObjectName"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["PageNumber"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["RowNumber"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["HeldFamilyID"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["HeldSPID"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["HeldKPID"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["HeldUserName"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["HeldProcDBID"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["HeldProcedureID"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["HeldProcName"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["HeldBatchID"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["HeldContextID"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["HeldLineNumber"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["HeldApplName"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["HeldTranName"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["HeldLockType"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["HeldCommand"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["WaitFamilyID"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["WaitSPID"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["WaitKPID"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["WaitUserName"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["WaitLockType"] ?> </td>
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
<<<<<<< HEAD
    ?>
    
=======
    ?>    
>>>>>>> 3.1.0

</table>

</DIV>
</DIV>
</DIV>

</center>
