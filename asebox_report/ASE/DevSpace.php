<?php
  if ( isset($_POST['order_by']    ) ) $order_by    =$_POST['order_by'];      else $order_by="Device";
  if ( isset($_POST['filterdevice']) ) $filterdevice=$_POST['filterdevice'];  else $filterdevice="";
?>

<script type="text/javascript">
var WindowObjectReference; // global variable
setStatMainTableSize(0);
</script>

<?php
//----------------------------------------------------------------------------------------------------
// Check table exists
$query = "select cnt=count(*) 
          from sysobjects 
          where name in ( '".$ServerName."_SptValues ')";   
$result = sybase_query($query,$pid);
$row = sybase_fetch_array($result);
if ($row["cnt"] < 1) {

    echo "<br>Device space information is not available. The proxy access to tables sysdevices and sysusages has not been activated for server ".$ServerName.".";
    exit();
    
}

include ("./ASE/sql/sql_DevSpace.php");
?>

<p></p>

<div class="boxinmain" style="min-width:550px">
<div class="boxtop">
<div style="float:left; position: relative; top: 3px; left: 6px"><?php include './export/export-table.php' ?></div>
<div class="title" style="width:65%"><?php echo  $Title ?></div>
<a   href="http://github.com/asebox/asebox/ASE-Devices-Space" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="Device help" TITLE="Device help"  /> </a>
</div>

<div class="boxcontent">

<div class="boxbtns" >
<table align="left" cellspacing="2px" ><tr>
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
      <td class="statTabletitle" > Device   </td>
      <td class="statTabletitle" > vdevno   </td>
      <td class="statTabletitle" > defdsk   </td>
      <td class="statTabletitle" > Total    </td>
      <td class="statTabletitle" > Used     </td>
      <td class="statTabletitle" > Free     </td>
      <td class="statTabletitle" > Location </td>
    </tr>

    <tr>   
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="order_by"  VALUE="Device"    <?php if ($order_by=="Device")    echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="order_by"  VALUE="vdevno"    <?php if ($order_by=="vdevno")    echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="order_by"  VALUE="defdsk"    <?php if ($order_by=="defdsk")    echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="order_by"  VALUE="Total"     <?php if ($order_by=="Total")     echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="order_by"  VALUE="Used"      <?php if ($order_by=="Used")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="order_by"  VALUE="Free DESc" <?php if ($order_by=="Free DESC") echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="order_by"  VALUE="Location"  <?php if ($order_by=="Location")  echo "CHECKED";  ?> > </td>
    </tr>

    <tr> 
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterdevice"  value="<?php if( isset($filterdevice) ){ echo $filterdevice ; } ?>" > </td>
      <td></td> 
      <td></td> 
      <td></td> 
      <td></td> 
      <td></td> 
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
			  <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';" onclick='javascript:getDeviceDetail("<?php echo $row["Device"]?>" )'>
        <?php
			  $cpt=1-$cpt;
        ?>
        <td class="statTablePtr"              > <?php echo $row["Device"]                 ?> </td>
        <td class="statTablePtr" align="right"> <?php echo number_format($row["vdevno"])  ?> </td>
        <td class="statTablePtr" align="center"> <?php echo $row["defdsk"]                 ?> </td>
        <td class="statTablePtr" align="right"> <?php echo number_format($row["Total"],2) ?> </td>
        <td class="statTablePtr" align="right"> <?php echo number_format($row["Used"], 2) ?> </td>
        <td class="statTablePtr" align="right"> <?php echo number_format($row["Free"], 2) ?> </td>
        <td class="statTablePtr"              > <?php echo $row["Location"]               ?> </td>
        </tr> 
    <?php
    }
    ?>

</table>
</DIV>
</DIV>
</DIV>

