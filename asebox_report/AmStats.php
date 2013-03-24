<?php

  if ( isset($_POST['orderAmStats']) ) $orderAmStats=$_POST['orderAmStats'];      else $orderAmStats="Thread";
  



    // Check if AseDbSpce table exist
    $query = "select cnt=count(*) 
              from sysobjects 
              where name in ( '".$ServerName."_AmStats ')";   
    $result = sybase_query($query,$pid);
    $row = sybase_fetch_array($result);
    if ($row["cnt"] < 1) {

	echo "Asemon_logger statistics (stats on asemon_logger threads) is not available. The AmStats collector has not been activated for server ".$ServerName.".<P> (Add AmStats.xml in the asemon_logger config file)";
        exit();
        
    }

$query = "select Thread,
nbCollect=count(*),
avgcWait=avg(1.*cWait),
avgcActive=avg(1.*cActive),
avgaWait=avg(1.*aWait),
avgaActive=avg(1.*aActive),
archRows=case when min(archRows)=-1 then -1 else avg(1.*archRows) end

from ".$ServerName."_AmStats
where Timestamp >='".$StartTimestamp."'        
and Timestamp <'".$EndTimestamp."'        
group by Thread
order by ".$orderAmStats
;

  $query_name = "AmStats";

?>

<p></p>
<script type="text/javascript">
setStatMainTableSize(0);
</script>

<div class="boxinmain" style="min-width:500px">
<div class="boxtop">
<img src="images/boxtop-corner-left.jpg" style="float:left;margin:0px"/>
<div style="float:left; position: relative; top: 3px;"><?php include './export/export-table.php' ?></div>
<div class="title" style="width:70%"><?php echo  $Title ?></div>
<img src="images/boxtop-corner-right.jpg" style="float:right;margin:0px;"/>
<a   href="http://sourceforge.net/apps/mediawiki/asemon?title=AseRep_AmStats" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="AmStats help" TITLE="AmStats help"  /> </a>
</div>

<div class="boxcontent">


<div class="statMainTable">
        
    <table cellspacing=2 cellpadding=4>

    <tr> 
      <td class="statTabletitle" > Thread </td>
      <td class="statTabletitle" > nbCollect</td>
      <td class="statTabletitle" > avgcWait </td>
      <td class="statTabletitle" > avgcActive </td>
      <td class="statTabletitle" > avgaWait </td>
      <td class="statTabletitle" > avgaActive </td>
      <td class="statTabletitle" > avgArchRows </td>
    </tr>

    <tr>   
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderAmStats"  VALUE="Thread"      <?php if ($orderAmStats=="Thread")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderAmStats"  VALUE="2 DESC"   <?php if ($orderAmStats=="2 DESC")   echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderAmStats"  VALUE="3 DESC"   <?php if ($orderAmStats=="3 DESC")   echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderAmStats"  VALUE="4 DESC"        <?php if ($orderAmStats=="4 DESC")        echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderAmStats"  VALUE="5 DESC"        <?php if ($orderAmStats=="5 DESC")        echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderAmStats"  VALUE="6 DESC"     <?php if ($orderAmStats=="6 DESC")     echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderAmStats"  VALUE="7 DESC"     <?php if ($orderAmStats=="7 DESC")     echo "CHECKED";  ?> > </td>
    </tr>


    <?php

        $result = sybase_query($query,$pid);
        
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
			<tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';" onclick="location.href='<?php echo "#".$row["Thread"]; ?>';">
            <?php
			$cpt=1-$cpt;
?>
    <td class="statTablePtr" > <?php echo $row["Thread"] ?>  </td>
    <td class="statTablePtr" align="right" > <?php echo number_format($row["nbCollect"]) ?>  </td>
    <td class="statTablePtr" align="right" > <?php echo number_format($row["avgcWait"],2) ?>  </td>
    <td class="statTablePtr" align="right" > <?php echo number_format($row["avgcActive"],2) ?>  </td>
    <td class="statTablePtr" align="right" > <?php echo number_format($row["avgaWait"],2) ?>  </td>
    <td class="statTablePtr" align="right" > <?php echo number_format($row["avgaActive"],2) ?>  </td>
    <td class="statTablePtr" align="right" > <?php echo number_format($row["archRows"],2) ?>  </td>
    </tr> 
    <?php
        }
    ?>

    </table>
</DIV>
</DIV>
</DIV>


