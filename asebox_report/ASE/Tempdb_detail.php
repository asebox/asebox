<?php

$query = "select Ts=convert(varchar(10),Timestamp,3)+' '+convert(varchar(5),Timestamp,108)
,PctDatUsed=str(100. - 100.*dbFree_pgs / case when isMixedLog=0 then  Total_pgs-logTotal_pgs else  Total_pgs end ,6,2)
,PctLogUsed=(100.*(logUsed_pgs+logClr_pgs)/(case when isMixedLog=0 then logTotal_pgs else Total_pgs  end)     )
,tempo='abcdefghijk'
from ".$ServerName."_AseDbSpce
where Timestamp >='".$StartTimestamp."'
and   Timestamp <='".$EndTimestamp."'
and   dbname ='tempdb'
order by Timestamp";

$query_name = "tempdb_detail";
?>

<div  class="boxinmain">

<div class="boxtop">
<div style="float:left; position: relative; top: 3px; left: 6px"><?php include $rootDir.'/export/export-table.php' ?></div>
<div class="title">Tempdb</div>
</div>

<div class="boxcontent">
<div style="overflow-y:visible"  class="statMainTable">

<table width="100%" border="0" cellspacing="1" cellpadding="0">
    <tr align="center"> 
      <td class="statTabletitle" align="left"> Ts  </td>
      <td class="statTabletitle"> PctDatUsed </td>
      <td class="statTabletitle"> PctLogUsed </td>
      <td class="statTabletitle"> tempo </td>
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
                <td class="statTable" NOWRAP>        <?php echo $row["Ts"]                            ?> </td>
                <td class="statTable" align="right"> <?php echo number_format($row["PctDatUsed"]    ) ?> </td>
                <td class="statTable" align="right"> <?php echo number_format($row["PctLogUsed"]    ) ?> </td>
                <td class="statTable" NOWRAP>        <?php echo $row["tempo"]                            ?> </td>
              </tr> 
        <?php
        }
        ?>
</table>
</DIV>
</DIV>
</DIV>