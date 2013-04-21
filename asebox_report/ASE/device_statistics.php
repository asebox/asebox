<?php        
   $filterdevice="";
   
   include './ASE/sql/sql_devices_statistics.php';
   $query_name=$forced_query_name;
?>

<div  style="overflow:visible;min-width:600px" class="boxinmain" >

<div class="boxtop">
<div style="float:left; position: relative; top: 3px; left: 6px"><?php include './export/export-table.php' ?></div>
<div class="title"><?php echo  $Title ?></div>
<!--(Tot. reads = Reads + APFReads)</div>-->
</div>

<div class="boxcontent">

<table width="100%" cellspacing="2" cellpadding="4">
    <tr align="center"> 
      <td class="statTabletitle"> Device    </td>
      <td class="statTabletitle"> Reads     </td>
      <td class="statTabletitle"> APFReads  </td>
      <td class="statTabletitle"> Writes    </td>
      <td class="statTabletitle"> avgserv_ms</td>
      <td class="statTabletitle"> Contention%</td> <!--Device semaphore contention % -->
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
              <td class="statTable" align="left"> <?php echo $row["Device"] ?>                       </td>
              <td class="statTable" align="right"> <?php echo number_format($row["PReads"]) ?>       </td>
              <td class="statTable" align="right"> <?php echo number_format($row["AReads"]) ?>       </td>
              <td class="statTable" align="right"> <?php echo number_format($row["PWrites"]) ?>      </td>
              <td class="statTable" align="right"> <?php echo number_format($row["avgserv_ms"],2) ?> </td>
              <td class="statTable" align="right"> <?php echo number_format($row["DevSemaphoreContentionPCT"]) ?> </td>
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