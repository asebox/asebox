<script type="text/javascript">
var WindowObjectReference; // global variable

setStatMainTableSize(0);

function getDeviceDetail(Device)
{
  ARContextJSON = document.inputparam.ARContextJSON.value;
  WindowObjectReference = window.open("./ASE/device_detail.php?Device="+urlencode(Device)+"&ARContextJSON="+ARContextJSON+"#top", "_blank");
  WindowObjectReference.focus();
}
</script>

<?php

  if ( isset($_POST['order_by']) ) $order_by=$_POST['order_by'];      else $order_by="Device";
  if ( isset($_POST['filterdevice'            ]) ) $filterdevice=            $_POST['filterdevice'];             else $filterdevice="";
  



    // Check if DevIO table exist
    $query = "select cnt=count(*) 
              from sysobjects 
              where name in ( '".$ServerName."_DevIO ')";   
    $result = sybase_query($query,$pid);
    $row = sybase_fetch_array($result);
    if ($row["cnt"] < 1) {

        echo "Device info is not available. The DevIO collector has not been activated for server ".$ServerName.".<P> (Add DevIO.xml or DevIO_V15 in the asemon_logger config file)";
        exit();
        
    }

    include ("./ASE/sql/sql_devices_statistics.php");
?>

<p></p>

<div class="boxinmain" style="min-width:550px">
<div class="boxtop">
<div style="float:left; position: relative; top: 3px; left: 6px"><?php include './export/export-table.php' ?></div>
<div class="title" style="width:65%"><?php echo  $Title ?></div>
<a   href="http://github.com/asebox/asebox/ASE-Devices-Summary" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="Device help" TITLE="Device help"  /> </a>
</div>

<div class="boxcontent">

<div class="boxbtns" >
<table align="left" cellspacing="2px" ><tr>
<td>
	<img src="images/button_sideLt.gif"  class="btn" height="20px" >
    <INPUT style="height:20px; " class="btn" type="submit" value="Refresh" name="RefreshStmt" >
    <img src="images/button_sideRt.gif"  class="btn" height="20px">
</td>
<td>
	<img src="images/button_sideLt.gif"  class="btn" height="20px" >
    <INPUT style="height:20px; " class="btn" type="button" value="Graph all" name="Graph all" onclick="javascript:getDeviceDetail('<?php if( isset($filterdevice) ) if ($filterdevice=="") echo "%"; else echo $filterdevice ; ?>');">
    <img src="images/button_sideRt.gif"  class="btn" height="20px">
</td>
</tr></table>
</div>



<div class="statMainTable">

    <table cellspacing=2 cellpadding=4>

    <tr> 
      <td class="statTabletitle" > Device      </td>
      <td class="statTabletitle" > PReads      </td>
      <td class="statTabletitle" > AReads      </td>
      <td class="statTabletitle" > PWrites     </td>
      <td class="statTabletitle" > avgserv_ms  </td>
      <td class="statTabletitle" > DevSemaphoreContentionPCT </td>
    </tr>

    <tr>   
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="order_by"  VALUE="Device"                         <?php if ($order_by=="Device")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="order_by"  VALUE="PReads DESC"                    <?php if ($order_by=="PReads DESC")   echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="order_by"  VALUE="AReads DESC"                    <?php if ($order_by=="AReads DESC")   echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="order_by"  VALUE="PWrites DESC"                   <?php if ($order_by=="PWrites DESC")        echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="order_by"  VALUE="avgserv_ms DESC"                <?php if ($order_by=="avgserv_ms DESC")        echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="order_by"  VALUE="DevSemaphoreContentionPCT DESC" <?php if ($order_by=="DevSemaphoreContentionPCT DESC")     echo "CHECKED";  ?> > </td>
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
        <td class="statTablePtr" > <?php echo $row["Device"] ?>  </td>
        <td class="statTablePtr" align="right" > <?php echo number_format($row["PReads"]) ?>  </td>
        <td class="statTablePtr" align="right" > <?php echo number_format($row["AReads"]) ?>  </td>
        <td class="statTablePtr" align="right" > <?php echo number_format($row["PWrites"]) ?>  </td>
        <td class="statTablePtr" align="right" > <?php echo number_format($row["avgserv_ms"],2) ?>  </td>
        <td class="statTablePtr" align="right" > <?php echo number_format($row["DevSemaphoreContentionPCT"],2) ?>  </td>
        </tr> 
    <?php
    }
    ?>

</table>
</DIV>
</DIV>
</DIV>

