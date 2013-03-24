<script>

setStatMainTableSize(0);

function getDISTDetail(ID, instance_id, instance, StartTimestamp, EndTimestamp)
{
  ARContextJSON = document.inputparam.ARContextJSON.value;
  /*
  WindowObjectReference = window.open("RS15/DIST_detail.php?ID="+ID+"&instance_id="+instance_id+"&instance="+instance+"&StartTimestamp="+StartTimestamp+"&EndTimestamp="+EndTimestamp+"&ARContextJSON="+ARContextJSON+"#top",
    "_blank"
    );
  */
  WindowObjectReference = window.open("RS15/module_detail.php?Module='DIST'&ID="+ID+"&instance_id="+instance_id+"&instance="+instance+"&StartTimestamp="+StartTimestamp+"&EndTimestamp="+EndTimestamp+"&ARContextJSON="+ARContextJSON+"#top",
    "_blank"
    );
	
  WindowObjectReference.focus();
}
</script>

<center>
<div class="boxinmain" style="min-width:400px">
<div class="boxtop">
<img src="images/boxtop-corner-left.jpg" style="float:left;margin:0px"/>
<div class="title"><?php echo  "DIST Statistics" ?></div>
<img src="images/boxtop-corner-right.jpg" style="float:right;margin:0px;"/>
</div>
<div class="boxcontent">
<div class="statMainTable" style="overflow-y:visible">
<table  cellspacing=2 cellpadding=4 >
    <tr> 
      <td class="statTabletitle" > instance_id                   </td>
      <td class="statTabletitle"> Instance                      </td>
      <td class="statTabletitle" > CmdsRead             </td>
      <td class="statTabletitle" > TransProcessed             </td>
      <td class="statTabletitle" > CmdsNoRepdef             </td>
      <td class="statTabletitle" > Duplicates             </td>
      <td class="statTabletitle" > CmdsIgnored             </td>
      <td class="statTabletitle" > SqtMaxCache             </td>

    </tr>



 <?php 
    // Get DIST summary statistics 
    $ID_search_clause="";
    include ("./RS15/sql/DIST_summary_stats.php");
    $result=sybase_query($query_DIST_summary_stats, $pid);
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
        <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';"  ONCLICK='javascript:getDISTDetail("<?php echo $row["ID"] ?>", "<?php echo $row["instance_id"] ?>", "<?php echo $row["instance"] ?>","<?php echo $StartTimestamp?>","<?php echo $EndTimestamp?>" )' >
        <?php
        $cpt=1-$cpt;
        ?>
        
        <td class="statTablePtr" > <?php echo $row["instance_id"] ?> </td> 
        <td class="statTablePtr" width="250px" nowrap> <?php echo $row["instance"] ?> </td> 
        <td class="statTablePtr" align="right" > <?php echo $row["CmdsRead"] ?> </td>
        <td class="statTablePtr" align="right" > <?php echo $row["TransProcessed"] ?> </td>
        <td class="statTablePtr" align="right" > <?php echo $row["CmdsNoRepdef"] ?> </td>
        <td class="statTablePtr" align="right" > <?php echo $row["Duplicates"] ?> </td>
        <td class="statTablePtr" align="right" > <?php echo $row["CmdsIgnored"] ?> </td>
        <td class="statTablePtr" align="right" > <?php echo $row["SqtMaxCache"] ?> </td>

        </tr> 
        <?php
    }

    ?>
    </table>
</DIV>
</DIV>
</DIV>
</center>

