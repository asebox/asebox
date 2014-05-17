<script>
function getRADetail(Instance_ID, Info, StartTimestamp, EndTimestamp)
{
  ARContextJSON = document.inputparam.ARContextJSON.value;
  WindowObjectReference = window.open("RS126/RA_detail.php?Instance_ID="+Instance_ID+"&Info="+Info+"&StartTimestamp="+StartTimestamp+"&EndTimestamp="+EndTimestamp+"&ARContextJSON="+ARContextJSON+"#top",
    "_blank"
    );
  WindowObjectReference.focus();
}

function getDSIDetail(Instance_ID, Info, StartTimestamp, EndTimestamp)
{
  ARContextJSON = document.inputparam.ARContextJSON.value;
  WindowObjectReference = window.open("RS126/DSI_detail.php?Instance_ID="+Instance_ID+"&Info="+Info+"&StartTimestamp="+StartTimestamp+"&EndTimestamp="+EndTimestamp+"&ARContextJSON="+ARContextJSON+"#top",
    "_blank");
  WindowObjectReference.focus();
}
</script>

<center>
<div  style="overflow:visible;min-width:600px" class="boxinmain" >

<div class="boxtop">
<img src="images/boxtop-corner-left.jpg" style="float:left;margin:0px"/>
<div class="title"><?php echo  "Rep Agent's Statistics"  ?></div>
<!--(Tot. reads = Reads + APFReads)</div>-->
<img src="images/boxtop-corner-right.jpg" style="float:right;margin:0px;"/>
</div>

<div class="boxcontent">
<div class="statMainTable">
<table width="100%" cellspacing="2" cellpadding="4">
    <tr> 
      <td class="statTabletitle" > Instance_ID     </td>
      <td class="statTabletitle" > Info     </td>
      <td class="statTabletitle" > CmdsTotal  		</td>
      <td class="statTabletitle" > CmdsApplied 		</td>
      <td class="statTabletitle" > UpdsRslocater   	</td>
      <td class="statTabletitle" > PacketsReceived	</td>
      <td class="statTabletitle" > BytesReceived   	</td>
      <td class="statTabletitle" > PacketSize   	</td>
    </tr>



	<?php 
		// Get REPAGENT's summary statistics	
		$query = "select Instance_ID, Info,
		    CmdsTotal=sum(convert(numeric(14,0),CmdsTotal)),   
		    CmdsApplied=sum(convert(numeric(14,0),CmdsApplied)),
		    UpdsRslocater = sum(UpdsRslocater ),
		    PacketsReceived = sum(convert(numeric(14,0),PacketsReceived)),
		    BytesReceived = sum(convert(numeric(14,0), BytesReceived)),
		    PacketSize = avg(PacketSize)
		from ".$ServerName."_REPAGENT
		where Timestamp >='".$StartTimestamp."'
		and Timestamp <='".$EndTimestamp."'
		group by Instance_ID, Info";
		$result=sybase_query($query, $pid);
//echo $query;		 
                $rw=0;
                $cpt=0;
		while (($row=sybase_fetch_array($result)))
		{
			$rw++;
			if($cpt==0)
			     $parite="impair";
			else
			     $parite="pair";
            ?>
			<tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';"  ONCLICK='javascript:getRADetail("<?php echo $row["Instance_ID"] ?>", "<?php echo $row["Info"] ?>","<?php echo $StartTimestamp?>","<?php echo $EndTimestamp?>" )' >
            <?php
			$cpt=1-$cpt;
                        ?>

                        <td class="statTablePtr" > <?php echo $row["Instance_ID"] ?> </td> 
                        <td class="statTablePtr" > <?php echo $row["Info"] ?> </td> 
                        <td class="statTablePtr" > <?php echo $row["CmdsTotal"] ?> </td> 
                        <td class="statTablePtr" > <?php echo $row["CmdsApplied"] ?> </td> 
                        <td class="statTablePtr" > <?php echo $row["UpdsRslocater"] ?> </td> 
                        <td class="statTablePtr" > <?php echo $row["PacketsReceived"] ?> </td> 
                        <td class="statTablePtr" > <?php echo $row["BytesReceived"] ?> </td> 
                        <td class="statTablePtr" > <?php echo $row["PacketSize"] ?> </td> 
                        </tr> 
                        <?php
		}

         ?>
    </td></tr>
</table>
</DIV>
</DIV>
</DIV>



<div  style="overflow:visible;min-width:600px" class="boxinmain" >
<div class="boxtop">
<img src="images/boxtop-corner-left.jpg" style="float:left;margin:0px"/>
<div class="title"><?php echo  "Stable devices Statistics"  ?></div>
<!--(Tot. reads = Reads + APFReads)</div>-->
<img src="images/boxtop-corner-right.jpg" style="float:right;margin:0px;"/>
</div>
<div class="boxcontent">
<div class="statMainTable">
<table width="100%" cellspacing="2" cellpadding="4">
    <tr class=statTableTitle> 
      <td class="statTabletitle" > PartId     </td>
      <td class="statTabletitle" > Part_name  		</td>
      <td class="statTabletitle" > Logical 		</td>
      <td class="statTabletitle" > State   	</td>
      <td class="statTabletitle" > CapacityAvg_Mb	</td>
      <td class="statTabletitle" > UsedAvg_Mb   	</td>
      <td class="statTabletitle" > UsedMax_Mb   	</td>
    </tr>



	<?php 
		// Get STABLE DEVICES usage	
		$query = "select PartId, 
		                 Part_name, 
		                 Logical, 
		                 State, 
		                 CapacityAvg_Mb=avg(Total_segs), 
		                 UsedAvg_Mb=avg(Used_segs), 
		                 UsedMax_Mb=max(Used_segs)
		from ".$ServerName."_DISKSPCE
		where Timestamp >='".$StartTimestamp."'
		and Timestamp <='".$EndTimestamp."'
		group by PartId, Part_name, Logical, State";
		$result=sybase_query($query, $pid);
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
                        </tr> 
                        <?php
		}

         ?>
    </td></tr>
</table>
</DIV>
</DIV>
</DIV>









<div  style="overflow:visible;min-width:600px" class="boxinmain" >
<div class="boxtop">
<img src="images/boxtop-corner-left.jpg" style="float:left;margin:0px"/>
<div class="title"><?php echo  "DSI Statistics"  ?></div>
<!--(Tot. reads = Reads + APFReads)</div>-->
<img src="images/boxtop-corner-right.jpg" style="float:right;margin:0px;"/>
</div>
<div class="boxcontent">
<div class="statMainTable">
<table width="100%" cellspacing="2" cellpadding="4">
    <tr> 
      <td class="statTabletitle" > Instance_ID     </td>
      <td class="statTabletitle" > Info     </td>
      <td class="statTabletitle" > CmdsApplied 		</td>
      <td class="statTabletitle" > TransApplied   	</td>
      <td class="statTabletitle" > NgTrans   	</td>
      <td class="statTabletitle" > Inserts   	</td>
      <td class="statTabletitle" > Updates  	</td>
      <td class="statTabletitle" > Deletes  	</td>
      <td class="statTabletitle" > SysTrans  	</td>
      <td class="statTabletitle" > CmdsSQLDDL   	</td>
      <td class="statTabletitle" > Commits   	</td>
    </tr>



	<?php 
		// Get DSIEXEC summary statistics	
		$query = "select Info, Instance_ID,
		    CmdsApplied=sum(convert(numeric(14,0),CmdsApplied)),   
		    TransApplied=sum(convert(numeric(14,0),TransApplied)),
		    NgTrans = sum(convert(numeric(14,0),NgTrans )),
		    InsertsRead = sum(convert(numeric(14,0),InsertsRead)),
		    UpdatesRead = sum(convert(numeric(14,0), UpdatesRead)),
		    DeletesRead = sum(convert(numeric(14,0), DeletesRead)),
		    SysTransRead = sum(convert(numeric(14,0), SysTransRead)),
		    CmdsSQLDDLRead = sum(convert(numeric(14,0), CmdsSQLDDLRead)),
		    CommitsRead = sum(convert(numeric(14,0), CommitsRead))
		from ".$ServerName."_DSIEXEC
		where Timestamp >='".$StartTimestamp."'
		and Timestamp <='".$EndTimestamp."'
		group by Info, Instance_ID";
		$result=sybase_query($query, $pid);
//echo $query;		 
                $rw=0;
                $cpt=0;
		while (($row=sybase_fetch_array($result)))
		{
			$rw++;
            if($cpt==0)
                 $parite="impair";
            else
                 $parite="pair";
            ?>
            <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';" ONCLICK='javascript:getDSIDetail("<?php echo $row["Instance_ID"] ?>", "<?php echo $row["Info"] ?>","<?php echo $StartTimestamp?>","<?php echo $EndTimestamp?>" )' >
            <?php
			$cpt=1-$cpt;
                        ?>

                        <td class="statTablePtr" > <?php echo $row["Instance_ID"] ?> </td>
                        <td class="statTablePtr" NOWRAP> <?php echo $row["Info"] ?> </td> 
                        <td class="statTablePtr" > <?php echo $row["CmdsApplied"] ?> </td> 
                        <td class="statTablePtr" > <?php echo $row["TransApplied"] ?> </td> 
                        <td class="statTablePtr" > <?php echo $row["NgTrans"] ?> </td> 
                        <td class="statTablePtr" > <?php echo $row["InsertsRead"] ?> </td> 
                        <td class="statTablePtr" > <?php echo $row["UpdatesRead"] ?> </td> 
                        <td class="statTablePtr" > <?php echo $row["DeletesRead"] ?> </td> 
                        <td class="statTablePtr" > <?php echo $row["SysTransRead"] ?> </td> 
                        <td class="statTablePtr" > <?php echo $row["CmdsSQLDDLRead"] ?> </td> 
                        <td class="statTablePtr" > <?php echo $row["CommitsRead"] ?> </td> 
                        </tr> 
                        <?php
		}

         ?>
    </td></tr>
</table>
</DIV>
</DIV>
</DIV>
</center>

