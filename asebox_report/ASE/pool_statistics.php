<?php
  // Check if table xxxx_CachePool supports 15.7
  $query = "select cnt=count(*) from syscolumns where id =object_id('".$ServerName."_CachePool')";
  $result = sybase_query($query,$pid);
  $row = sybase_fetch_array($result);
  if ($row["cnt"] > 11)
      $support157=1;
  else
      $support157=0;


    include $rootDir.'/ASE/sql/sql_pool_statistics.php';
?>

<div  class="boxinmain">

<div class="boxtop">
<div style="float:left; position: relative; top: 3px; left: 6px"><?php include $rootDir.'/export/export-table.php' ?></div>
<div class="title">Pool Statistics</div>
</div>

<div class="boxcontent">
<div style="overflow-y:visible"  class="statMainTable">

<table width="75%" border="0" cellspacing="1" cellpadding="0">
    <tr align="center"> 
      <td class="statTabletitle"> CacheName  </td>
      <td class="statTabletitle"> IOBufferSize </td>
      <td class="statTabletitle"> AllocatedKB </td>
      <td class="statTabletitle"> PagesReads </td>
      <td class="statTabletitle"> PhysicalReads </td>
      <td class="statTabletitle"> Stalls </td>
      <td class="statTabletitle"> BuffersToMRU </td>
      <td class="statTabletitle"> BuffersToLRU </td>
      <td class="statTabletitle"> PhysReads/s </td>
      <td class="statTabletitle"> Turnover_s </td>

<?php
      if ($support157==1) {
?>
      <td class="statTabletitle"> LogicalReads </td>
      <td class="statTabletitle"> PhysicalWrites </td>
      <td class="statTabletitle"> APFReads </td>
      <td class="statTabletitle"> APFPercentage </td>
      <td class="statTabletitle"> WashSize </td>
<?php
    }
?>

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
                <td class="statTable" NOWRAP>        <?php echo $row["CacheName"] ?>        </td>
                <td class="statTable" align="right"> <?php echo number_format($row["IOBufferSize"]    ) ?> </td>
                <td class="statTable" align="right"> <?php echo number_format($row["AllocatedKB"]     ) ?> </td>
                <td class="statTable" align="right"> <?php echo number_format($row["sumPagesReads"]   ) ?> </td>
                <td class="statTable" align="right"> <?php echo number_format($row["sumPhysicalReads"]) ?> </td>
                <td class="statTable" align="right"> <?php echo number_format($row["sumStalls"]       ) ?> </td>
                <td class="statTable" align="right"> <?php echo number_format($row["sumBuffersToMRU"] ) ?> </td>
                <td class="statTable" align="right"> <?php echo number_format($row["sumBuffersToLRU"] ) ?> </td>
                <td class="statTable" align="right"> <?php echo number_format($row["PhysReads_s"],2   ) ?> </td>
                <td class="statTable" align="right"> <?php if ($row["Turnover_s"]=="infinite") echo "infinite"; else echo number_format($row["Turnover_s"]      ); ?> </td>
<?php
      if ($support157==1) {
?>
                <td class="statTable" align="right"> <?php echo number_format($row["sumLogicalReads"])   ?> </td>
                <td class="statTable" align="right"> <?php echo number_format($row["sumPhysicalWrites"]) ?> </td>
                <td class="statTable" align="right"> <?php echo number_format($row["sumAPFReads"])       ?> </td>
                <td class="statTable" align="right"> <?php echo number_format($row["avgAPFPercentage"])  ?> </td>
                <td class="statTable" align="right"> <?php echo number_format($row["avgWashSize"])       ?> </td>
<?php
    }
?>
              </tr> 
        <?php
        }
        ?>
</table>
</DIV>
</DIV>
</DIV>