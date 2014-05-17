<script>

setStatMainTableSize(0);


</script>

<center>
<div class="boxinmain" style="min-width:600px">
<div class="boxtop">
<img src="images/boxtop-corner-left.jpg" style="float:left;margin:0px"/>
<div class="title"><?php echo  "Stable devices Statistics" ?></div>
<img src="images/boxtop-corner-right.jpg" style="float:right;margin:0px;"/>
</div>
<div class="boxcontent">
<div class="statMainTable" style="overflow-y:visible">
<table cellspacing=2 cellpadding=4 >
    <tr> 
      <td class="statTabletitle" > PartId     </td>
      <td class="statTabletitle" > Part_name  		</td>
      <td class="statTabletitle" > Logical 		</td>
      <td class="statTabletitle" > State   	</td>
      <td class="statTabletitle" > CapacityAvg_Mb	</td>
      <td class="statTabletitle" > UsedAvg_Mb   	</td>
      <td class="statTabletitle" > UsedMax_Mb   	</td>
      <td class="statTabletitle" > UsedLast_Mb   	</td>
    </tr>



	<?php 
  $blocksize = 16; // This is currently fixed but should be based on config
  
		// Get STABLE DEVICES usage	
		$query = "select PartId, 
		                 Part_name, 
		                 Logical, 
		                 State, 
		                 CapacityAvg_Mb=avg(Total_segs)*64*".$blocksize."/1024, 
		                 UsedAvg_Mb=avg(Used_segs)*64*".$blocksize."/1024, 
		                 UsedMax_Mb=max(Used_segs)*64*".$blocksize."/1024,
						 UsedLast_Mb=(select Used_segs*64*".$blocksize."/1024 
                                      from ".$ServerName."_DISKSPCE
                                      where Timestamp = 
						                 (select max(Timestamp) from ".$ServerName."_DISKSPCE where Timestamp <='".$EndTimestamp."')
									  and PartId = D.PartId
                                     )
		from ".$ServerName."_DISKSPCE D
		where Timestamp >='".$StartTimestamp."'
		and Timestamp <='".$EndTimestamp."'
		group by PartId, Part_name, Logical, State
		order by PartId";
		$result=sybase_query($query, $pid);
		$rw=0;
		$cpt=0;
//echo $query;		 
		while (($row=sybase_fetch_array($result)))
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

                        <td class="statTable" > <?php echo $row["PartId"] ?> </td> 
                        <td class="statTable" > <?php echo $row["Part_name"] ?> </td> 
                        <td class="statTable" > <?php echo $row["Logical"] ?> </td> 
                        <td class="statTable" > <?php echo $row["State"] ?> </td> 
                        <td class="statTable" > <?php echo $row["CapacityAvg_Mb"] ?> </td> 
                        <td class="statTable" > <?php echo $row["UsedAvg_Mb"] ?> </td> 
                        <td class="statTable" > <?php echo $row["UsedMax_Mb"] ?> </td> 
                        <td class="statTable" > <?php echo $row["UsedLast_Mb"] ?> </td> 
                        </tr> 
                        <?php
		}

         ?>
    </table>
</DIV>
</DIV>
</DIV>
</center>

