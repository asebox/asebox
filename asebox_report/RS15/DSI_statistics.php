<script>

setStatMainTableSize(0);


function getDSIDetail(instance_id, instance, StartTimestamp, EndTimestamp)
{
  ARContextJSON = document.inputparam.ARContextJSON.value;
/*
  WindowObjectReference = window.open("RS15/DSI_detail.php?instance_id="+instance_id+"&instance="+instance+"&StartTimestamp="+StartTimestamp+"&EndTimestamp="+EndTimestamp+"&ARContextJSON="+ARContextJSON+"#top",
    "_blank");
*/
  WindowObjectReference = window.open("RS15/module_detail.php?Module='DSI','DSIEXEC','DSIHQ'&instance_id="+instance_id+"&instance="+instance+"&StartTimestamp="+StartTimestamp+"&EndTimestamp="+EndTimestamp+"&ARContextJSON="+ARContextJSON+"#top",
    "_blank");
  WindowObjectReference.focus();
}
</script>


<center>
<div class="boxinmain" style="min-width:400px">
<div class="boxtop">
<img src="images/boxtop-corner-left.jpg" style="float:left;margin:0px"/>
<div class="title"><?php echo  "DSI Statistics" ?></div>
<img src="images/boxtop-corner-right.jpg" style="float:right;margin:0px;"/>
</div>
<div class="boxcontent">
<div class="statMainTable" style="overflow-y:visible">
    <table cellspacing=2 cellpadding=4>
    <tr> 
      <td class="statTabletitle" > instance_id     </td>
      <td class="statTabletitle" > Instance     </td>
      <td class="statTabletitle" > DSICmdsSucceed   </td>
      <td class="statTabletitle" > DSITranGroupsSucceeded    </td>
      <td class="statTabletitle" > DSIReadTransUngrouped    </td>
      <td class="statTabletitle" > Inserts    </td>
      <td class="statTabletitle" > Updates   </td>
      <td class="statTabletitle" > Deletes   </td>
      <td class="statTabletitle" > SysTrans   </td>
      <td class="statTabletitle" > CmdsSQLDDL    </td>
      <td class="statTabletitle" > Commits    </td>
    </tr>



    <?php 
    // Get DSI and DSIEXEC summary statistics 
    $instance_id_search_clause = "";
    $DSI_type = "DSI";
    include ("./RS15/sql/DSI_summary_stats.php");
    $result=sybase_query($query_DSI_summary_stats, $pid);
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
        <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';" ONCLICK='javascript:getDSIDetail(" <?php echo $row["instance_id"] ?>", "<?php echo $row["instance"] ?>","<?php echo $StartTimestamp?>","<?php echo $EndTimestamp?>" )' >
        <?php
        $cpt=1-$cpt;
        ?>

        <td class="statTablePtr" > <?php echo $row["instance_id"] ?> </td>
        <td class="statTablePtr" width="250px" nowrap> <?php echo $row["instance"] ?> </td> 
        <td class="statTablePtr" > <?php echo $row["DSICmdsSucceed"] ?> </td> 
        <td class="statTablePtr" > <?php echo $row["DSITranGroupsSucceeded"] ?> </td> 
        <td class="statTablePtr" > <?php echo $row["DSIReadTransUngrouped"] ?> </td> 
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
</table>
</DIV>
</DIV>
</DIV>
</center>

