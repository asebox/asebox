<?php
	$param_list=array(
		'orderPrc',
		'rowcnt',
		'filterSPID',
		'filterLockID',
		'filterBlockedBy',
		'filterLckBase',
		'filterLckObjet',
		'filterUsr',
		'filterTrn',
		'filterPrg',
	);
	foreach ($param_list as $param)
		@$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];
 

        if ( !isset($orderPrc) ) $orderPrc=$order_by;
        if ( !isset($rowcnt) ) $rowcnt=200;
        if ( !isset($filterSPID ) ) $filterSPID ="";
        if ( !isset($filterLockID ) ) $filterLockID ="";
        if ( !isset($filterBlockedBy ) ) $filterBlockedBy ="";
        if ( !isset($filterLckBase ) ) $filterLckBase ="";
        if ( !isset($filterLckObjet ) ) $filterLckObjet ="";
        if ( !isset($filterUsr ) ) $filterUsr ="";
        if ( !isset($filterTrn ) ) $filterTrn ="";
        if ( !isset($filterPrg ) ) $filterPrg ="";
?>      
                
<center>
        
	<?php 
		// Prepare lock analysis

if ($ArchSrvType=="Adaptive Server Enterprise") {  
    // It is on ASE, isolate declare cursor step
		$result=sybase_query(
		               "declare c cursor for 
                        select BlockedBy,Timestamp,WT=max(WaitTime)
                        from ".$ServerName."_BlockedP 
                        where Timestamp >='".$StartTimestamp."'
                        and   Timestamp <='".$EndTimestamp."'
                        and BlockedBy   is not null
                        group by BlockedBy,Timestamp
                        order by BlockedBy,Timestamp",
                       $pid);

		if ($result==false){ 
			sybase_close($pid); 
			$pid=0;
			echo "<tr><td>Error</td></tr></table>";
			return(0);
		}
        $result = sybase_query("if object_id('#blk_max') is not null drop table #blk_max",$pid);
        $result = sybase_query("if object_id('#blk_max2') is not null drop table #blk_max2",$pid);
		$result=sybase_query(
                       "declare @savts datetime, @ts datetime, @savWT int, @WT int, @savBlockedBy int, @BlockedBy int
                        create table #blk_max (BlockedBy int, max_TS datetime, max_WaitTime int)
                        set nocount on
                        open c
                        fetch c into @savBlockedBy,@savts,@savWT
                        if @@sqlstatus = 0
                        begin 
                            while @@sqlstatus=0
                            begin
                               fetch c into @BlockedBy,@ts,@WT
                               if @BlockedBy != @savBlockedBy
                               begin
                                 -- max block found for a spid
                                 insert into #blk_max values (@savBlockedBy,@savts,@savWT)
                               end
                               else
                                 if @WT < @savWT or @WT < 5
                                 begin
                                   -- max block found for a spid
                                   insert into #blk_max values (@savBlockedBy,@savts,@savWT)
                                 end
                               select @savWT=@WT, @savts=@ts, @savBlockedBy=@BlockedBy
                            end
                            -- sav last row found
                            insert into #blk_max values (@savBlockedBy,@savts,@savWT)
                        end
                        close c
                        deallocate cursor c",
			           $pid);

		if ($result==false){ 
			sybase_close($pid); 
			$pid=0;
			echo "<tr><td>Error</td></tr></table>";
			return(0);
		}
}
else {
    // It is on IQ, don't isolate declare cursor step
                $result = sybase_query("if object_id('#blk_max') is not null drop table #blk_max",$pid);
                $result = sybase_query("if object_id('#blk_max2') is not null drop table #blk_max2",$pid);
                $query = "create table #blk_max (BlockedBy int, max_TS datetime, max_WaitTime int)";
                $result = sybase_query($query,$pid);
		$result=sybase_query(
			           "declare c cursor for 
                        select BlockedBy,Timestamp,WT=max(WaitTime)
                        from ".$ServerName."_BlockedP 
                        where Timestamp >='".$StartTimestamp."'
                        and   Timestamp <='".$EndTimestamp."'
                        and BlockedBy   is not null
                        group by BlockedBy,Timestamp
                        order by BlockedBy,Timestamp
                        
                        declare @savts datetime, @ts datetime, @savWT int, @WT int, @savBlockedBy int, @BlockedBy int
                        create table #blk_max (BlockedBy int, max_TS datetime, max_WaitTime int)
                        set nocount on
                        open c
                        fetch c into @savBlockedBy,@savts,@savWT
                        if @@sqlstatus = 0
                        begin 
                            while @@sqlstatus=0
                            begin
                               fetch c into @BlockedBy,@ts,@WT
                               if @BlockedBy != @savBlockedBy
                               begin
                                 -- max block found for a spid
                                 insert into #blk_max values (@savBlockedBy,@savts,@savWT)
                               end
                               else
                                 if @WT < @savWT or @WT < 5
                                 begin
                                   -- max block found for a spid
                                   insert into #blk_max values (@savBlockedBy,@savts,@savWT)
                                 end
                               select @savWT=@WT, @savts=@ts, @savBlockedBy=@BlockedBy
                            end
                            -- sav last row found
                            insert into #blk_max values (@savBlockedBy,@savts,@savWT)
                        end
                        close c
                        deallocate cursor c",
			          $pid);


}
        ?>


<script type="text/javascript">
setStatMainTableSize(0);
</script>

<center>


<div class="boxinmain" style="min-width:800px">
<div class="boxtop">
<div class="title"><?php echo  $Title ?></div>
<a   href="http://github.com/asebox/asebox/ASE-Locks" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="Locks help" TITLE="Locks help"  /> </a>
</div>

<div class="boxcontent">

<div class="boxbtns" >
<table align="left" cellspacing="2px" ><tr>
<td>Max rows (0 = unlimited) :</td>
<td>
	<input type="text" SIZE="8" name="rowcnt" value="<?php if( isset($rowcnt) ){ echo $rowcnt ; } ?>">
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
      <td class="statTabletitle" > Timestamp  </td>
      <td class="statTabletitle" > WaitTime   </td>
      <td class="statTabletitle" > SPID       </td>
      <td class="statTabletitle" > KPID       </td>
      <td class="statTabletitle" > DBID       </td>
      <td class="statTabletitle" > ParentSPID </td>
      <td class="statTabletitle" > LockID     </td>
      <td class="statTabletitle" > BlockedBy  </td>
      <td class="statTabletitle" > State      </td>
      <td class="statTabletitle" > Context    </td>
      <td class="statTabletitle" > ObjectID   </td>
      <td class="statTabletitle" > Database   </td>
      <td class="statTabletitle" > Object     </td>
      <td class="statTabletitle" > Page       </td>
      <td class="statTabletitle" > Row        </td>
      <td class="statTabletitle" > State      </td>
      <td class="statTabletitle" > LockType   </td>
      <td class="statTabletitle" > LockLevel  </td>
      <td class="statTabletitle" > User       </td>
      <td class="statTabletitle" > Trans      </td>
      <td class="statTabletitle" > Program    </td>
      <td class="statTabletitle" > Procedure  </td>
      <td class="statTabletitle" > Line       </td>
    </tr>
    
    <tr> 
      <td></td> 
      <td></td> 
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="4" NAME="filterSPID"  value="<?php if( isset($filterSPID) ){ echo $filterSPID ; } ?>" > </td>
      <td></td> 
      <td></td> 
      <td></td> 
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="5" NAME="filterLockID"  value="<?php if( isset($filterLockID) ){ echo $filterLockID ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="5" NAME="filterBlockedBy"  value="<?php if( isset($filterLockID) ){ echo $filterBlockedBy ; } ?>" > </td>
      <td></td> 
      <td></td> 
      <td></td> 
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="10" NAME="filterLckBase"  value="<?php if( isset($filterLckBase) ){ echo $filterLckBase ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterLckObjet"  value="<?php if( isset($filterLckObjet) ){ echo $filterLckObjet ; } ?>" > </td>
      <td></td> 
      <td></td> 
      <td></td> 
      <td></td> 
      <td></td> 
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="10" NAME="filterUsr"  value="<?php if( isset($filterUsr) ){ echo $filterUsr ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="12" NAME="filterTrn"  value="<?php if( isset($filterTrn) ){ echo $filterTrn ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="12" NAME="filterPrg"  value="<?php if( isset($filterPrg) ){ echo $filterPrg ; } ?>" > </td>

    </tr>    
    
    <?php
    // Filter blocking occurences if necessary
    $query = "select B.* into #blk_max2 
    from ".$ServerName."_BlockedP A , #blk_max B
    where A.Timestamp = B.max_TS
      and A.BlockedBy = B.BlockedBy
      and (A.SPID = convert(int,'".$filterSPID."') or '".$filterSPID."'='')
      and (A.LockID = convert(int,'".$filterLockID."') or '".$filterLockID."'='')
      and (A.BlockedBy = convert(int,'".$filterBlockedBy."') or '".$filterBlockedBy."'='')
      and (lckBase like '".$filterLckBase."' or '".$filterLckBase."'='')
      and (lckObjet like '".$filterLckObjet."' or '".$filterLckObjet."'='')
      and (Usr like '".$filterUsr."' or '".$filterUsr."'='')
      and (Trn like '".$filterTrn."' or '".$filterTrn."'='')
      and (Prg like '".$filterPrg."' or '".$filterPrg."'='')
    UNION
    select B.*
    from ".$ServerName."_BlockedP A , #blk_max B
    where A.Timestamp = B.max_TS
      and A.LockID = B.BlockedBy
      and (A.SPID = convert(int,'".$filterSPID."') or '".$filterSPID."'='')
      and (A.LockID = convert(int,'".$filterLockID."') or '".$filterLockID."'='')
      and (A.BlockedBy = convert(int,'".$filterBlockedBy."') or '".$filterBlockedBy."'='')
      and (lckBase like '".$filterLckBase."' or '".$filterLckBase."'='')
      and (lckObjet like '".$filterLckObjet."' or '".$filterLckObjet."'='')
      and (Usr like '".$filterUsr."' or '".$filterUsr."'='')
      and (Trn like '".$filterTrn."' or '".$filterTrn."'='')
      and (Prg like '".$filterPrg."' or '".$filterPrg."'='')
     
    ";
    sybase_query($query,$pid) ;
    
	$query="set rowcount ".$rowcnt. " 
select
     TS=convert(varchar,Timestamp,109)   ,
     maxBB=B.BlockedBy ,
     WaitTime    ,
     SPID        ,
     KPID        ,
     DBID        ,
     ParentSPID  ,
     LockID      ,
     A.BlockedBy   ,
     BlockedState,
     Context     ,
     ObjectID    ,
     lckBase     ,
     lckObjet    ,
     lckPage     ,
     lckRow      ,
     LockState   ,
     LockType    ,
     LockLevel   ,
     Usr         ,
     Trn         ,
     Prg         ,
     Prc         ,
     Line        
from ".$ServerName."_BlockedP A , #blk_max2 B
where A.Timestamp = B.max_TS
and A.BlockedBy = B.BlockedBy
union all
select
     TS=convert(varchar,Timestamp,109)   ,
     maxBB=B.BlockedBy ,
     WaitTime    ,
     SPID        ,
     KPID        ,
     DBID        ,
     ParentSPID  ,
     LockID      ,
     A.BlockedBy   ,
     BlockedState,
     Context     ,
     ObjectID    ,
     lckBase     ,
     lckObjet    ,
     lckPage     ,
     lckRow      ,
     LockState   ,
     LockType    ,
     LockLevel   ,
     Usr         ,
     Trn         ,
     Prg         ,
     Prc         ,
     Line        
from ".$ServerName."_BlockedP A , #blk_max2 B
where A.Timestamp = B.max_TS
and A.LockID = B.BlockedBy

order by TS, maxBB, BlockedState desc, SPID
set rowcount 0";
  //echo $query;	
	$result = sybase_query($query,$pid) ;
	$rw=0;
	$OldTS="";
	$OldBB=0;
	$parite="White";
        if ($result != FALSE ) {   
          while( $row = sybase_fetch_array($result))
          {
			$rw++;
			if ( ($row["TS"]!=$OldTS) || ($row["maxBB"]!=$OldBB) ) {
			    // new blocking situation
			    // display with other color
			    $OldTS=$row["TS"];
			    $OldBB=$row["maxBB"];
			    
			    // if ( $parite == "Green" ) 
			    //   $parite="White"; 
			    // else
			    //   $parite="Green";
			    
			    // insert an empty row
			    ?><tr><td colspan="23" style="background-color:#F0F0DD"/> </tr><?php
			}
			?>
			<tr class="statTable<?php echo $parite; ?>"   >
			<?php
    ?>
      <td nowrap class="statTable" > <?php echo $row["TS"]           ?> </td> 
      <td nowrap class="statTable" > <?php echo $row["WaitTime"]     ?> </td> 
      <td nowrap class="statTable" > <?php echo $row["SPID"]         ?> </td>
      <td nowrap class="statTable" > <?php echo $row["KPID"]         ?> </td>
      <td nowrap class="statTable" > <?php echo $row["DBID"]         ?> </td>
      <td nowrap class="statTable" > <?php echo $row["ParentSPID"]   ?> </td>
      <td nowrap class="statTable" > <?php echo $row["LockID"]       ?> </td>
      <td nowrap class="statTable" > <?php echo $row["BlockedBy"]    ?> </td>
      <td nowrap class="statTable" > <?php echo $row["BlockedState"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["Context"]      ?> </td>
      <td nowrap class="statTable" > <?php echo $row["ObjectID"]     ?> </td>
      <td nowrap class="statTable" > <?php echo $row["lckBase"]      ?> </td>
      <td nowrap class="statTable" > <?php echo $row["lckObjet"]     ?> </td>
      <td nowrap class="statTable" > <?php echo $row["lckPage"]      ?> </td>
      <td nowrap class="statTable" > <?php echo $row["lckRow"]       ?> </td>
      <td nowrap class="statTable" > <?php echo $row["LockState"]    ?> </td>
      <td nowrap class="statTable" > <?php echo $row["LockType"]     ?> </td>
      <td nowrap class="statTable" > <?php echo $row["LockLevel"]    ?> </td>
      <td nowrap class="statTable" > <?php echo $row["Usr"]          ?> </td>
      <td nowrap class="statTable" > <?php echo $row["Trn"]          ?> </td>
      <td nowrap class="statTable" > <?php echo $row["Prg"]          ?> </td>
      <td nowrap class="statTable" > <?php echo $row["Prc"]          ?> </td>
      <td nowrap class="statTable" > <?php echo $row["Line"]         ?> </td>
     </tr> 
    <?php
          } // end while
	} // end if $result...
	if ($rw == 0 )  {
    ?>
    <tr>
       <td colspan="23" align="center" > <font  STYLE="font-weight: 900"> No blocking situation   </font> </td>
    </tr>
    
    <?php 	
	     }

        // Drop temporary table
	$query = "drop table #blk_max set dateformat mdy";
	$result = sybase_query($query,$pid);
    ?>

</table>

</DIV>
</DIV>
</DIV>

</center>
