<div  style="overflow:visible" class="boxinmain">

<<<<<<< HEAD

<div class="boxtop">
<img src="images/boxtop-corner-left.jpg" style="float:left;margin:0px"/>
<div class="title">Lock analysis</div>
<img src="images/boxtop-corner-right.jpg" style="float:right;margin:0px;"/>
=======
<div class="boxtop">
<div style="float:left; position: relative; top: 3px; left: 6px"></div>
<div class="title">Lock analysis</div>
>>>>>>> 3.1.0
</div>

<div class="boxcontent">


<table width="50%" border="0" cellspacing="1" cellpadding="0" >


<<<<<<< HEAD




=======
>>>>>>> 3.1.0
	<?php 
		// Prepare lock analysis

if ($ArchSrvType=="Adaptive Server Enterprise") {  
    // It is on ASE, isolate declare cursor step
<<<<<<< HEAD
=======

		//$result=sybase_query("
                //        close c
                //        deallocate cursor c",
		//	           $pid);
    
>>>>>>> 3.1.0
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
			include ("../connectArchiveServer.php");	
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
			include ("../connectArchiveServer.php");	
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



		//  Get total time blocked
		$result=sybase_query(
			"select total_time_blocked=isnull(sum(max_time_blocked),0)
			from #blk_max",
		 	$pid);
		while (($row=sybase_fetch_array($result)))
		{
			$total_time_blocked= $row["total_time_blocked"];
		}
                
                // Nombre de situations de blocage, nb proc bloques, temps moyen , temps max
		$result=sybase_query(
			"select nb_block=count(*),
			        avg_blocked_proc=avg( nbblkprocs), 
			        max_blocked_proc=max( nbblkprocs),
			        avg_time_blocked_s=avg(max_time_blks),
			        max_time_blocked_s=max(max_time_blks)
			 from (
			   select nbblkprocs=count(*),
			        max_time_blks=max(max_time_blocked)
		           from #blk_max
		           group by max_TS) statBlks",
		 	$pid);
		while (($row=sybase_fetch_array($result)))
		{
			$nb_block= $row["nb_block"];
			$avg_blocked_proc= $row["avg_blocked_proc"];
			$max_blocked_proc= $row["max_blocked_proc"];
			$avg_time_blocked_s= $row["avg_time_blocked_s"];
			$max_time_blocked_s= $row["max_time_blocked_s"];
		}
             

	?>
		
    <tr> 
      <td class="statTableUpperTitle" colspan="5" align="center"> Total time blocked (s) :</td>
    </tr>
    <tr>
      <td class="statTable" colspan="5" align="center">  <?php echo $total_time_blocked ?> </td>
    </tr>
    <tr>
      <td class="statTabletitle" colspan="1">  nb_block </td>
      <td class="statTabletitle" colspan="1">  avg_blocked_proc </td>
      <td class="statTabletitle" colspan="1">  max_blocked_proc </td>
      <td class="statTabletitle" colspan="1">  avg_time_blocked_s </td>
      <td class="statTabletitle" colspan="1">  max_time_blocked_s </td>
    </tr>
    <tr align="right">
      <td class="statTable" colspan="1"> <?php echo $nb_block ?>           </td>
      <td class="statTable" colspan="1"> <?php echo $avg_blocked_proc ?>   </td>
      <td class="statTable" colspan="1"> <?php echo $max_blocked_proc ?>   </td>
      <td class="statTable" colspan="1"> <?php echo $avg_time_blocked_s ?> </td>
      <td class="statTable" colspan="1"> <?php echo $max_time_blocked_s ?> </td>
    </tr>

 
 
    <tr> 
      <td class="statTableUpperTitle" colspan="5"> Top20 Blocking Objects :</td>
    </tr>
    <tr> 
<<<<<<< HEAD
      <td class="statTabletitle" > Database  </td>
      <td class="statTabletitle" > Table  </td>
      <td class="statTabletitle" > LockName   </td>
      <td class="statTabletitle" > nb_block   </td>
      <td class="statTabletitle" > tps_block_s   </td>
=======
      <td class="statTabletitle" > Database    </td>
      <td class="statTabletitle" > Table       </td>
      <td class="statTabletitle" > LockName    </td>
      <td class="statTabletitle" > nb_block    </td>
      <td class="statTabletitle" > tps_block_s </td>
>>>>>>> 3.1.0
    </tr>
 
    <?php
        
	// Nombre et temps de blocage par objet et type de lock
	$query="set rowcount 20 
		select lckBase,lckObjet,lckName, nb_block=count(*), tps_block_s=sum(time_blocked)
		from ".$ServerName."_BlockedP A , #blk_max B
		where A.Timestamp = B.max_TS
		and A.blockedSpid = B.blockedSpid
		group by lckBase,lckObjet,lckName  
		order by tps_block_s desc     
		set rowcount 0";

	
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
            <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';">
            <?php
            $cpt=1-$cpt;
            ?>
             <td colspan="1"        align="left" > <?php echo $row["lckBase"] ?>  </td>
             <td class="statTable"  align="left" > <?php echo $row["lckObjet"] ?> </td>
             <td class="statTable"  align="left" > <?php echo $row["lckName"] ?>  </td>
             <td class="statTable"  align="right"> <?php echo $row["nb_block"] ?> </td>
             <td class="statTable"  align="right"> <?php echo $row["tps_block_s"] ?> </td>
            </tr> 
            <?php
          } // end while
	} // end if $result...
	if ($rw == 0 ) {
    ?>
    <tr>
       <td colspan="5" align="center" > <font  STYLE="font-weight: 900"> No blocking object   </font> </td>
    </tr>
    
    <?php 	
	}
    ?>

    <tr> 
      <td class="statTableUpperTitle" colspan="5"> Top20 Blocking situations :</td>
    </tr>
    <tr> 
      <td class="statTabletitle" colspan="3"> Timestamp  </td>
      <td class="statTabletitle" > nb_blocked_proc  </td>
      <td class="statTabletitle" > max_time_blocked_s   </td>
    </tr>
    <?php
	$query="set rowcount 20 
		select max_TS, nb_blocked_proc=count(*), max_time_blocked=max(max_time_blocked)
		from #blk_max
		group by max_TS
		order by  max_time_blocked desc
		set rowcount 0";
	
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
            <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';">
            <?php
            $cpt=1-$cpt;
            ?>
 
              <td class="statTable"  align="left" colspan="3">    <?php echo $row["max_TS"] ?>  </td>
              <td class="statTable"  align="right"> <?php echo $row["nb_blocked_proc"] ?>  </td>
              <td class="statTable"  align="right"> <?php echo $row["max_time_blocked"] ?> </td>
    
            </tr> 
            <?php
          } // end while
	      } // end if $result...
	      if ($rw == 0 ) {
        ?>
        <tr>
           <td colspan="5" align="center" > <font STYLE="font-weight: 900"> No blocking situation   </font> </td>
        </tr>
        
        <?php 	
	         }
        ?>
 
  </table>

    
  <?php 	
        // Drop temporary table
	$query = "drop table #blk_max";
	$result = sybase_query($query,$pid);
  ?>

</DIV>
</DIV>