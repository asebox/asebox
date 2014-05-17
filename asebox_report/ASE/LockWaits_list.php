<?php
$param_list=array(
	'orderPrc',
	'rowcnt',
	'filterDbName',
	'filterTabName'
);
foreach ($param_list as $param)
	@$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];

       if ( !isset($orderPrc) ) $orderPrc=$order_by;
       if ( !isset($rowcnt) ) $rowcnt=200;
       if ( !isset($filterDbName   ) ) $filterDbName      ="";
       if ( !isset($filterTabName  ) ) $filterTabName      ="";

       include ("sql/sql_LockWaits_list.php");
?>              
        
<script type="text/javascript">
setStatMainTableSize(0);
</script>
        
<center>
        
<div class="boxinmain" style="min-width:800px">
<div class="boxtop">
<div style="float:left; position: relative; top: 3px; left: 6px"><?php include './export/export-table.php' ?></div>
<div class="title"><?php echo  $Title ?></div>
<a   href="http://github.com/asebox/asebox/ASE-LockWaits" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="Lock waits help" TITLE="Lock waits help"  /> </a>
</div>

<div class="boxcontent">

<div class="boxbtns" >
<table align="left" cellspacing="2px" ><tr>
<td>Max rows (0 = unlimited) :</td>
<td>
	<input type="text" name="rowcnt" value="<?php if( isset($rowcnt) ){ echo $rowcnt ; } ?>">
</td>
<td>
	<img src="images/button_sideLt.gif"  class="btn" height="20px" >
    <INPUT style="height:20px; " class="btn" type="submit" value="Refresh" name="RefreshStmt" >
    <img src="images/button_sideRt.gif"  class="btn" height="20px">
</td>
</tr></table>
</div>

<div class="statMainTable">
<table cellspacing=2 cellpadding=4>

    <tr> 
      <td class="statTabletitle" > Database      </td>
      <td class="statTabletitle" > Table         </td>
      <td class="statTabletitle" > LockScheme    </td>
      <td class="statTabletitle" > Pagetype      </td>
      <td class="statTabletitle" > StatName      </td>
      <td class="statTabletitle" > WaitTime_ms   </td>
      <td class="statTabletitle" > Waits         </td>
      <td class="statTabletitle" > AvgWaitTime_ms</td>
    </tr>
    
    <tr class=statTableTitle> 
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterDbName"  value="<?php if(isset($filterDbName  ) ) { echo $filterDbName     ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterTabName" value="<?php if(isset($filterTabName ) ) { echo $filterTabName     ; } ?>" > </td>
    </tr>
    
    
    <?php
	$result = sybase_query($query,$pid) ;
	$rw=0;
	$cpt=1;
        if ($result != FALSE ) {   
          while( $row = sybase_fetch_array($result))
          {
			$rw++;
			if($cpt==0)
			     $parite="impair";
			else
			     $parite="pair";
			?>
			<tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';"  >
			<?php
			$cpt=1-$cpt;
    ?>
      <td nowrap class="statTable" > <?php echo $row["DbName"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["TabName"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["LockScheme"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["Pagetype"] ?> </td>
      <td nowrap class="statTable" > <?php echo $row["StatName"] ?> </td>
      <td nowrap class="statTable" > <?php echo number_format($row["WaitTime_ms"]) ?> </td>
      <td nowrap class="statTable" > <?php echo number_format($row["sumWaits"]) ?> </td>
      <td nowrap class="statTable" > <?php echo number_format($row["AvgWaitTime_ms"]) ?> </td>
     </tr> 
    <?php
          } // end while
	} // end if $result...
	if ($rw == 0 )  {
    ?>
    <tr>
       <td colspan="19" align="center" > <font STYLE="font-weight: 900"> No lock   </font> </td>
    </tr>
    <?php
        } // end if $result
    ?>
    

</table>
</DIV>
</DIV>
</DIV>


</center>
