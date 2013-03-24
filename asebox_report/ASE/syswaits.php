<?php

  if ( isset($_POST['ordersyswaits']) ) $ordersyswaits=$_POST['ordersyswaits'];      else  $ordersyswaits="3 desc";
  

    // Check if SysWaits, WEvInf I, WClassInf tables exist
    $query = "select cnt=count(*) 
              from sysobjects 
              where name in ( '".$ServerName."_SysWaits ', '".$ServerName."_WEvInf', '".$ServerName."_WClassInf')";   
    $result = sybase_query($query,$pid);
    $row = sybase_fetch_array($result);
    if ($row["cnt"] < 3) {

	echo "SysWaits info is not available. The SysWaits collector has not been activated for server ".$ServerName.".<P> (Add SysWaits.xml, WClassInf.xml, WEvInf.xml in the asemon_logger config file)";
        exit();
        
    }

	include './ASE/sql/sql_SysWaits_list.php';
?>

<p></p>

<script type="text/javascript">
setStatMainTableSize(0);
</script>

<div class="boxinmain" >
<div class="boxtop">
<div style="float:left; position: relative; top: 3px; left: 6px"><?php include './export/export-table.php' ?></div>
<div class="title" style="width:65%">SysWaits</div>
<a   href="http://github.com/asebox/asebox?title=AseRep_ASESysWaits" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="SysWaits help" TITLE="SysWaits help"  /> </a>
</div>

<div class="boxcontent">

<div class="statMainTable">

       
        




    <table cellspacing=2 cellpadding=4>

    <tr> 
      <td class="statTabletitle" > ClassDesc       </td>
      <td class="statTabletitle" > EventDesc       </td>
      <td class="statTabletitle" > WaitTime(s)     </td>
      <td class="statTabletitle" > Waits           </td>
      <td class="statTabletitle" > AvgWaitTime(ms) </td>
    </tr>

    <tr>   
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="ordersyswaits"  VALUE="ClassDesc"      <?php if ($ordersyswaits=="ClassDesc")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="ordersyswaits"  VALUE="EventDesc"      <?php if ($ordersyswaits=="EventDesc")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="ordersyswaits"  VALUE="3 desc"      <?php if ($ordersyswaits=="3 desc")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="ordersyswaits"  VALUE="4 desc"      <?php if ($ordersyswaits=="4 desc")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="ordersyswaits"  VALUE="5 desc"      <?php if ($ordersyswaits=="5 desc")      echo "CHECKED";  ?> > </td>
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
			<tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';" >
            <?php
			$cpt=1-$cpt;
?>
    <td class="statTable" align="left" nowrap> <?php echo $row["ClassDesc"] ?> </td>
    <td class="statTable" align="left" nowrap> <?php echo $row["EventDesc"] ?> </td>
    <td class="statTable" align="right"> <?php echo number_format($row["SumWaitTime"]) ?> </td>
    <td class="statTable" align="right"> <?php echo number_format($row["Sumwaits"]) ?> </td>
    <td class="statTable" align="right"> <?php echo number_format($row["AvgWaitTime_ms"],2) ?> </td>
    </tr> 
    <?php
        }
    ?>

    </table>
</DIV>
</DIV>
</DIV>

<p></p>




   <?php  // loop on all events
        $query="select W.WaitEventID,ClassDesc=C.Description,EventDesc=I.Description
from ".$ServerName."_SysWaits W, ".$ServerName."_WEvInf I, ".$ServerName."_WClassInf C
where W.Timestamp >='".$StartTimestamp."'
and W.Timestamp <'".$EndTimestamp."'
and convert(smallint,W.WaitEventID) = I.WaitEventID
and I.WaitClassID=C.WaitClassID
group by W.WaitEventID,C.Description,I.Description
order by sum(1.*WaitTime) desc";


        $result=sybase_query($query, $pid);
        while (($row=sybase_fetch_array($result)))
        {
            $WaitEventID= $row["WaitEventID"];
            $ClassDesc= $row["ClassDesc"];
            $EventDesc= $row["EventDesc"];
            ?>
            <p></p>
            <img src='<?php echo "./ASE/graphSysWaits.php?ARContextJSON=".urlencode($ARContextJSON)."&WaitEventID=".urlencode($WaitEventID)."&ClassDesc=".urlencode($ClassDesc)."&EventDesc=".urlencode($EventDesc); ?> '>
            <p></p>
            <?php
         }
    ?>
