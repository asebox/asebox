<?php

    if ( isset($_POST['orderVers'])       ) $orderVers        = $_POST['orderVers'];         else $orderVers         = "IQconnID";
    if ( isset($_POST['rowcnt'])          ) $rowcnt           = $_POST['rowcnt'];            else $rowcnt            = 200;

    include ("IQ/sql/sql_IQVersions_statistics.php");

?>     
       
       
<script type="text/javascript">
var WindowObjectReference; // global variable

function getCnxDetail(ConnCreateTime,IQconnID,StartTimestamp,EndTimestamp)
{
  ARContextJSON = document.inputparam.ARContextJSON.value;
  WindowObjectReference = window.open("IQ/IQCnx_detail.php?ConnCreateTime="+ConnCreateTime+"&IQconnID="+IQconnID+"&StartTimestamp="+StartTimestamp+"&EndTimestamp="+EndTimestamp+"&ARContextJSON="+ARContextJSON+"#top",
    "_blank");
  WindowObjectReference.focus();
}
</script>

<center>



<div class="boxinmain" style="min-width:500px;">
<div class="boxtop">
<img src="images/boxtop-corner-left.jpg" style="float:left;margin:0px"/>
<div style="float:left; position: relative; top: 3px;"><?php include './export/export-table.php' ?></div>
<div class="title" style="width:70%"><?php echo  $Title ?></div>
<img src="images/boxtop-corner-right.jpg" style="float:right;margin:0px;"/>
<a   href="http://sourceforge.net/apps/mediawiki/asemon?title=AseRep_IQVers" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="IQ Versions help" TITLE="IQ Versions help"  /> </a>
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
<td>
	<?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; order by : ".$orderVers; ?>
    <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" ?>
</td>
</tr></table>
</div>



<div class="statMainTable">



<table cellspacing=2 cellpadding=4 >
    <tr class=statTableTitle> 
      <td class="statTabletitle" > Ts            </td>          
      <td class="statTabletitle" > IQconnID            </td>          
      <td class="statTabletitle" > MinKBRelease    </td>           
      <td class="statTabletitle" > MaxKBRelease    </td>           
      <td class="statTabletitle" > Timeblocking_s    </td>          
    </tr>
    


<?php
	$result = sybase_query("set rowcount ".$rowcnt."
                               ".$query."
                               set rowcount 0",
                               $pid);                       
	if ($result==false){ 
		sybase_close($pid); 
		$pid=0;
		include ("../connectArchiveServer.php");	
		echo "<tr><td>Error</td></tr></table>";
		return(0);
	}
	
	
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
			<tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';" Onclick='javascript:getCnxDetail("<?php echo $row["ConnCreateTime"]?>","<?php echo $row["IQconnID"]?>","<?php echo $StartTimestamp?>","<?php echo $EndTimestamp?>" )' >
            <?php
			$cpt=1-$cpt;
?>
    <td class="statTablePtr" >       <?php echo $row["Ts"] ?>   </td> 
    <td class="statTablePtr" >       <?php echo $row["IQconnID"] ?>  </td> 
    <td class="statTablePtr" align="right">       <?php echo number_format($row["MinKBRelease"]) ?> </td> 
    <td class="statTablePtr" align="right">       <?php echo number_format($row["MaxKBRelease"]) ?> </td> 
    <td class="statTablePtr" align="right">       <?php echo number_format($row["Timeblocking_s"]) ?> </td> 
    </tr> 
    <?php
        }
?>
</table>
</DIV>
</DIV>
</DIV>
</center>














