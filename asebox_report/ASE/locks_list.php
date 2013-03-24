<?php

	$param_list=array(
		'orderPrc',
		'rowcnt',
		'filterblockedSpid',
		'filterblockedUsr',
		'filterblockedTran',
		'filterblockedProg',
		'filterblockedProc',
		'filterblockedLine',
		'filterblockingSpid',
		'filterblockingUsr',
		'filterblockingTran',
		'filterblockingProg',
		'filterblockingProc',
		'filterblockingLine',
		'filterlckBase',
		'filterlckObjet',
		'filterlckPage',
		'filterlckRow',
		'filterlckName'
	);
	foreach ($param_list as $param)
		@$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];
 

        if ( !isset($orderPrc) ) $orderPrc=$order_by;
        if ( !isset($rowcnt) ) $rowcnt=200;
        if ( !isset($filterblockedSpid ) ) $filterblockedSpid ="";
        if ( !isset($filterblockedUsr  ) ) $filterblockedUsr  ="";
        if ( !isset($filterblockedTran ) ) $filterblockedTran ="";
        if ( !isset($filterblockedProg ) ) $filterblockedProg ="";
        if ( !isset($filterblockedProc ) ) $filterblockedProc ="";
        if ( !isset($filterblockedLine ) ) $filterblockedLine ="";
        if ( !isset($filterblockingSpid) ) $filterblockingSpid="";
        if ( !isset($filterblockingUsr ) ) $filterblockingUsr ="";
        if ( !isset($filterblockingTran) ) $filterblockingTran="";
        if ( !isset($filterblockingProg) ) $filterblockingProg="";
        if ( !isset($filterblockingProc) ) $filterblockingProc="";
        if ( !isset($filterblockingLine) ) $filterblockingLine="";
        if ( !isset($filterlckBase     ) ) $filterlckBase     ="";
        if ( !isset($filterlckObjet    ) ) $filterlckObjet    ="";
        if ( !isset($filterlckPage     ) ) $filterlckPage     ="";
        if ( !isset($filterlckRow      ) ) $filterlckRow      ="";
        if ( !isset($filterlckName     ) ) $filterlckName     ="";    
?>      
        
        
        
<center>
        
        
        
        
        
        
	<?php 
		// Prepare lock analysis

if ($ArchSrvType=="Adaptive Server Enterprise") {  
    // It is on ASE, isolate declare cursor step
		$result=sybase_query(
			"declare c cursor for 
                         select blockedSpid,Timestamp,time_blocked
                         from ".$ServerName."_BlockedP 
                         where Timestamp >='".$StartTimestamp."'
                         and   Timestamp <='".$EndTimestamp."'
                        order by blockedSpid,Timestamp",
                        $pid);

		if ($result==false){ 
			sybase_close($pid); 
			$pid=0;
			echo "<tr><td>Error</td></tr></table>";
			return(0);
		}
        $result = sybase_query("if object_id('#blk_max') is not null drop table #blk_max",$pid);
		$result=sybase_query(
                        "declare @savts datetime, @ts datetime, @savtb int, @tb int, @savspid int, @spid int
                        create table #blk_max (blockedSpid int, max_TS datetime, max_time_blocked int)
                        set nocount on
                        open c
                        fetch c into @savspid,@savts,@savtb
                        while @@sqlstatus=0
                        begin
                           fetch c into @spid,@ts,@tb
                           if @tb <@savtb or @spid != @savspid
                           begin
                             -- max block found for a spid
                             insert into #blk_max values (@savspid,@savts,@savtb)
                           end
                           select @savtb=@tb, @savts=@ts, @savspid=@spid
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
                $query = "create table #blk_max (blockedSpid int, max_TS datetime, max_time_blocked int)";
                $result = sybase_query($query,$pid);
		$result=sybase_query(
			"declare c cursor for 
                         select blockedSpid,Timestamp,time_blocked
                         from ".$ServerName."_BlockedP 
                         where Timestamp >='".$StartTimestamp."'
                         and   Timestamp <='".$EndTimestamp."'
                         order by blockedSpid,Timestamp
                        
                        declare @savts datetime, @ts datetime, @savtb int, @tb int, @savspid int, @spid int
                        
                        set nocount on
                        open c
                        fetch c into @savspid,@savts,@savtb
                        while @@sqlstatus=0
                        begin
                           fetch c into @spid,@ts,@tb
                           if @tb <@savtb or @spid != @savspid
                           begin
                             -- max block found for a spid
                             insert into #blk_max values (@savspid,@savts,@savtb)
                           end
                           select @savtb=@tb, @savts=@ts, @savspid=@spid
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
<a   href="http://github.com/asebox/asebox?title=AseRep_Locks" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="Locks help" TITLE="Locks help"  /> </a>
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
      <td class="statTabletitle" > Timestamp     </td>
      <td class="statTabletitle" > time_blocked  </td>
      <td class="statTabletitle" > blockedSpid   </td>
      <td class="statTabletitle" > blockedUsr    </td>
      <td class="statTabletitle" > blockedTran   </td>
      <td class="statTabletitle" > blockedProg   </td>
      <td class="statTabletitle" > blockedProc   </td>
      <td class="statTabletitle" > blockedLine   </td>
      <td class="statTabletitle" > blockingSpid  </td>
      <td class="statTabletitle" > blockingUsr   </td>
      <td class="statTabletitle" > blockingTran  </td>
      <td class="statTabletitle" > blockingProg  </td>
      <td class="statTabletitle" > blockingProc  </td>
      <td class="statTabletitle" > blockingLine  </td>
      <td class="statTabletitle" > lckBase       </td>
      <td class="statTabletitle" > lckObjet      </td>
      <td class="statTabletitle" > lckPage       </td>
      <td class="statTabletitle" > lckRow        </td>
      <td class="statTabletitle" > lckName       </td>
    </tr>
    
    <tr> 
      <td></td> 
      <td></td> 
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterblockedSpid"  value="<?php if( isset($filterblockedSpid) ){ echo $filterblockedSpid ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterblockedUsr"   value="<?php if( isset($filterblockedUsr) ) { echo $filterblockedUsr ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterblockedTran"  value="<?php if( isset($filterblockedTran) ) { echo $filterblockedTran ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterblockedProg"  value="<?php if( isset($filterblockedProg) ) { echo $filterblockedProg ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterblockedProc"  value="<?php if( isset($filterblockedProc) ) { echo $filterblockedProc ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterblockedLine"  value="<?php if( isset($filterblockedLine) ) { echo $filterblockedLine ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterblockingSpid" value="<?php if( isset($filterblockingSpid) ) { echo $filterblockingSpid ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterblockingUsr"  value="<?php if( isset($filterblockingUsr) ) { echo $filterblockingUsr ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterblockingTran" value="<?php if( isset($filterblockingTran) ) { echo $filterblockingTran ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterblockingProg" value="<?php if( isset($filterblockingProg) ) { echo $filterblockingProg ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterblockingProc" value="<?php if( isset($filterblockingProc) ) { echo $filterblockingProc ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterblockingLine" value="<?php if( isset($filterblockingLine) ) { echo $filterblockingLine ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterlckBase"      value="<?php if( isset($filterlckBase) ) { echo $filterlckBase ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterlckObjet"     value="<?php if( isset($filterlckObjet) ) { echo $filterlckObjet ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterlckPage"      value="<?php if( isset($filterlckPage) ) { echo $filterlckPage ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterlckRow"       value="<?php if( isset($filterlckRow) ) { echo $filterlckRow ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterlckName"      value="<?php if( isset($filterlckName) ) { echo $filterlckName ; } ?>" > </td>
    </tr>
    
    
    <?php
	$query="set rowcount ".$rowcnt. " 
		select A.*
		from ".$ServerName."_BlockedP A , #blk_max B
		where A.Timestamp = B.max_TS
		and A.blockedSpid = B.blockedSpid
	        and (A.blockedSpid = convert(int,'".$filterblockedSpid."') or '".$filterblockedSpid."' ='')
	        and isnull(blockedUsr,'')   like '%".$filterblockedUsr."%'
	        and isnull(blockedTran,'')  like '%".$filterblockedTran."%'
	        and isnull(blockedProg,'')  like '%".$filterblockedProg."%'
	        and isnull(blockedProc,'')  like '%".$filterblockedProc."%'
	        and (blockedLine = convert(int,'".$filterblockedLine."') or '".$filterblockedLine."' ='')
	        and (blockingSpid = convert(int,'".$filterblockingSpid."') or '".$filterblockingSpid."' ='')
	        and isnull(blockingUsr,'')  like '%".$filterblockingUsr."%'
	        and isnull(blockingTran,'') like '%".$filterblockingTran."%'
	        and isnull(blockingProg,'') like '%".$filterblockingProg."%'
	        and isnull(blockingProc,'') like '%".$filterblockingProc."%'
	        and (blockingLine = convert(int,'".$filterblockingLine."') or '".$filterblockingLine."' ='')
	        and isnull(lckBase,'')      like '%".$filterlckBase."%'
	        and isnull(lckObjet,'')     like '%".$filterlckObjet."%'
	        and (lckPage = convert(int,'".$filterlckPage."') or '".$filterlckPage."' ='')
	        and (lckRow = convert(int,'".$filterlckRow."') or '".$filterlckRow."' ='')
	        and isnull(lckName,'')      like '%".$filterlckName."%'
		order by A.Timestamp, A.time_blocked desc
		set rowcount 0";
  //echo $query;	
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
      <td nowrap class="statTable" > <?php echo $row["Timestamp"] ?>    </td> 
      <td nowrap class="statTable" > <?php echo $row["time_blocked"] ?> </td> 
      <td nowrap class="statTable" > <?php echo $row["blockedSpid"] ?>  </td>  
      <td nowrap class="statTable" > <?php echo $row["blockedUsr"] ?>   </td>   
      <td nowrap class="statTable" > <?php echo $row["blockedTran"] ?>  </td>  
      <td nowrap class="statTable" > <?php echo $row["blockedProg"] ?>  </td>  
      <td nowrap class="statTable" > <?php echo $row["blockedProc"] ?>  </td>  
      <td nowrap class="statTable" > <?php echo $row["blockedLine"] ?>  </td>  
      <td nowrap class="statTable" > <?php echo $row["blockingSpid"] ?> </td> 
      <td nowrap class="statTable" > <?php echo $row["blockingUsr"] ?>  </td>  
      <td nowrap class="statTable" > <?php echo $row["blockingTran"] ?> </td> 
      <td nowrap class="statTable" > <?php echo $row["blockingProg"] ?> </td> 
      <td nowrap class="statTable" > <?php echo $row["blockingProc"] ?> </td> 
      <td nowrap class="statTable" > <?php echo $row["blockingLine"] ?> </td> 
      <td nowrap class="statTable" > <?php echo $row["lckBase"] ?>      </td> 
      <td nowrap class="statTable" > <?php echo $row["lckObjet"] ?>     </td>
      <td nowrap class="statTable" > <?php echo $row["lckPage"] ?>      </td> 
      <td nowrap class="statTable" > <?php echo $row["lckRow"] ?>       </td>  
      <td nowrap class="statTable" > <?php echo $row["lckName"] ?>      </td> 
     </tr> 
    <?php
          } // end while
	} // end if $result...
	if ($rw == 0 )  {
    ?>
    <tr>
       <td colspan="19" align="center" > <font  STYLE="font-weight: 900"> No blocking situation   </font> </td>
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














