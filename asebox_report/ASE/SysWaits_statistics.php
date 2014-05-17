<?php
// Check if SysWaits, WEvInf I, WClassInf tables exist
$query = "select cnt=count(*) 
          from sysobjects 
          where name in ( '".$ServerName."_SysWaits ', '".$ServerName."_WEvInf', '".$ServerName."_WClassInf')";   
$result = sybase_query($query,$pid);
$row = sybase_fetch_array($result);
if ($row["cnt"] == 3) {
      
$ordersyswaits="SumWaitTime desc";
      include './ASE/sql/sql_SysWaits_list.php';
?>

<div class="boxinmain">

<div class="boxtop">
<div style="float:left; position: relative; top: 3px; left: 6px"><?php include './export/export-table.php' ?></div>
<div class="title">SysWaits (Top 20)</div>
</div>

<div class="boxcontent">




<table width="100%" border="0" cellspacing="1" cellpadding="0">
    <tr align="center"> 
      <td class="statTabletitle"> ClassDesc  </td>
      <td class="statTabletitle"> EventDesc  </td>
      <td class="statTabletitle"> WaitTime(s)  </td>
      <td class="statTabletitle"> Waits   </td>
      <td class="statTabletitle"> AvgWaitTime(ms)   </td>
    </tr>

    <?php
        $result = sybase_query("set rowcount 20".$query." set rowcount 0",$pid) ;
        $rw=0;
        $cpt=0;
        if ($result != FALSE ) {   
          while( $row = sybase_fetch_array($result))
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
                <td class="statTable" align="left" > <?php echo $row["ClassDesc"] ?> </td>
                <td class="statTable" align="left" > <?php echo $row["EventDesc"] ?> </td>
                <td class="statTable" align="right"> <?php echo number_format($row["SumWaitTime"]) ?>      </td>
                <td class="statTable" align="right"> <?php echo number_format($row["Sumwaits"]) ?>         </td>
                <td class="statTable" align="right"> <?php echo number_format($row["AvgWaitTime_ms"],2) ?> </td>
              </tr> 
              <?php
          } // end while
        } // end if $result...
        else {
        ?>
            <tr>
               <td colspan="6" align="left" > <font size="4"  STYLE="font-weight: 900" COLOR="red"> Error for this period   </font> </td>
            </tr>
    
        <?php   
        }
        ?>
</table>

  </DIV>
</DIV>
  <?php
  }  // End test SysWaits exists
  ?>
