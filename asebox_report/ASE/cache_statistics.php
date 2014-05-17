<?php
    include './ASE/sql/sql_cache_statistics.php';
?>

<div  style="overflow:visible" class="boxinmain">

<div class="boxtop">
<div style="float:left; position: relative; top: 3px; left: 6px"><?php include './export/export-table.php' ?></div>
<div style="width:60%" class="title">Caches Statistics</div>
</div>

<div class="boxcontent">

<table border="0" cellspacing="2" cellpadding="4">
    <tr align="center"> 
      <td class="statTabletitle"> CacheName  </td>
      <td class="statTabletitle"> sumSearch  </td>
      <td class="statTabletitle"> Caches Hits  </td>
      <td class="statTabletitle"> Caches Misses  </td>
      <td class="statTabletitle"> sumWrites  </td>
      <td class="statTabletitle"> sumStalls  </td>
      <td class="statTabletitle"> Hit_pct   </td>
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
                <td class="statTable"> <?php echo $row["CacheName"] ?> </td>
                <td class="statTable" align="right"> <?php echo number_format($row["sumSearch"]) ?> </td>
                <td class="statTable" align="right"> <?php echo number_format($row["sumLReads"]) ?> </td>
                <td class="statTable" align="right"> <?php echo number_format($row["sumReads"] ) ?>  </td>
                <td class="statTable" align="right"> <?php echo number_format($row["sumWrites"]) ?> </td>
                <td class="statTable" align="right"> <?php echo number_format($row["sumStalls"]) ?> </td>
                <td class="statTable" align="right"> <?php echo number_format($row["Hit_pct"],2  ) ?>   </td>
              </tr> 
        <?php
        }
        ?>
</table>
</DIV>
</DIV>

