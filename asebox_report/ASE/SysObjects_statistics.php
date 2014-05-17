<?php
$param_list=array(
	'orderPrc',
	'rowcnt',
	'filterDbName',
	'filterTabName',
	'filterObjType',
	'filterObjName',
	'filterCrDate'
);
foreach ($param_list as $param)
   @$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];

   if ( !isset($orderPrc     ) ) $orderObj=$order_by;
   if ( !isset($rowcnt       ) ) $rowcnt=200;
   if ( !isset($filterDbName ) ) $filterDbName  ="";
   if ( !isset($filterObjType) ) $filterObjType ="";
   if ( !isset($filterObjName) ) $filterObjName ="";
   if ( !isset($filterCrDate ) ) $filterCrDate  ="";

   include ("sql/sql_sysobj_statistics.php");
?>

<script type="text/javascript">
setStatMainTableSize(0);
</script>
        
<center>
        
<div class="boxinmain" style="min-width:500px">
<div class="boxtop">
<div style="float:left; position: relative; top: 3px; left: 6px"><?php include './export/export-table.php' ?></div>
<div class="title"><?php echo $Title ?></div>
<a   href="http://github.com/asebox/asebox/ASE-SysObjects" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="Object list help" TITLE="Lock waits help"  /> </a>
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
      <td class="statTabletitle" > Database    </td>
      <td class="statTabletitle" > Type        </td>
      <td class="statTabletitle" > Name        </td>
      <td class="statTabletitle" > Create Date </td>
    </tr>
    
    <tr class=statTableTitle> 
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterDbName"  SIZE="12"  value="<?php if(isset($filterDbName  ) ) { echo $filterDbName  ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterObjType" SIZE="3"  value="<?php if(isset($filterObjType ) ) { echo $filterObjType ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterObjName" SIZE="20" value="<?php if(isset($filterObjName ) ) { echo $filterObjName ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterCrDate"  SIZE="15" value="<?php if(isset($filterCrDate  ) ) { echo $filterCrDate  ; } ?>" > </td>
    </tr>
    
    <tr class=statTableTitle>  
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderObj"  VALUE="dbname,type,name"      <?php if ($orderObj=="dbname,type,name") echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderObj"  VALUE="type,dbname,name"      <?php if ($orderObj=="type,dbname,name") echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderObj"  VALUE="name,type,dbname"      <?php if ($orderObj=="name,type,dbname") echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderObj"  VALUE="crdate desc,type,name" <?php if ($orderObj=="crdate desc,type,name") echo "CHECKED";  ?> > </td>
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
    <td nowrap class="statTable" > <?php echo $row["dbname"]  ?> </td>
    <td nowrap class="statTable" > <?php echo $row["type"] ?> </td>
    <td nowrap class="statTable" > <?php echo $row["name"] ?> </td>
    <td nowrap class="statTable" > <?php echo $row["crdate"]  ?> </td>
    </tr> 
    <?php
          } // end while
	} // end if $result...
	if ($rw == 0 )  {
    ?>
    <tr>
       <td colspan="19" align="center" > <font STYLE="font-weight: 900"> No results   </font> </td>
    </tr>
    <?php
        } // end if $result
    ?>
    

</table>
</DIV>
</DIV>
</DIV>


</center>
