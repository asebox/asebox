<script>

setStatMainTableSize(0);


function getSQMRDetail(ID, instance_id, instance, StartTimestamp, EndTimestamp)
{
  ARContextJSON = document.inputparam.ARContextJSON.value;
  WindowObjectReference = window.open("RS15/module_detail.php?Module='SQMR'&ID="+ID+"&instance_id="+instance_id+"&instance="+instance+"&StartTimestamp="+StartTimestamp+"&EndTimestamp="+EndTimestamp+"&ARContextJSON="+ARContextJSON+"#top",
    "_blank");
  WindowObjectReference.focus();
}
</script>


<center>
<div class="boxinmain" style="min-width:400px">
<div class="boxtop">
<img src="images/boxtop-corner-left.jpg" style="float:left;margin:0px"/>
<div class="title"><?php echo  "Stable Queue Manager Readers Statistics" ?></div>
<img src="images/boxtop-corner-right.jpg" style="float:right;margin:0px;"/>
</div>
<div class="boxcontent">
<div class="statMainTable" style="overflow-y:visible">
    <table cellspacing=2 cellpadding=4>
    <tr>
        <td class="statTabletitle" > instance_id     </td>
        <td class="statTabletitle" > Instance     </td>

        <td class="statTabletitle" > CmdsRead          </td>
        <td class="statTabletitle" > BlocksRead           </td>
        <td class="statTabletitle" > BlocksReadCached           </td>
        <td class="statTabletitle" > PhysReads           </td>
        <td class="statTabletitle" > AvgSQMRReadTime_ms        </td>

    </tr>




    <?php 
    // Get SQM summary statistics 
    $ID_search_clause = "";
    $ID_clause = "";
    $SQM_type = "SQMR";
    include ("./RS15/sql/SQMR_summary_stats.php");
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

        <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';" ONCLICK='javascript:getSQMRDetail("<?php echo $row["ID"] ?>", "<?php echo $row["instance_id"] ?>", "<?php echo $row["instance"] ?>","<?php echo $StartTimestamp?>","<?php echo $EndTimestamp?>" )' >
        <?php
        $cpt=1-$cpt;
        ?>

        <td class="statTablePtr" > <?php echo $row["instance_id"] ?> </td>
        <td class="statTablePtr" width="350px" nowrap> <?php echo $row["instance"] ?> </td> 

        <td class="statTablePtr" align="right" > <?php echo number_format($row["CmdsRead"]) ?> </td>
        <td class="statTablePtr" align="right" > <?php echo number_format($row["BlocksRead"]) ?> </td>
        <td class="statTablePtr" align="right" > <?php echo number_format($row["BlocksReadCached"]) ?> </td>
        <td class="statTablePtr" align="right" > <?php echo number_format($row["Reads"]) ?> </td>
        <td class="statTablePtr" align="right" > <?php echo number_format($row["AvgSQMRReadTime_ms"],2) ?> </td>


        <?php
    }
    ?>
    </table>
</DIV>
</DIV>
</DIV>
</center>

