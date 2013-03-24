<?php
    include './ASE/sql/sql_logcontention_statistics.php';
?>

<div class="boxinmain">

<div class="boxtop">
<div style="float:left; position: relative; top: 3px; left: 6px"><?php include './export/export-table.php' ?></div>
<div style="width:60%" class="title">Logs contention </div>
</div>

<div class="boxcontent">

<div style="overflow:visible"  class="statMainTable">


<table width="50%" border="0" cellspacing="1" cellpadding="0">
    <tr align="center"> 
      <td class="statTabletitle"> DBName       </td>
      <td class="statTabletitle"> sumAppendLog </td>
      <td class="statTabletitle"> sumLogWaits  </td>
      <td class="statTabletitle"> waitPct      </td>
    </tr>

    <?php
        $result = sybase_query($query,$pid);

        $rw=0;
        $cpt=0;
        while($row = sybase_fetch_array($result))
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
    <td class="statTable"> <?php echo $row["DBName"] ?>       </td>
    <td class="statTable" align="right"> <?php echo number_format($row["sumAppendLog"])?> </td>
    <td class="statTable" align="right"> <?php echo number_format($row["sumLogWaits"] )?> </td>
    <td class="statTable" align="right"> <?php echo number_format($row["waitPct"],2   )?> </td>
    </tr> 
    <?php
        }
?>
</table>
</DIV>
</DIV>
</DIV>
