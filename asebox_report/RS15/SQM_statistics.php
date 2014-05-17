<script>

setStatMainTableSize(0);


function getSQMDetail(ID, instance_id, instance, StartTimestamp, EndTimestamp)
{
  ARContextJSON = document.inputparam.ARContextJSON.value;
/*
  WindowObjectReference = window.open("RS15/SQM_detail.php?ID="+ID+"&instance_id="+instance_id+"&instance="+instance+"&StartTimestamp="+StartTimestamp+"&EndTimestamp="+EndTimestamp+"&ARContextJSON="+ARContextJSON+"#top",
    "_blank");
*/
  WindowObjectReference = window.open("RS15/module_detail.php?Module='SQM'&ID="+ID+"&instance_id="+instance_id+"&instance="+instance+"&StartTimestamp="+StartTimestamp+"&EndTimestamp="+EndTimestamp+"&ARContextJSON="+ARContextJSON+"#top",
    "_blank");
  WindowObjectReference.focus();
}
</script>


<center>
<div class="boxinmain" style="min-width:400px">
<div class="boxtop">
<img src="images/boxtop-corner-left.jpg" style="float:left;margin:0px"/>
<div class="title"><?php echo  "Stable Queue Manager Statistics" ?></div>
<img src="images/boxtop-corner-right.jpg" style="float:right;margin:0px;"/>
</div>
<div class="boxcontent">
<div class="statMainTable" style="overflow-y:visible">
    <table cellspacing=2 cellpadding=4>
    <tr> 
        <td class="statTabletitle" > instance_id     </td>
        <td class="statTabletitle" > Instance     </td>

        <td class="statTabletitle" > CmdsWritten          </td>
        <td class="statTabletitle" > AvgCmdSize           </td>
        <td class="statTabletitle" > MaxCmdSize           </td>
        <td class="statTabletitle" > BlocksWritten        </td>
        <td class="statTabletitle" > BlocksFullWrite      </td>
        <td class="statTabletitle" > BytesWritten         </td>
        <td class="statTabletitle" > MaxSegsActive        </td>
        <td class="statTabletitle" > UpdsRsoqid           </td>
        <td class="statTabletitle" > AvgSQMWriteTime_ms           </td>

    </tr>



    <?php 
    // Get SQM summary statistics 
    $ID_search_clause = "";
    $ID_clause = "";
    $SQM_type = "SQM,";
    include ("./RS15/sql/SQM_summary_stats.php");
    $result=sybase_query($query_SQM_summary_stats, $pid);
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

        <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';" ONCLICK='javascript:getSQMDetail("<?php echo $row["ID"] ?>", "<?php echo $row["instance_id"] ?>", "<?php echo $row["instance"] ?>","<?php echo $StartTimestamp?>","<?php echo $EndTimestamp?>" )' >
        <?php
        $cpt=1-$cpt;
        ?>

        <td class="statTablePtr" > <?php echo $row["instance_id"] ?> </td>
        <td class="statTablePtr" width="250px" nowrap> <?php echo $row["instance"] ?> </td> 

        <td class="statTablePtr" align="right" > <?php echo $row["CmdsWritten"] ?> </td>
        <td class="statTablePtr" align="right" > <?php echo $row["AvgCmdSize"] ?> </td>
        <td class="statTablePtr" align="right" > <?php echo $row["MaxCmdSize"] ?> </td>
        <td class="statTablePtr" align="right" > <?php echo $row["BlocksWritten"] ?> </td>
        <td class="statTablePtr" align="right" > <?php echo $row["BlocksFullWrite"] ?> </td>
        <td class="statTablePtr" align="right" > <?php echo $row["BytesWritten"] ?> </td>
        <td class="statTablePtr" align="right" > <?php echo $row["MaxSegsActive"] ?> </td>
        <td class="statTablePtr" align="right" > <?php echo $row["UpdsRsoqid"] ?> </td>
        <td class="statTablePtr" align="right" > <?php echo $row["AvgSQMWriteTime_ms"] ?> </td>

<?php /*
WritesFailedLoss    
WritesTimerPop      
WritesForceFlush    
WriteRequests       
XNLWrites           
XNLSkips            
AvgXNLSize          
SleepsStartQW       
SleepsWaitSeg       
SleepsWriteRScmd    
SleepsWriteDRmarker 
SleepsWriteEnMarker 
AffinityHintUsed    
*/
?>


        <?php
    }
    ?>
    </table>
</DIV>
</DIV>
</DIV>
</center>

