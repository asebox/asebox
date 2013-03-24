<?php

    if ( isset($_POST['orderqueues'              ]) ) $orderqueues=              $_POST['orderqueues'];               else $orderqueues="1";
?>

<script type="text/javascript">
var WindowObjectReference; // global variable


setStatMainTableSize(0);

function getQueuesDetail(Info)
{
  ARContextJSON = document.inputparam.ARContextJSON.value;
  WindowObjectReference = window.open("RS15/queue_detail.php?Info="+Info+"&ARContextJSON="+ARContextJSON+"#top",
    "_blank"
    );
  WindowObjectReference.focus();
}
</script>


<?php
  $Title="Stable queues usage";
  // Check if table xxxx_RSWhoSQM exists
  $query = "select id from sysobjects where name ='".$ServerName."_RSWhoSQM'";
  $result = sybase_query($query,$pid);
  $rw=0;
  while($row = sybase_fetch_array($result))
  {
    $rw++;
  }	
  if ($rw == 0)   // Check if xxxx_RSWhoSQM exists
  {
	      echo "The RSWhoSQM collector has not been activated for server ".$ServerName.".<P> (Add  RSWhoSQM.xml in the asemon_logger config file)";
	      exit();
  }
?>
<center>




  <?php
	include './RS15/sql/rsqueues_statistics.php';
  ?>
  

<div class="boxinmain" style="min-width:500px">
<div class="boxtop">
<img src="images/boxtop-corner-left.jpg" style="float:left;margin:0px"/>
<div style="float:left; position: relative; top: 3px;"><?php include './export/export-table.php' ?></div>
<div class="title"><?php echo  $Title ?></div>
<img src="images/boxtop-corner-right.jpg" style="float:right;margin:0px;"/>
<a href="http://sourceforge.net/apps/mediawiki/asemon?title=AseRep_rsqueuesStats" TARGET="_blank"> <img SRC="images/Help-circle-blue-32.png" ALT="RSQueues help" TITLE="RSQueues help"  width="32" height="32" border="0"> </a>
</div>

<div class="boxcontent">


<div class="statMainTable">
    <table cellspacing=2 cellpadding=4>
    <tr> 
      <td class="statTabletitle" > Queue          </td>
      <td class="statTabletitle" > AVG_active_queue_sz_Mb          </td>
      <td class="statTabletitle" > MAX_active_queue_sz_Mb          </td>
      <td class="statTabletitle" > LAST_active_queue_sz_Mb          </td>
      <td class="statTabletitle" > sav_int_mn   </td>
      <td class="statTabletitle" > AVG_saved_queue_sz_Mb          </td>
      <td class="statTabletitle" > MAX_saved_queue_sz_Mb          </td>
      <td class="statTabletitle" > LAST_saved_queue_sz_Mb          </td>
    </tr>

    <tr>  
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderqueues"  VALUE="1" <?php if ($orderqueues=="1") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderqueues"  VALUE="2 desc" <?php if ($orderqueues=="2 desc") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderqueues"  VALUE="3 desc" <?php if ($orderqueues=="3 desc") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderqueues"  VALUE="4 desc" <?php if ($orderqueues=="4 desc") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderqueues"  VALUE="5 desc" <?php if ($orderqueues=="5 desc") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderqueues"  VALUE="6 desc" <?php if ($orderqueues=="6 desc") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderqueues"  VALUE="7 desc" <?php if ($orderqueues=="7 desc") echo "CHECKED"; ?> > </td>

	  
	</tr>


    <?php

	$result = sybase_query($query, $pid);
	//echo $query;
	if ($result==false){ 
		sybase_close($pid); 
		$pid=0;
		include ("connectArchiveServer.php");	
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
        <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';" Onclick='javascript:getQueuesDetail("<?php echo urlencode($row["Info"])?>" )' >
        <?php
		$cpt=1-$cpt;
		?>
            <td class="statTablePtr" nowrap>               <?php echo $row["Info"] ?>  </td>
			<td class="statTablePtr" align="right" > <?php echo number_format($row["AVG_active_queue_sz_Mb"],2) ?>  </td>
			<td class="statTablePtr" align="right" > <?php echo number_format($row["MAX_active_queue_sz_Mb"],2) ?>  </td>
			<td class="statTablePtr" align="right" > <?php echo number_format($row["LAST_active_queue_sz_Mb"],2) ?>  </td>
			<td class="statTablePtr" align="right" > <?php echo $row["sav_int_mn"] ?>  </td>
			<td class="statTablePtr" align="right" > <?php echo number_format($row["AVG_saved_queue_sz_Mb"],2) ?>  </td>
			<td class="statTablePtr" align="right" > <?php echo number_format($row["MAX_saved_queue_sz_Mb"],2) ?>  </td>
			<td class="statTablePtr" align="right" > <?php echo number_format($row["LAST_saved_queue_sz_Mb"],2) ?>  </td>
        </tr> 
        <?php
    } // while($row = sybase_fetch_array($result))
    ?>
    </table>
</DIV>
</DIV>
</DIV>

