<div  style="clear:left;overflow:visible" class="boxinmain">



<div class="boxtop">
<img src="images/boxtop-corner-left.jpg" style="float:left;margin:0px"/>
<div class="title" style="width:85%" >Summary Statistics</div>
<img src="images/boxtop-corner-right.jpg" style="float:right;margin:0px;"/>
</div>
<div class="boxcontent">


<table width="50%" border="0" cellspacing="1" cellpadding="0">

	<?php 
		// Get activity counters	
		$query = "select opers_scanned=sum(opers_scanned*1.0),
                     opers_processed=sum(opers_processed*1.0),
                     pct_opers_processed= case when sum(opers_scanned*1.0)=0 then '0' else str(100. * sum(opers_processed*1.0) / sum(opers_scanned*1.0),10,2) +' %' end,
                     opers_skipped=sum(opers_skipped*1.0),
                     transactions_opened=sum(transactions_opened*1.0),
                     transactions_committed=sum(transactions_committed*1.0),
                     transactions_aborted=sum(transactions_aborted),
                     transactions_processed=sum(transactions_processed),
                     transactions_skipped=sum(transactions_skipped),
                     number_of_ltl_commands_sent=sum(number_of_ltl_commands_sent*1.0),
                     vm_max_memory=max(vm_maximum_memory),
                     vm_max_memory_used=str(max(vm__max_memory_used),10,2)+' %',
                     vm_total_memory_allocated=max(vm_total_memory_allocated)
                            
                     from ".$ServerName."_RAOSTATS          
                     where Timestamp >='".$StartTimestamp."'        
                     and Timestamp <'".$EndTimestamp."'";
		$result=sybase_query($query, $pid);

    $cnt=0;
		while (($row=sybase_fetch_array($result)))
		{
      $opers_scanned = $row["opers_scanned"]; 
      $opers_processed = $row["opers_processed"]; 
      $pct_opers_processed = $row["pct_opers_processed"]; 
      $opers_skipped = $row["opers_skipped"]; 
      $transactions_opened = $row["transactions_opened"]; 
      $transactions_committed = $row["transactions_committed"]; 
      $transactions_aborted = $row["transactions_aborted"]; 
      $transactions_processed = $row["transactions_processed"]; 
      $transactions_skipped = $row["transactions_skipped"]; 
      $number_of_ltl_commands_sent = $row["number_of_ltl_commands_sent"]; 
      $vm_max_memory = $row["vm_max_memory"]; 
      $vm_max_memory_used = $row["vm_max_memory_used"]; 
      $vm_total_memory_allocated = $row["vm_total_memory_allocated"];
      $cnt++;
		}          

	?>
		
    <tr> 
      <td class="statTableUpperTitle" colspan="5"> Activity : </td>
    </tr>
    <tr align="center"> 
      <td class="statTabletitle" colspan="2"> opers_scanned  </td>
      <td class="statTabletitle" colspan="1"> opers_processed  </td>
      <td class="statTabletitle" colspan="1"> pct_opers_processed  </td>
      <td class="statTabletitle" colspan="1"> opers_skipped  </td>
    </tr>
    <tr align="right">
      <td class="statTable" colspan="2">  <?php echo $opers_scanned ?> </td>
      <td class="statTable" colspan="1">  <?php echo $opers_processed ?> </td>
      <td class="statTable" colspan="1">  <?php echo $pct_opers_processed ?> </td>
      <td class="statTable" colspan="1">  <?php echo $opers_skipped ?> </td>
    </tr>
 
    <tr> 
      <td class="statTableUpperTitle" colspan="5"> Transactions : </td>
    </tr>
    <tr align="center"> 
      <td class="statTabletitle" colspan="1"> transactions_opened  </td>
      <td class="statTabletitle" colspan="1"> transactions_committed  </td>
      <td class="statTabletitle" colspan="1"> transactions_aborted  </td>
      <td class="statTabletitle" colspan="1"> transactions_processed  </td>
      <td class="statTabletitle" colspan="1"> transactions_skipped  </td>
    </tr>
    <tr align="right">
      <td class="statTable" colspan="1">  <?php echo $transactions_opened ?> </td>
      <td class="statTable" colspan="1">  <?php echo $transactions_committed ?> </td>
      <td class="statTable" colspan="1">  <?php echo $transactions_aborted ?> </td>
      <td class="statTable" colspan="1">  <?php echo $transactions_processed ?> </td>
      <td class="statTable" colspan="1">  <?php echo $transactions_skipped ?> </td>
    </tr>

    <tr> 
      <td class="statTableUpperTitle" colspan="5"> Java VM : </td>
    </tr>
    <tr align="center"> 
      <td class="statTabletitle" colspan="1"> vm_max_memory  </td>
      <td class="statTabletitle" colspan="1"> vm_max_memory_used  </td>
      <td class="statTabletitle" colspan="3"> vm_total_memory_allocated  </td>
    </tr>
    <tr align="right">
      <td class="statTable" colspan="1">  <?php echo $vm_max_memory ?> </td>
      <td class="statTable" colspan="1">  <?php echo $vm_max_memory_used ?> </td>
      <td class="statTable" colspan="3">  <?php echo $vm_total_memory_allocated ?> </td>
    </tr>

  </table>

  </DIV>


</DIV>